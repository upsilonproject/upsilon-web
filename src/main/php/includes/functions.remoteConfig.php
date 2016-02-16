<?php

function getConfigById($configId) {
	$sql = 'SELECT r.id, r.name, r.mtime FROM remote_configs r WHERE r.id = :id';
	$stmt = stmt($sql);
	$stmt->bindValue(':id', $configId);
	$stmt->execute();
	$config = $stmt->fetchRowNotNull();
	$config['mtime'] = date(DATE_RFC2822, strtotime($config['mtime']));

	return $config;
}

function getConfigNodes($config) {
	$sql = 'SELECT a.id, n.id AS nodeId, n.identifier, n.lastUpdated FROM remote_config_allocated_nodes a LEFT JOIN nodes n ON a.node = n.identifier WHERE a.config = :config';
	$stmt = db()->prepare($sql);
	$stmt->bindValue(':config', $config);
	$stmt->execute();

	$nodes = $stmt->fetchAll();

	$nodes = addStatusToNodes($nodes);

	return $nodes;
}

function getConfigServices($config) {
	$sql = 'SELECT a.id, s.id AS serviceId, s.name, c.identifier AS commandIdentifier, s.parent, cm.icon FROM remote_config_allocated_services a LEFT JOIN remote_config_services s ON a.service = s.id LEFT JOIN remote_config_commands c ON s.command = c.id LEFT JOIN command_metadata cm ON c.metadata = cm.id LEFT JOIN remote_configs rc ON a.config = rc.id WHERE rc.id = :config';
	$stmt = stmt($sql);
	$stmt->bindValue(':config', $config);
	$stmt->execute();
	$services = $stmt->fetchAll();

	return $services;
}

function getConfigCommandsUsedByServices() {
	return array();
}

function getConfigCommands($config) {
	$sql = 'SELECT a.id, c.identifier, c.command_line, cm.icon FROM remote_config_allocated_commands a LEFT JOIN remote_config_commands c ON a.command = c.id LEFT JOIN command_metadata cm ON c.metadata = cm.id LEFT JOIN remote_configs rc ON a.config = rc.id WHERE rc.id = :config';
	$stmt = stmt($sql);
	$stmt->bindValue(':config', $config);
	$stmt->execute();
	$commands = $stmt->fetchAll();

	return $commands;
}

function addArgValuesToServices(array $services) {
	$ret = array();

	foreach ($services as $k => $service) {
		$args = getServiceArgumentValues($service['serviceId']);
		
		$service['arguments'] = $args;
		$ret[$k] = $service;
	}

	return $ret;
}

function setParentIfBlank(array &$services) {
	foreach ($services as &$service) {
		if (empty($service['parent'])) {
			$service['parent'] = 'base_service';
		}
	}
}

function generateConfigXmlFromConfig($config) {
	global $tpl;

	$services = getConfigServices($config['id']);
	$services = addArgValuesToServices($services);
	setParentIfBlank($services);

	$tpl->assign('listServices', $services);
	$tpl->assign('listCommandsUsed', getConfigCommandsUsedByServices($config['id']));
	$tpl->assign('listCommandsUnused', getConfigCommands($config['id']));

	$tpl->assign('comment', '');
	$tpl->assign('mtime', $config['mtime']);

	return $tpl->fetch('config.xml');
}

function generateConfigFromId($configId) {
	$config = getConfigById($configId);

	return generateConfigXmlFromConfig($config);
}

function generateConfigFromNodeIdentifier($identifier) {
	$sql = 'SELECT an.config AS id FROM remote_config_allocated_nodes an WHERE an.node = :identifier';
	$stmt = db()->prepare($sql);
	$stmt->bindValue(':identifier', $identifier);
	$stmt->execute();

	$config = $stmt->fetchRowNotNull();

	return generateConfigFromId($config['id']);
}

function getCommandArguments($id) {
	$sql = 'SELECT a.id, a.name, a.datatype FROM remote_config_command_arguments a WHERE a.command = :commandId';
	$stmt = db()->prepare($sql);
	$stmt->bindValue(':commandId', $id);
	$stmt->execute();

	return $stmt->fetchAll();
}

function deleteConfigAllocatedNode($id) {
	$sql = 'SELECT a.config FROM remote_config_allocated_nodes a WHERE a.id = :id';
	$stmt = db()->prepare($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();

	$config = $stmt->fetchRowNotNull();
	$config = $config['config'];

	$sql = 'DELETE FROM remote_config_allocated_nodes WHERE id = :id';
	$stmt = db()->prepare($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();

	return $config;
}

function getNodesUsingRemoteService($serviceId) {
	$sql = 'SELECT n.id, n.identifier FROM remote_config_allocated_services ass LEFT JOIN remote_configs c ON ass.config = c.id LEFT JOIN remote_config_allocated_nodes an ON an.config = c.id LEFT JOIN nodes n ON an.node = n.identifier WHERE ass.service = :serviceId';
	$stmt = stmt($sql);
	$stmt->bindValue(':serviceId', $serviceId);
	$stmt->execute();

	return $stmt->fetchAll();
}

?>
