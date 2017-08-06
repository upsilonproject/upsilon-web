<?php

$title = 'View chart';
require_once 'includes/widgets/header.php';

foreach ($_REQUEST['serviceIds'] as $serviceId) {
	$service = getServiceById($serviceId);

	$results = getServiceResults($service['identifier'], $service['node'], 7);
	$results = array_reverse($results);

	$metadata = getServiceMetadata($service['identifier']);
}

$tpl->assign('legend', true);
$tpl->assign('instanceChartIndex', 0);
$tpl->assign('listServiceId', $_REQUEST['serviceIds']);
$tpl->assign('metric', 'karma');
$tpl->assign('yAxisMarkings', array());
$tpl->assign('metadata', array('metrics' => $metadata['metrics']));
$tpl->assign('itemService', $service);
$tpl->display('widgetChartMetric.tpl');

require_once 'includes/widgets/footer.php';

?>
