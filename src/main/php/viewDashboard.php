<?php

require_once 'includes/common.php';
require_once 'includes/classes/Dashboard.php';

use \libAllure\HtmlLinksCollection;
use \libAllure\DatabaseFactory;

$id = san()->filterUint('id');

$links = new HtmlLinksCollection('Dashboard &nabla;');

if (getSiteSetting('enableDroneConfig')) {
    $links->add('createRemoteConfigService.php?', 'Monitor new service...');
    $links->addSeparator();
}

$links->add('createWidgetInstance.php?dashboard=' . $id, 'Add Widget');
$links->add('updateDashboard.php?id=' . $id, 'Update this dashboard');
$links->add('deleteDashboard.php?id=' . $id, 'Delete this dashboard');

$itemDashboard = new Dashboard($id); 

setNav(array('listDashboards.php' => 'Dashboards'), $itemDashboard->getTitle());
require_once 'includes/widgets/header.php';

$tpl->assign('itemDashboard', $itemDashboard);
$tpl->assign('listInstances', $itemDashboard->getWidgetInstances());
$tpl->assign('hiddenWidgets', $itemDashboard->getHiddenWidgetInstances());
$tpl->assign('sessionOptions', sessionOptions());
$tpl->display('dashboard.tpl');

require_once 'includes/widgets/footer.php';

?>
