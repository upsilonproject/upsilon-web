<?php

$title = 'Create remote config service';
require_once 'includes/common.php';

$config = san()->filterId();

$sql = 'INSERT INTO remote_config_allocated_services (config) VALUES (:config) ';
$stmt = stmt($sql);
$stmt->bindValue(':config', $config);
$stmt->execute();

redirect('updateRemoteConfigurationServiceInstance.php?id=' . insertId(), 'Editing...');

?>
