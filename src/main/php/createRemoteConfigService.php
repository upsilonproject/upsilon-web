<?php

$title = 'Create remote config service';
require_once 'includes/common.php';

$sql = 'INSERT INTO remote_config_services () VALUES () ';
$stmt = stmt($sql);
$stmt->execute();

$serviceId = insertId();

if (isset($_REQUEST['config'])) {

	$sql = 'INSERT INTO remote_config_allocated_services (config, service) VALUES (:config, :service)  ';
	$stmt = stmt($sql);
	$stmt->bindValue(':config', san()->filterUint('config'));
	$stmt->bindValue(':service', $serviceId);
	$stmt->execute();
}

redirect('updateRemoteConfigurationService.php?id=' . $serviceId, 'Editing...');

?>
