<?php

$title = 'View service result graph';
require_once 'includes/common.php';

use \libAllure\DatabaseFactory;
use \libAllure\Sanitizer;

function getServiceResults($identifier) {
	$sql = 'SELECT r.id, r.karma, r.checked AS date, r.output FROM service_check_results r LEFT JOIN services s ON r.service = s.identifier WHERE s.id = :id ORDER BY r.checked DESC LIMIT 30';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	
	$stmt->bindValue(':id', $identifier);
	$stmt->execute();

	$results = $stmt->fetchAll();

	return $results;
}

function extractNagiosMetric($service, $field) {
	$listMetrics = explode(",", $service['output']);

	$match = preg_match_all('#([\|\w]+)=([\d\.]+)#i', $service['output'], $matches, PREG_SET_ORDER);

	$metric = new stdClass;
	$metric->date = $service['date'];
	$metric->karma = $service['karma'];
	$metric->value = '[NO OUTPUT]';

	foreach ($matches as $match) {
			if ($match[1] == $field) {
				$metric->value = $match[2];
			}
	}

	return $metric;
}

function karmaToInt($karma) {
	switch ($karma) {
		case 'BAD': return -1;
		case 'STALLED': return 0;
		case 'GOOD': return 1;
		case 'WARNING': return -.5;
		case 'UNKNOWN': return 0;
	}
}

$field = Sanitizer::getInstance()->filterString('metric');

if (empty($field)) {
	$field = 'karma';
}

$metrics = array();

foreach ($_REQUEST['services'] as $service) {
	$results = getServiceResults($service);
	$results = array_reverse($results);

	$metrics[] = array(
		'serviceId' => $service,
		'metrics' => getServiceMetrics($results, $field)
	);
}

header('Content-Type: application/json');
echo json_encode(array(
	'graphIndex' => $_REQUEST['graphIndex'],
	'metric' => $field,
	'services' => $metrics
));

exit;

$g = new Graph();
$g->drawAxis(false, true);

$g->plotMetrics($metrics, $field);
$g->output();

?>
