<?php

$title = 'Create remote config service';
require_once 'includes/common.php';

$sql = 'INSERT INTO remote_config_commands () VALUES () ';
$stmt = stmt($sql);
$stmt->execute();

redirect('updateRemoteConfigurationCommand.php?id=' . insertId(), 'Editing...');

?>
