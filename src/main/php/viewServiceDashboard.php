<?php

require_once 'includes/common.php';

setNav(array('listDashboards.php' => 'Dashboards'), 'Service Dashboard');
require_once 'includes/widgets/header.php';


use \libAllure\DatabaseFactory;

$tpl->assign('listUngroupedServices', getServicesUngrouped());
$tpl->display('ungroupedServices.tpl');

$tpl->display('index.tpl');

foreach (getGroups() as $itemGroup) {
	$tpl->assign('itemGroup', $itemGroup);
	$tpl->assign('hidden', false);
	//$tpl->display('group.tpl');
}

require_once 'includes/widgets/footer.php';

?>
