<?php

require_once 'jsonCommon.php';

$serviceId = $_REQUEST['serviceIds'][0];
$service = getServiceById($serviceId, true);
##$results = getServiceResultsMostRecent($service['identifier'], $service['node']);

$ret = array(
	'chartIndex' => $_REQUEST['chartIndex'],
	'services' => array($service)
);

outputJson($ret);
?>
