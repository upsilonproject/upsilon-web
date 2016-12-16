<?php

$title = 'Update catalog';
require_once 'includes/widgets/header.php';

$commandsCatalogDir = "https://raw.githubusercontent.com/upsilonproject/upsilon-catalog/master/";
$catalog = file_get_contents($commandsCatalogDir . 'commands.json');
$catalog = json_decode($catalog);

echo 'Updating commands catalog from: ' . $commandsCatalogDir . '<br />';

$sql = 'INSERT INTO remote_config_commands (command_line, identifier, metadata) VALUES (:commandLine, :identifier, :metadata) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id), metadata = metadata';
$stmtCommand = stmt($sql);

$sql = 'INSERT INTO remote_config_command_arguments (command, name) VALUES (:command, :arg) ON DUPLICATE KEY UPDATE id = id';
$stmtArgs = stmt($sql);

$sql = 'INSERT INTO command_metadata (commandIdentifier, icon) values (:command, :icon) ON DUPLICATE KEY UPDATE icon = :iconUpdate';
$stmtMetadata = stmt($sql);

echo 'Number of commands: ' . sizeof($catalog->catalog) . '<br />';

$count = 0;

foreach ($catalog->catalog as $command) {
	if (isset($command->icon)) {
		$stmtMetadata->bindValue(':icon', $command->icon);
		$stmtMetadata->bindValue(':iconUpdate', $command->icon);
		echo 'Setting icon for ' . $command->identifier . ' to ' . $command->icon . '<br />';

		if (!file_exists('resources/images/serviceIcons/' . $command->icon)) {
			$icon = file_get_contents($commandsCatalogDir . 'icons/' . $command->icon);
			file_put_contents('resources/images/serviceIcons/' . $command->icon, $icon);
		}
	} else {
		echo 'Default icon for ' . $command->identifier . '<br />';
		$stmtMetadata->bindValue(':icon', '00defaultIcon.png');
		$stmtMetadata->bindValue(':iconUpdate', '00defaultIcon.png');
	}

	var_dump($stmtMetadata->debugDumpParams());
	$stmtMetadata->bindValue(':command', 'uc_' . $command->identifier);
	$stmtMetadata->execute();
	

	$metadataId = \libAllure\DatabaseFactory::getInstance()->lastInsertId();

	$stmtCommand->bindValue(':commandLine', $command->command_line);
	$stmtCommand->bindValue(':identifier', 'uc_' . $command->identifier);
	$stmtCommand->bindValue(':metadata', $metadataId);
//	$stmtCommand->bindValue(':metadataUpdate', $metadataId);
	$stmtCommand->execute();

	$commandId = \libAllure\DatabaseFactory::getInstance()->lastInsertId();

	$matches = array();
	preg_match_all('/(\$[\w]+)/i', $command->command_line, $matches);

	foreach ($matches[0] as $arg) {
		$stmtArgs->bindValue(':command', $commandId);
		$stmtArgs->bindValue(':arg', $arg);
		$stmtArgs->execute();
	}
}
?>
<a href = "listCommandDefinitions.php">Command Definitions</a><br /><br />
<a href = "index.php">Return to index</a>

