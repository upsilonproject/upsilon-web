<?php

$title = 'Create remote config service';
require_once 'includes/common.php';

$config = san()->filterId();

$sql = 'INSERT INTO remote_config_allocated_commands (config) VALUES (:config) ';
$stmt = stmt($sql);
$stmt->bindValue(':config', $config);
$stmt->execute();

redirect('updateRemoteConfigurationCommandInstance.php?id=' . insertId(), 'Editing...');

?>
