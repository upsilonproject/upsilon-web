<?php

require_once 'includes/common.php';

$title = 'Dashboards';

$links = linksCollection();
$links->add('createDashboard.php', 'Create new dashboard');
$links->add('installWidgets.php', 'Install Widgets');

require_once 'includes/widgets/header.php';

$tpl->assign('listDashboards', getDashboards());
$tpl->display('listDashboards.tpl');

$sql = 'SELECT w.id, w.class, count(wi.id) AS instances FROM widgets w LEFT JOIN widget_instances wi ON w.id = wi.widget GROUP BY w.id';
$stmt = stmt($sql);
$stmt->execute();

$tpl->assign('listWidgets', $stmt->fetchAll());
$tpl->display('listWidgets.tpl');

require_once 'includes/widgets/footer.php';

?>
