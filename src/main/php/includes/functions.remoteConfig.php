<?php

require_once 'includes/classes/Amqp.php';

function getClickableCommandLine($configSource) {
	$line = $configSource['remote_config_command_line'];
	$args = getServiceArgumentValues($configSource['remote_configuration_service_id']);

	foreach ($args as $name => $value) {
		if ($value == '') {
			$value = '<span class = "bad">(empty)</span>';
		}

		$line = str_replace('$' . $name, '<a href = "updateRemoteConfigurationService.php?id=' . $configSource['remote_configuration_service_id'] . '"><abbr class = "commandArg" title = "' . $name . '">' . $value . '</abbr></a>', $line);
	}

	$line = preg_replace('#\$(\w+)#i', '<a href = "updateRemoteConfigurationService.php?id=' . $configSource['remote_configuration_service_id'] . '">$<strong class = "bad">\1</strong></a>', $line);

	return $line;

}

function deleteRemoteConfigurationCommand($id) {
	$sql = 'DELETE FROM remote_config_commands WHERE id = :id';
	$stmt = stmt($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
}

function getConfigSourceFromServiceResultIdentifier($serviceIdentifier, $nodeIdentifier) {
	$sql = 'SELECT rc.id AS remote_config_id, rc.name AS remote_config_name, rcs.id AS remote_configuration_service_id, ras.id AS remote_config_allocated_service_id, rcc.id AS remote_config_command_id, rcc.identifier AS remote_config_command_name, rcc.command_line AS remote_config_command_line FROM services s LEFT JOIN remote_config_services rcs ON rcs.name = s.identifier LEFT JOIN remote_config_commands rcc ON rcs.command = rcc.id LEFT JOIN remote_config_allocated_services ras ON ras.service = rcs.id LEFT JOIN remote_configs rc ON rc.id = ras.config WHERE s.identifier = :serviceIdentifier AND s.node = :nodeIdentifier';
	$stmt = stmt($sql);
	$stmt->bindValue(':serviceIdentifier', $serviceIdentifier);
	$stmt->bindValue(':nodeIdentifier', $nodeIdentifier);
	$stmt->execute();

	$configs = $stmt->fetchAll();

	if ($configs[0]['remote_config_id'] == null) {
		return 'local';
	} else {
		return $configs[0];
	}
}

function updateConfig($configId, $description = null) {
	$sql = 'UPDATE remote_configs r SET r.mtime = utc_timestamp() WHERE r.id = :id';
	$stmt = stmt($sql);
	$stmt->bindValue(':id', $configId);
	$stmt->execute();

	if (empty($description)) {
		$description = 'Config updated.';
	}

	logger($description, array('nodeConfigId' => $configId));
}

function getConfigById($configId) {
	$sql = 'SELECT r.id, r.autoSendOnUpdate, r.name, r.mtime, unix_timestamp(r.mtime) AS modifiedTimestamp FROM remote_configs r WHERE r.id = :id';
	$stmt = stmt($sql);
	$stmt->bindValue(':id', $configId);
	$stmt->execute();
	$config = $stmt->fetchRowNotNull();
	$config['mtime'] = date(DATE_RFC2822, strtotime($config['mtime']));

	return $config;
}

function getConfigNodes($configId) {
	$config = getConfigById($configId);

	$sql = 'SELECT a.id, n.id AS nodeId, n.identifier, n.lastUpdated, n.configs FROM remote_config_allocated_nodes a LEFT JOIN nodes n ON a.node = n.identifier WHERE a.config = :config';
	$stmt = db()->prepare($sql);
	$stmt->bindValue(':config', $configId);
	$stmt->execute();

	$nodes = $stmt->fetchAll();

	$nodes = addStatusToNodes($nodes);

	foreach ($nodes as $index => $node) {
			$reportedConfigs = parseReportedConfigs($node['configs']);

			$nodes[$index]['reportVersion'] = null;

			if (isset($reportedConfigs[$configId])) {
				$nodes[$index]['reported'] = $reportedConfigs[$configId];
				$nodes[$index]['reportStatus'] = 'REPORTED';
				$nodes[$index]['reportVersion'] = date(DATE_RFC2822, $reportedConfigs[$configId]['updated'] / 1000);

				if (strtotime($config['mtime']) > strtotime($reportedConfigs[$configId]['updated'])) {
					$nodes[$index]['reportKarma'] = 'OLD';
				} else {
					$nodes[$index]['reportKarma'] = 'GOOD';
				}
			} else {
				$nodes[$index]['reported'] = null;
				$nodes[$index]['reportStatus'] = 'NOT REPORTED';
				$nodes[$index]['reportKarma'] = 'BAD';
			}
	}

	return $nodes;
}

function getConfigServices($config) {
	$sql = 'SELECT a.id, s.id AS serviceId, s.name, c.id AS commandId, c.identifier AS commandIdentifier, s.parent, cm.icon FROM remote_config_allocated_services a LEFT JOIN remote_config_services s ON a.service = s.id LEFT JOIN remote_config_commands c ON s.command = c.id LEFT JOIN command_metadata cm ON c.metadata = cm.id LEFT JOIN remote_configs rc ON a.config = rc.id WHERE rc.id = :config';
	$stmt = stmt($sql);
	$stmt->bindValue(':config', $config);
	$stmt->execute();
	$services = $stmt->fetchAll();

	return $services;
}

function getConfigCommandsUsedByServices($configId) {
	$sql = 'SELECT DISTINCT rcc.identifier, rcc.command_line FROM remote_configs rc LEFT JOIN remote_config_allocated_services als ON als.config = rc.id LEFT JOIN remote_config_services rcs ON als.service = rcs.id LEFT JOIN remote_config_commands rcc ON rcs.command = rcc.id WHERE rc.id = :configId';
	$stmt = stmt($sql);
	$stmt->bindValue(':configId', $configId);
	$stmt->execute();

	return $stmt->fetchAll();
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
	$sql = 'SELECT n.id, n.identifier, c.id AS configId, c.name AS configName, s.identifier AS reportedServiceIdentifier, s.id AS reportedServiceId, s.karma AS reportedServiceKarma FROM remote_config_allocated_services ass RIGHT JOIN remote_configs c ON ass.config = c.id RIGHT JOIN remote_config_allocated_nodes an ON an.config = c.id RIGHT JOIN nodes n ON an.node = n.identifier RIGHT JOIN remote_config_services rcs ON ass.service = rcs.id RIGHT JOIN services s ON s.node = n.identifier AND s.identifier = rcs.name WHERE ass.service = :serviceId';
	$stmt = stmt($sql);
	$stmt->bindValue(':serviceId', $serviceId);
	$stmt->execute();

	return $stmt->fetchAll();
}

function getNodesUsingConfig($configId) {
	$sql = 'SELECT n.id, n.identifier FROM remote_config_allocated_nodes aln LEFT JOIN nodes n ON aln.node = n.identifier WHERE aln.config = :configId ';
	$stmt = stmt($sql);
	$stmt->bindValue(':configId', $configId);
	$stmt->execute();

	return $stmt->fetchAll();
}

function deleteRemoteConfigurationCommandInstance($commandInstanceId) {
	$sql = 'SELECT alc.config FROM remote_config_allocated_commands alc WHERE alc.id = :commandInstance';
	$stmt = stmt($sql);
	$stmt->bindValue(':commandInstance', $commandInstanceId);
	$stmt->execute();

	$config = $stmt->fetchRowNotNull();
	$config = $config['config'];

	$sql = 'DELETE FROM remote_config_allocated_commands WHERE id = :commandInstance';
	$stmt = stmt($sql);
	$stmt->bindValue(':commandInstance', $commandInstanceId);
	$stmt->execute();

	return $config;
}

function sendUpdatedConfig($configId, $nodeIdentifier) {
	$config = generateConfigFromId($configId);

	$msg = new UpsilonMessage('UPDATED_NODE_CONFIG', $config);
	$msg->addHeader('node-identifier', $nodeIdentifier);
	$msg->addHeader('remote-config-id', $configId);
	$msg->addHeader('remote-config-source-identifier', getSiteSetting('configSourceIdentifier') . '-' . $configId);
	$msg->publish();

	logger('Sending _nodeConfigId_ to ' . $nodeIdentifier, array('nodeConfigId' => $configId));
}


?>
