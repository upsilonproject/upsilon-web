<?php

$title = 'List of Services';
require_once 'includes/widgets/header.php';

use \libAllure\DatabaseFactory;
use \libAllure\FilterTracker;

$filters = new FilterTracker();
$filters->addBool('problems', 'Problems');
$filters->addBool('ungrouped');
$filters->addInt('maintPeriod', 'Maintenance Period');
$filters->addString('name');
$filters->addSelect('node', getNodes(), 'identifier');

$qb = new \libAllure\QueryBuilder();
$qb->from('services')->fields('id', 'identifier', 'output', 'description', 'lastUpdated', 'karma', 'secondsRemaining', 'node');

if ($filters->isUsed('problems')) {
	$qb->whereNotEquals('karma', 'good');
}

if ($filters->isUsed('ungrouped'))  {
	$qbGroupMemberships = new \libAllure\QueryBuilder();
	$qbGroupMemberships->from('service_group_memberships', 'g')->fields('service');

	$qb->whereSubquery('s.identifier', 'NOT IN', $qbGroupMemberships);
} 

if ($filters->isUsed('maintPeriod')) {
	$id = san()->filterUint('maintPeriod');

	$qb->leftJoin('service_metadata', 'm')->on('s.identifier', 'm.service');
	$qb->whereEquals('m.acceptableDowntimeSla', $id);

	$activeFilters[] = 'Maint Period';
}

if ($filters->isUsed('name')) {
	$qb->where('identifier', 'LIKE', '"%' . $filters->getValue('name') . '%"');
}

if ($filters->isUsed('node')) {
	$qb->whereEquals('node', $filters->getValue('node'));
}

$stmt = DatabaseFactory::getInstance()->prepare($qb->build());
$stmt->execute();
$listServices = $stmt->fetchAll();

$tpl->assign('filters', $filters->getAll());
$tpl->assign('listServices', $listServices);
$tpl->display('listServices.tpl');

require_once 'includes/widgets/footer.php';

?>
