<?php

require_once 'jsonCommon.php';

use \libAllure\DatabaseFactory;
use \libAllure\QueryBuilder;

$filters = instanceCoverageFilter();

$qb = new QueryBuilder();
$qb->from('services')->fields('s.id', 's.identifier', 's.node');

if ($filters->isUsed('node')) {
	$qb->whereEquals('s.node', $filters->getValue('node'));
}

if ($filters->isUsed('identifier')) {
	$qb->where('s.identifier', 'LIKE', '"%' . $filters->getValue('identifier') . '%"');
}

$qb->orderBy('s.identifier ASC');

$stmt = DatabaseFactory::getInstance()->prepare($qb->build());
$stmt->execute();

$ret = [];
foreach ($stmt->fetchAll() as $itemService) {
	$ret[] = [ 
		'identifier' => $itemService['identifier'],
		'id' => $itemService['id'],
		'node' => $itemService['node']
	];
}

$sql = 'SELECT a.service FROM class_service_assignments a WHERE instance = :instance AND requirement = :requirement';
$stmt = DatabaseFactory::getInstance()->prepare($sql);
$stmt->bindValue(':instance', san()->filterUint('instance'));
$stmt->bindValue(':requirement', san()->filterUint('requirement'));
$stmt->execute();

outputJson($ret);
