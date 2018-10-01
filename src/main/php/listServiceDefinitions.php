<?php

require_once 'includes/common.php';

$links = linksCollection();
$links->add('createRemoteConfigService.php?', 'Create Service');

$title = 'Services';
require_once 'includes/widgets/header.php';

$filters = new \libAllure\FilterTracker();
$filters->addString('identifier', 'Identifier');
$filters->addString('command', 'Command');

$qb = new \libAllure\QueryBuilder();
$qb->from('remote_config_services', 's');
$qb->fields('s.id', 's.name', 's.parent', array('c.id', 'commandId'), array('c.identifier', 'commandIdentifier'), array('count(a.id)', 'instanceCount'), 'm.icon');
$qb->join('remote_config_commands', 'c')->on('s.command', 'c.id');
$qb->join('command_metadata', 'm')->on('c.metadata', 'm.id');
$qb->join('remote_config_allocated_services', 'a')->on('a.service', 's.id');
$qb->groupBy('s.id');

if ($filters->isUsed('identifier')) {
	$qb->where('s.name', 'LIKE', '"%' . $filters->getValue('identifier') . '%"');
}

if ($filters->isUsed('command')) {
	$qb->where('c.identifier', 'LIKE', '"%' . $filters->getValue('command') . '%"');
}

$stmt = stmt($qb->build())->execute();

$tpl->assign('listServices', $stmt->fetchAll());
$tpl->assign('filters', $filters->getAll());
$tpl->display('listServiceDefinitions.tpl');

require_once 'includes/widgets/footer.php';

?>
