<?php

use \libAllure\DatabaseFactory;
use \libAllure\Session;
use \libAllure\Sanitizer;
use \libAllure\HtmlLinksCollection;
use \libAllure\FilterTracker;

require_once 'includes/classes/SessionOptions.php';

$nav = array();

function setNav() {
	global $nav;

	foreach (func_get_args() as $arg) {
		$nav[] = $arg;
	}
}

function loggerFields() {
	return array('userId', 'usergroupId', 'serviceResultId', 'nodeId', 'nodeConfigId', 'serviceDefinitionId', 'commandDefinitionId', 'classId', 'dashboardId', 'serviceGroupId');
}

function logger($message, $keys = array()) {
	$sql = 'INSERT INTO logs (message, timestamp, userId, usergroupId, serviceResultId, nodeId, nodeConfigId, serviceDefinitionId, commandDefinitionId, classId, dashboardId, serviceGroupId) VALUES (:message, utc_timestamp(), :userId, :usergroupId, :serviceResultId, :nodeId, :nodeConfigId, :serviceDefinitionId, :commandDefinitionId, :classId, :dashboardId, :serviceGroupId)';
	$stmt = stmt($sql);
	$stmt->bindValue(':message', $message);
	
	foreach (loggerFields() as $arg) {
		if (isset($keys[$arg])) {
			$stmt->bindValue($arg, $keys[$arg]);
		} else {
			$stmt->bindValue($arg, null);
		}
	}

	$stmt->execute();
}

function isUsingSsl() {
	if (!isset($_SERVER['HTTPS'])) {
		$_SERVER['HTTPS'] = 'off';
	}

	return $_SERVER['HTTPS'] == 'on';
}

function explodeOrEmpty($delimiter = null, $serialString = "") {
	$serialString = trim($serialString);

	if (strlen($serialString) == 0) {
		return array();
	} else {
		return explode($delimiter, $serialString);
	}
}

function setSiteSetting($key, $val) {
        global $settings;

        $sql = 'INSERT INTO settings (`key`, `value`) VALUES (:key, :valueInsert) ON DUPLICATE KEY UPDATE value = :valueUpdate';
        $stmt = DatabaseFactory::getInstance()->prepare($sql);
        $stmt->bindValue(':key', $key);
        $stmt->bindValue(':valueInsert', $val);
        $stmt->bindValue(':valueUpdate', $val);
        $stmt->execute();
}


function getSiteSetting($key, $default = '') {
        global $settings;

		try {
			if (empty($settings)) {
					$sql = 'SELECT s.`key`, s.value FROM settings s';
					$stmt = DatabaseFactory::getInstance()->prepare($sql);
					$stmt->execute();

					foreach ($stmt->fetchAll() as $row) {
							$settings[$row['key']] = $row['value'];
					}
			}


			if (!isset($settings[$key])) {
					return $default;
			} else {
					return $settings[$key];
			}
		} catch (Exception $e) {
			return $default;
		}
}

function connectDatabase() {
        try {
                $db = new \libAllure\Database(CFG_DB_DSN, CFG_DB_USER, CFG_DB_PASS);
                \libAllure\DatabaseFactory::registerInstance($db);
        } catch (Exception $e) {
                throw new Exception('Could not connect to database. Check the username, password, host, port and database name.<br />' . $e->getMessage(), null, $e);
        }

        try {
                $maint = getSiteSetting('maintenanceMode', 'NONE');
        } catch (Exception $e) {
                if ($e->getCode() == '42S02') {
                        throw new Exception('Settings table not found. Did you import the table schema?', null, $e);
                } else {
                        throw new Exception('Unhandled SQL error while getting settings table: ' . $e->getMessage(), null, $e);
                }
        }

        if ($maint === 'NONE') {
                throw new Exception('Essential setting "maintenanceMode" does not exist in the DB. Did you import the initial data?');
        }

        return $db;
}

function insertId() {
	return DatabaseFactory::getInstance()->lastInsertId();
}

function stmt($sql) {
	return DatabaseFactory::getInstance()->prepare($sql);
}
 
function san() {
	return Sanitizer::getInstance();
}

function db() { 
	return DatabaseFactory::getInstance();
}

function linksCollection($title = null) {
	return new HtmlLinksCollection($title);
}

function redirect($url) {
	header('Location: ' . $url);

	exit;
}

function plural($num, $short = false, $longForm = null) {
	$shortForm = substr($longForm, 0, 1);

	if ($short) {
		return $shortForm;
	} else {
		if ($num != 1) {
			$longForm .= 's';
		}

		return ' ' . $longForm . ' ago';
	}
}
	 
function getRelativeTime($date, $short = false, $fromDate = null) {
	if ($fromDate == null) {
		$fromDate = time();
	}

	return getRelativeTimeSecondsRectified($fromDate - strtotime($date), $short);
}

function getRelativeTimeSecondsRectified($diff, $short = false) {
	$rectified = false;

	if ($diff < 0) {
		$diff = abs($diff);
		$rectified = true;
	}

	$res = getRelativeTimeSeconds($diff, $short);

	if ($rectified) {
		return '+'.$res;
	} else {
		return '-'.$res;
	}
}

function getRelativeTimeSeconds($diff, $short = false) {
	if ($diff<60) {
		return $diff . plural($diff, $short, 'second');
	}

	$diff = round($diff/60);

	if ($diff<60) {
		return $diff . plural($diff, $short, 'minute');
	}

	$diff = round($diff/60);
	
	if ($diff<24) {
		return $diff . plural($diff, $short, 'hour');
	}

	$diff = round($diff/24);

	if ($diff<7) {
		return $diff . plural($diff, $short, 'day');
	}
	
	$diff = round($diff/7);

	if ($diff<4) {
		return $diff . plural($diff, $short, 'week');
	}

	return '???';
}

function getNodes() {
	$sql = 'SELECT n.id, n.identifier, n.serviceType, n.lastUpdated, count(s.id) AS serviceCount, n.serviceType AS nodeType, n.instanceApplicationVersion FROM nodes n LEFT JOIN services s ON s.node = n.identifier GROUP BY n.id';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->execute();

	$nodes = $stmt->fetchAll();

	$nodes = addStatusToNodes($nodes);

	return $nodes;
}

function findLatestNodeVersion($nodes) {
	$latestTimestamp = 0;
	$latestValue = '';

	if (!isset($nodes[0]['instanceApplicationVersion'])) {
		return;
	}

	foreach ($nodes as $node) {
		$matches = array();

		preg_match_all('/(?<major>[\d]+)\.(?<minor>[\d]+)\.(?<revision>[\d]+)\-\d+\-(?<timestamp>[\d]+)/i', $node['instanceApplicationVersion'], $matches);

		if (isset($matches['timestamp'])) {
			$timestamp = intval(current($matches['timestamp']));

			if ($timestamp > $latestTimestamp) {
				$latestTimestamp = $timestamp;
				$latestValue = $node['instanceApplicationVersion'];
			}
		}
	}

	return $latestValue;
}

function isOld($timestamp) {
	$diff = usertime() - strtotime($timestamp);

	return $diff > intval(Session::getUser()->getData('oldServiceThreshold'));
}

function addStatusToNodes($nodes) {
	$latestVersion = findLatestNodeVersion($nodes);

	foreach ($nodes as &$itemNode) {
		if (isset($itemNode['instanceApplicationVersion'])) {
			if ($itemNode['instanceApplicationVersion'] == $latestVersion) {
				$itemNode['versionKarma'] = 'GOOD';
			} else {
				$itemNode['versionKarma'] = 'OLD';
			}
		} else {
			$itemNode['versionKarma'] = 'UNKNOWN';
		}
	}

	return $nodes;
}

function isJsonSubResultsValid($results) {
	foreach ($results as $result) {
		if (!is_array($result)) {
			return false;
		}
	}

	return true;
}

function parseOutputJson(&$service) {
	$pat = '#<json>(.+)</json>#ims';

	$matches = array();
	$res = preg_match($pat, $service['output'], $matches);


	if ($res) {
		$ret = preg_replace($pat, null, $service['output']);

		//$service['output'] = $service['output']; 
		$json = json_decode($matches[1], true);

		if (!empty($json['subresults']) && isJsonSubResultsValid($json['subresults'])) {
			$service['listSubresults'] = $json['subresults'];
				
			foreach ($service['listSubresults'] as $key => $result) {
				if (!isset($result['karma']) || $service['karma'] == 'OLD') {
					$service['listSubresults'][$key]['karma'] = $service['karma'];
				}

				$service['listSubresults'][$key]['karma'] = strtolower($service['listSubresults'][$key]['karma']);

				// name
				if (isset($result['name'])) {
					$service['listSubresults'][$key]['name'] = san()->escapeStringForHtml($result['name']);
					continue;
				}	

				if (isset($result['subject'])) {
					$service['listSubresults'][$key]['name'] = san()->escapeStringForHtml($result['subject']);
					continue;
				}

				if (isset($result['title'])) {
					$service['listSubresults'][$key]['name'] = san()->escapeStringForHtml($result['title']);
				}
			}
		}

		if (isset($json['metrics'])) {
			$service['listMetrics'] = $json['metrics'];
		}

		if (isset($json['tasks'])) {
			$service['tasks'] = $json['tasks'];
		}

		if (isset($json['events'])) {
			$service['events'] = $json['events'];
		}

		if (isset($json['news'])) {
			$service['news'] = $json['news'];
		}

		$service['stabilityProbibility'] = rand(1, 100);
	}

}

function parseMetadata(&$service) {
	if (empty($service['metaActions'])) {
		return;
	}

	foreach (explode("\n", $service['metaActions']) as $line) {
		$comps = explode("=", $line, 2);
		
		if (count($comps) > 0) {
			$link = new stdClass;
			$link->url = $comps[1];
			$link->title = $comps[0];

			$service['listActions'][] = $link;
		}
	
	}
}

$now = time();

function invalidateOldServices(&$service) {
	global $now;
	
	$diff = $now - strtotime($service['lastUpdated']);

	if ($diff > intval(Session::getUser()->getData('oldServiceThreshold'))) {
		$service['karma'] = 'OLD';
		$service['output'] = "WARNING: This result of this service check was older than the user's preference threshold." . $service['output'];
	}
}

function parseAcceptableDowntime(&$service) {
	if (!empty($service['acceptableDowntime'])) {
		$downtime = explode("\n", trim($service['acceptableDowntime']));

		$dt = getFailedDowntimeRule($downtime);

		if ($dt != false && $service['karma'] != 'GOOD') {
			$service['karma'] = 'SKIPPED';
			$service['output'] = '[DT:' . $dt . '] ' . $service['output'];
		}
	}
}

function getFailedDowntimeRule(array $downtime) {
	foreach ($downtime as $rule) {
		$literals = explode(' ', trim($rule));

		if (sizeof($literals) != 3) {
			continue;
		} else {
			$field = $literals[0];
			$operator = $literals[1];
			$value = $literals[2];

			if (is_numeric($value)) {
				$value = intval($value);
			}

			switch ($field) {
				case 'day':
					$lval = strtolower(date('D'));
					break;
				case 'hour':
					$lval = intval(date('G'));
					break;
				case 'week':
					$lval = intval(date('W'));
					break;
				default:
					continue;
			}

			switch ($operator) {
				case '>':
				case '>=':
				case '<':
				case '<=':
				case '==':
				case '!':
					$res = null;

					$expr = "\$res = '$lval' $operator '$value';";
					eval($expr);

					if ($res) {
						return $rule . '(' . $lval . ')';
					}

					break;
			}
		}
	}

	return false;
}

function getFilterServices() {
	$filters = new FilterTracker();
	$filters->addBool('problems', 'Problems');
	$filters->addBool('ungrouped');
	$filters->addBool('ungrouped');
	$filters->addBool('ungrouped');
	$filters->addInt('maintPeriod', 'Maintenance Period');
	$filters->addString('name');
	$filters->addSelect('node', getNodes(), 'identifier');

	return $filters;
}

function getServicesBad() {
	$filters = getFilterServices();
	
	$_REQUEST['problems'] = true;

	$problemServices = getServicesWithFilter(null, $filters);

	foreach ($problemServices as $key => $service) {
		if ($service['karma'] == 'OLD') {
			unset($problemServices[$key]);
		}
	}

	$problemServices = array_values($problemServices);

	return $problemServices;
}

function getServicesWithFilter($groupId = null, $filters = null) {
	if ($filters == null) {
		$filters = getFilterServices();
	}

	$qb = new \libAllure\QueryBuilder();
	$qb->from('services')->fields('id', 'identifier', 'identifier alias', 'commandLine executable', 'estimatedNextCheck', 'lastChanged', 'output', 'description', 'lastUpdated', 'karma', 'secondsRemaining', 'node');

	if ($filters->isUsed('problems')) {
		$qb->whereNotEquals('karma', 'good');
	}

	if ($filters->isUsed('ungrouped'))  {
		$qbGroupMemberships = new \libAllure\QueryBuilder();
		$qbGroupMemberships->from('service_group_memberships', 'g')->fields('service');

		$qb->whereSubquery('s.identifier', 'NOT IN', $qbGroupMemberships);
	} 

	if ($filters->isUsed('maintPeriod')) {
		$id = san()->filterUint('maintPeriod');

		$qb->leftJoin('service_metadata', 'm')->on('s.identifier', 'm.service');
		$qb->whereEquals('m.acceptableDowntimeSla', $id);

		$activeFilters[] = 'Maint Period';
	}

	if ($filters->isUsed('name')) {
		$qb->where('identifier', 'LIKE', '"%' . $filters->getValue('name') . '%"');
	}

	if ($filters->isUsed('node')) {
		$qb->whereEquals('node', $filters->getValue('node'));
	}

	$qb->leftJoin('remote_config_allocated_nodes', 'rn')->on('s.node', 'rn.node');
	$qb->leftJoin('remote_config_allocated_services', 'ras')->on('ras.config', 'rn.config');
	$qb->leftJoin('remote_config_services', 'rs')->on('ras.service', 'rs.id')->on('rs.name', 'identifier');
	$qb->leftJoin('remote_configs', 'rc')->on('rn.config', 'rc.id')->onImpl(null, null, 'not(isnull(rs.id))');
	$qb->fields(array('rc.id', 'remote_config_id'));
	$qb->fields(array('rs.id', 'remote_config_service_id'));
	$qb->fields(array('rs.name', 'remote_config_service_identifier'));
	$qb->fields(array('rc.name', 'remote_config_name'));
	$qb->groupBy('s.id');

	$stmt = DatabaseFactory::getInstance()->prepare($qb->build());
	$stmt->execute();
	$listServices = $stmt->fetchAll();

	$listServices = enrichServices($listServices);

	return $listServices;
}

function getServices($groupId = null) {
	if ($groupId == null) {
		$sqlSubservices = 'SELECT DISTINCT m.id membershipId, md.actions AS metaActions, IF(md.icon IS null, cmd.icon, md.icon) AS icon, IF(md.alias IS null, s.identifier, md.alias) AS alias, IF(md.acceptableDowntimeSla IS NULL, md.acceptableDowntime, sla.content) AS acceptableDowntime, s.id, s.lastUpdated, s.lastChanged, s.description, s.commandLine, s.output, s.karma, s.secondsRemaining, s.executable, s.consecutiveCount, s.node, s.estimatedNextCheck FROM service_group_memberships m RIGHT JOIN services s ON m.service = s.identifier LEFT JOIN service_groups g ON m.`group` = g.title LEFT JOIN command_metadata cmd ON s.commandIdentifier = cmd.commandIdentifier LEFT JOIN service_metadata md ON md.service = s.identifier LEFT JOIN acceptable_downtime_sla sla ON md.acceptableDowntimeSla = sla.id ORDER BY s.identifier';
		$stmt = DatabaseFactory::getInstance()->prepare($sqlSubservices);
		$stmt->execute();

	} else {
		$sqlSubservices = 'SELECT DISTINCT m.id membershipId, md.actions AS metaActions, IF(md.icon IS null, cmd.icon, md.icon) AS icon, IF(md.alias IS null, s.identifier, md.alias) AS alias, IF(md.acceptableDowntimeSla IS NULL, md.acceptableDowntime, sla.content) AS acceptableDowntime, s.id, s.lastUpdated, s.lastChanged, s.description, s.commandLine, s.output, s.karma, s.secondsRemaining, s.executable, s.consecutiveCount, s.node, s.estimatedNextCheck FROM service_group_memberships m RIGHT JOIN services s ON m.service = s.identifier LEFT JOIN service_groups g ON m.`group` = g.title LEFT JOIN command_metadata cmd ON s.commandIdentifier = cmd.commandIdentifier LEFT JOIN service_metadata md ON md.service = s.identifier LEFT JOIN acceptable_downtime_sla sla ON md.acceptableDowntimeSla = sla.id WHERE g.id = :groupId ORDER BY s.identifier';
		$stmt = DatabaseFactory::getInstance()->prepare($sqlSubservices);
		$stmt->bindValue(':groupId', $groupId);
		$stmt->execute();

	}

	$listServices = $stmt->fetchAll();
	$listServices = enrichServices($listServices);

	return $listServices;
}

function castService(&$service) {
	echo 'Warning: Service Cast';
	var_dump($service['castServiceCritical']);
}

function enrichService($service, $parseOutput = true, $parseMetadata = true, $invalidateOldServices = true, $parseAcceptableDowntime = true, $castServices = false) {
	$services = enrichServices(array($service), $parseOutput, $parseMetadata, $invalidateOldServices, $parseAcceptableDowntime, $castServices);

	return $services[0];
}

function enrichServices($listServices, $parseOutput = true, $parseMetadata = true, $invalidateOldServices = true, $parseAcceptableDowntime = true, $castServices = false) {
	foreach ($listServices as $k => $itemService) {
		$listServices[$k]['stabilityProbibility'] = 0;
		$listServices[$k]['executableShort'] = str_replace(array('.pl', '.py', 'check_'), null, basename($listServices[$k]['executable']));
		$listServices[$k]['isOverdue'] = (time() - strtotime($itemService['estimatedNextCheck'])) > 0;
		$listServices[$k]['estimatedNextCheckRelative'] = getRelativeTime($itemService['estimatedNextCheck'], true);
		$listServices[$k]['lastChangedRelative'] = getRelativeTime($itemService['lastChanged'], true);
		$listServices[$k]['listSubresults'] = array();
		$listServices[$k]['listActions'] = array();

		$parseAcceptableDowntime && parseAcceptableDowntime($listServices[$k]);
		$invalidateOldServices && invalidateOldServices($listServices[$k]);
		$parseOutput && parseOutputJson($listServices[$k]);
		$parseMetadata && parseMetadata($listServices[$k]);
		$castServices && castService($listServices[$k]);

		$listServices[$k]['output'] = htmlspecialchars($listServices[$k]['output']);
	}

	return $listServices;
}

function array2dFetchKey($array, $key) {
	$ret = array();
	foreach ($array as $item) {
		if (is_array($item) && isset($item[$key])) {
			$ret[] = $item[$key];
		}
	}

	return $ret;
}

function enrichGroupListingsWithServiceMemberships($listGroups, $subGroupDepth = 1) {
	foreach ($listGroups as &$itemGroup) {
		$itemGroup['listServices'] = getServices($itemGroup['id']);

		if ($subGroupDepth > 0) {
				$sql = 'SELECT g.* FROM service_groups g WHERE g.parent = :name';
				$stmt = DatabaseFactory::getInstance()->prepare($sql);
				$stmt->bindValue(':name', $itemGroup['name']);
				$stmt->execute();

				$itemGroup['listSubgroups'] = array();

				foreach ($stmt->fetchAll() as $itemSubgroup) {
					$itemSubgroup['listServices'] = getServices($itemSubgroup['id']);

					$itemGroup['listSubgroups'][] = $itemSubgroup;
				}
		}
	}

	return $listGroups;
}

function getClassInstancesInGroup($gid) {
	$sql = 'SELECT ci.id, ci.title FROM class_instance_group_memberships gm LEFT JOIN class_instances ci ON gm.class_instance = ci.id WHERE gm.gid = :gid';
	$stmt = stmt($sql);
	$stmt->bindValue(':gid', $gid);
	$stmt->execute();

	$ret = $stmt->fetchAll();

	foreach ($ret as &$ci) {
		$ci['requirements'] = getInstanceRequirements($ci['id']);
	}

	return $ret;
}

function enrichGroupListingsWithClassInstanceMemberships($listGroups, $subGroupDepth = 1) {
	foreach ($listGroups as &$itemGroup) {
		$itemGroup['listClassInstances'] = getClassInstancesInGroup($itemGroup['id']);
	}

	return $listGroups;
}


function enrichGroupListingsWithNodeMemberships($listGroups) {
	foreach ($listGroups as &$itemGroup) {
		$itemGroup['listNodes'] = array();
	}

	return $listGroups;
}

function getGroups($includeServices = true, $includeNodes = true, $includeClassInstances = true) {
	$sql = 'SELECT g.id, g.title AS name, g.description, p.id AS parentId, p.title AS parentName, count(m.id) AS serviceCount, count(n.id) AS nodeCount FROM service_groups g LEFT JOIN service_group_memberships m ON g.title = m.group LEFT JOIN service_groups p ON g.parent = p.title LEFT JOIN node_group_memberships mn ON g.id = mn.gid LEFT JOIN nodes n ON mn.node = n.id GROUP BY g.id ORDER BY g.title ASC';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->execute();

	$listGroups = $stmt->fetchAll();
	
	if ($includeServices) {
		$listGroups = enrichGroupListingsWithServiceMemberships($listGroups);
	}

	if ($includeNodes) {
		$listGroups = enrichGroupListingsWithNodeMemberships($listGroups);
	}

	if ($includeClassInstances) {
		$listGroups = enrichGroupListingsWithClassInstanceMemberships($listGroups);
	}

	return $listGroups;
}

function getGroup($id) {
	$sql = 'SELECT g.* FROM service_groups g WHERE g.id = :id LIMIT 1';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();

	$itemGroup = enrichGroupListingsWithServiceMemberships(array($stmt->fetchRowNotNull()));
	$itemGroup = enrichGroupListingsWithNodeMemberships(array($itemGroup[0]));
	$itemGroup = enrichGroupListingsWithClassInstanceMemberships(array($itemGroup[0]));

	return $itemGroup[0];
}

function handleApiLogin() {
	if (isset($_REQUEST['login'])) {
		$sql = 'SELECT u.id, u.username, a.* FROM apiClients a LEFT JOIN users u ON a.user = u.id WHERE a.identifier = :identifier LIMIT 1';
		$stmt = DatabaseFactory::getInstance()->prepare($sql);
		$stmt->bindValue(':identifier', $_REQUEST['login']);
		$stmt->execute();


		if ($stmt->numRows() > 0) {
			$apiClient = $stmt->fetchRow();
			$username = $apiClient['username'];

			$user = \libAllure\User::getUser($username);
			$_SESSION['user'] = $user;
			$_SESSION['username'] = $username;

			sessionOptions()->drawHeaders = $apiClient['drawHeader'];
			sessionOptions()->drawNavigation = $apiClient['drawNavigation'];
			sessionOptions()->drawBigClock = $apiClient['drawBigClock'];

			$_SESSION['apiClient'] = $apiClient['identifier'];
			$_SESSION['apiClientRedirect'] = $apiClient['redirect'];


			redirectApiClients();
		}
	}  
}

function redirectApiClients() {
	if (isset($_SESSION['apiClientRedirect'])) {
			if (stripos($_SESSION['apiClientRedirect'], 'dashboard') !== false) {
				$dashboard = explode(':', $_SESSION['apiClientRedirect']);
	
				$url = 'viewDashboard.php?id=' . $dashboard[1];
				redirect($url, 'API Login complete. Redirecting to Dashboard.');
			}

			switch ($_SESSION['apiClientRedirect']) {
				case 'mobile':
					redirect('viewMobileStats.php', 'View Mobile Stats');
				case 'hud':
					redirect('viewServiceDashboard.php', 'API Login complete. Redirecting to Service HUD.');
				default:
					redirect($_SERVER['REQUEST_URI'], 'API login complete.');
			}
	}
}

function getServiceByIdentifier($identifier) {
	$sql = 'SELECT s.id FROM services s WHERE s.identifier = :identifier';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':identifier', $identifier);
	$stmt->execute();

	$result = $stmt->fetchRowNotNull();

	return getServiceById($result['id']);
}

function getServiceById($id, $parseOutput = false) {
		$sql = 'SELECT s.id, s.description, s.identifier, s.executable, s.commandLine, s.karma, s.node, s.output, s.lastChanged, s.lastUpdated, s.estimatedNextCheck, s.consecutiveCount, s.commandIdentifier FROM services s WHERE s.id = :serviceId';
		$stmt = DatabaseFactory::getInstance()->prepare($sql);
		$stmt->bindValue(':serviceId', $id);
		$stmt->execute();

		if ($stmt->numRows() == 0) {
			throw new Exception("Service not found");
		}

		$service = $stmt->fetchRowNotNull();
		$service = enrichService($service);

		$parseOutput && parseOutputJson($service);

		$service['commandLine'] = $service['executable'];

		return $service;
}

function getEvents() {
	$sql = 'SELECT s.id, s.identifier, s.output FROM services s JOIN service_metadata m ON m.service = s.identifier AND m.hasEvents = 1';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->execute();

	$events = array();
	$listEvents = $stmt->fetchAll();

	foreach ($listEvents as $itemServiceWithEvents) {
		parseOutputJson($itemServiceWithEvents);

		if (!empty($itemServiceWithEvents['events'])) {
			$events = array_merge_recursive($events, $itemServiceWithEvents['events']);
		}
	}

	return $events;
}

function getTasks() {
	$sql = 'SELECT s.id, s.identifier, s.output FROM services s JOIN service_metadata m ON m.service = s.identifier AND m.hasTasks = 1 ';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->execute();

	$listServices = $stmt->fetchAll();

	$tasks = array(
		'hihu' => array(),
		'hilu' => array(),
		'lihu' => array(),
		'lilu' => array()
	);

	foreach ($listServices as $itemService) {
		parseOutputJson($itemService);

		if (isset($itemService['tasks'])) {
			$tasks = array_merge_recursive($tasks, $itemService['tasks']);
		}
	}

	return $tasks;
}

function array_utf8_encode_recursive($dat) { 
	if (is_string($dat)) { 
        	return utf8_encode($dat); 
        } 
        
	if (is_object($dat)) { 
            $ovs= get_object_vars($dat); 
            $new=$dat; 
            foreach ($ovs as $k =>$v)    { 
                $new->$k=array_utf8_encode_recursive($new->$k); 
            } 
            return $new; 
        } 
          
        if (!is_array($dat)) return $dat; 

          $ret = array(); 
          foreach($dat as $i=>$d) $ret[$i] = array_utf8_encode_recursive($d); 
          return $ret; 
} 
																										
function outputJson($content) {
	header('Content-Type: application/json');
	$content = array_utf8_encode_recursive($content);
	$encoded = json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

	if ($encoded) {
		echo $encoded;
	} else {
		if (function_exists('json_last_error_msg')) {
			$msg = json_last_error_msg();
		} else {
			$msg = json_last_error();
		}

		throw new Exception('JSON Encode error:' . $msg);
	}

	exit;
}

function isApiPage() {
	return strpos($_SERVER['PHP_SELF'], 'json');
}

function denyApiAccess($message = 'API Access Forbidden. Did you authenticate?') {
	header('HTTP/1.0 403 Forbidden');
	header('Content-Type: application/json');

	outputJson($message);
}

function validateAcceptableDowntime($el) {
	$content = $el->getValue();
	$content = trim($content);

	if (empty($content)) {
		return;
	}

	$line = 0;
	foreach (explode("\n", $content) as $rule) {
		$line++;

		$literals = explode(' ', trim($rule));

		if (count($literals) != 3) {
			$el->setValidationError('Line ' . $line . ': 3 literals expected (field, operator, value). Found: ' . count($literals));
			return;
		}

		$field = $literals[0];
		$operator = $literals[1];
		$value = $literals[2];

		switch ($operator) {
			case '==':
			case '!':
			case '>':
			case '<':
			case '>=':
			case '<=':
				break;
			default:
				$el->setValidationError('Line ' . $line . ': Unknown operator: ' . $operator);
				return;
		}


		switch ($field) {
			case 'hour':
			case 'day':
			case 'week':
				break;
			default:
				$el->setValidationError('Line ' . $line . ': Unknown operator: ' . $field);
				return;
		}
	}
}

function deleteServiceByIdentifier($identifier) {
	$service = getServiceByIdentifier($identifier);

	$sql = 'DELETE FROM services WHERE identifier = :identifier';
	$stmt = stmt($sql);
	$stmt->bindValue(':identifier', $identifier);
	$stmt->execute();

	$sql = 'DELETE FROM service_group_memberships WHERE service = :serviceIdentifier';
	$stmt = stmt($sql);
	$stmt->bindValue(':serviceIdentifier', $identifier);
	$stmt->execute();

	$sql = 'DELETE FROM service_check_results WHERE service = :serviceIdentifier';
	$stmt = stmt($sql);
	$stmt->bindValue(':serviceIdentifier', $identifier);
	$stmt->execute();

	return $service;
}


function getWidgetInstance($id) {
	$sql = 'SELECT wi.dashboard FROM widget_instances wi WHERE wi.id = :id';
	$stmt = stmt($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();

	return $stmt->fetchRowNotNull();
}

function deleteWidgetInstance($id) {
	$widgetInstance = getWidgetInstance($id);

	$sql = 'DELETE FROM widget_instances WHERE id = :id';
	$stmt = stmt($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();

	return $widgetInstance;
}

function deleteGroupByName($name) {
	$sql = 'DELETE FROM service_group_memberships WHERE `group` = :groupTitle';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':groupTitle', $name);
	$stmt->execute();

	$sql = 'DELETE FROM service_groups WHERE title = :groupTitle';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':groupTitle', $name);
	$stmt->execute();
}

function deleteDashboardById($id) {
	$sql = 'DELETE FROM widget_instances WHERE dashboard = :id ';
	$stmt = stmt($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();

	$sql = 'DELETE FROM dashboard WHERE id = :id';
	$stmt = stmt($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
}

function getUsergroups() {
	$sql = 'SELECT g.id, g.title FROM groups g ORDER BY g.title ASC';
	$stmt = stmt($sql);
	$stmt->execute();

	return $stmt->fetchAll();
}

function getUserGroupById($id) {
	$sql = 'SELECT g.id, g.title FROM groups g WHERE g.id = :id LIMIT 1';
	$stmt = stmt($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();

	return $stmt->fetchRowNotNull();
}

function addUserToGroup($userId, $groupId) {
	$sql = 'INSERT INTO group_memberships (`user`, `group`) VALUES (:user, :group)';
	$stmt = stmt($sql);
	$stmt->bindValue(':user', $userId);
	$stmt->bindValue(':group', $groupId);
	$stmt->execute();
}

function getUsersInGroupById($groupId) {
	$sql = 'SELECT u.id AS userId, u.username FROM users u LEFT JOIN group_memberships m ON m.user = u.id WHERE m.`group` = :id ';
	$stmt = stmt($sql);
	$stmt->bindValue(':id', $groupId);
	$stmt->execute();

	return $stmt->fetchAll();
}

function deleteUserGroupMembership($user, $group) {
	$sql = 'DELETE FROM group_memberships WHERE user = :user AND `group` = :group LIMIT 1';
	$stmt = stmt($sql);
	$stmt->bindValue(':user', $user);
	$stmt->bindValue(':group', $group);
	$stmt->execute();
}

function createUsergroup($title) {
	$sql = 'INSERT INTO groups (`title`) VALUES (:title)';
	$stmt = stmt($sql);
	$stmt->bindValue(':title', $title);
	$stmt->execute();

	return insertId();
}

function deleteUsergroupById($id) {
	$sql = 'DELETE FROM groups WHERE id = :id LIMIT 1';	
	$stmt = stmt($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
}

function createGroup($title) {
	$sql = 'INSERT INTO service_groups (title) VALUES (:title)';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue('title', $title);
	$stmt->execute();

	return insertId();
}

function getRooms() {
	$sql = 'SELECT r.id, r.filename, r.title FROM rooms r';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':id', Sanitizer::getInstance()->filterUint('id'));
	$stmt->execute();
}

function getMaintPeriodById($id) {
	$sql = 'SELECT s.content, s.title FROM acceptable_downtime_sla s WHERE s.id = :id';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
	$sla = $stmt->fetchRowNotNull();

	return $sla;
}

function deleteMaintPeriodById($id) {
	$sql = 'DELETE FROM acceptable_downtime_sla WHERE id = :id';
	$stmt = stmt($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();

	$sql = 'UPDATE service_metadata SET acceptableDowntimeSla = NULL WHERE acceptableDowntimeSla = :id';
	$stmt = stmt($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
}

function setMaintPeriodContent($id, $content, $title) {
	$sql = 'UPDATE acceptable_downtime_sla SET content = :content, title = :title WHERE id = :id';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':content', $content);
	$stmt->bindValue(':title', $title);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
}

function getServicesUngrouped() {
	$sql = 'SELECT s.estimatedNextCheck, s.secondsRemaining, s.description, s.id FROM services s WHERE s.description NOT IN (SELECT s2.description FROM service_group_memberships m INNER JOIN services s2 ON m.service = s2.identifier)';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->execute();

	$listServices = $stmt->fetchAll();

	return $listServices;
}

function getMembershipsFromServiceIdentifier($identifier) {
	$sql = 'SELECT m.id, m.`group`, g.id AS groupId, g.title AS groupName FROM service_group_memberships m INNER JOIN service_groups g ON m.group = g.title WHERE m.service = :service';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':service', $identifier);
	$stmt->execute();

	return $stmt->fetchAll();
}

function deleteServiceGroupMembershipById($id) {
	$sql = 'DELETE FROM service_group_memberships WHERE id = :id';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
}

function getServiceGroupMembershipById($id) {
	$sql = 'SELECT m.*, s.id AS service FROM service_group_memberships m INNER JOIN services s ON m.service = s.identifier WHERE m.id = :id';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();

	return $stmt->fetchRowNotNull();
}

function setGroupPermissions($id, array $perms) {
	$sql = 'DELETE FROM privileges_g WHERE `group` = :id';
	$stmt = stmt($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();

	foreach ($perms as $perm) {
		$sql = 'SELECT p.id FROM permissions p WHERE p.`key` = :key LIMIT 1';
		$stmt = stmt($sql);
		$stmt->bindValue(':key', trim($perm));
		$stmt->execute();

		$permDb = $stmt->fetchRowNotNull();
	
		$sql = 'INSERT INTO privileges_g (`permission`, `group`) VALUES (:key, :group)';
		$stmt = stmt($sql);
		$stmt->bindValue(':key', $permDb['id']);
		$stmt->bindValue(':group', $id);
		$stmt->execute();

	}
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

function getSingleServiceMetric($service, $field) {
	$pat = '#<json>(.+)</json>#ims';

	if ($field == 'karma') {
		$metric = new stdClass;
		$metric->date = $service['checked'];
		$metric->karma = $service['karma'];
		$metric->value = karmaToInt($service['karma']);

		return $metric;
	}

		$res = preg_match($pat, $service['output'], $matches);

		if ($res) {
			$ret = preg_replace($pat, null, $service['output']);

			$json = json_decode($matches[1]);

			if ($field == 'count') {
				$metric = new stdClass;
				$metric->date = $service['date'];
				$metric->karma = $service['karma'];
				$metric->value = count($json);

				return $metric;
			}

			if (!empty($json->metrics)) {
				foreach ($json->metrics as $metric) {
					if ($metric->name != $field) {
						continue;
					}
		
					$metric->date = $service['checked'];
					$metric->karma = $service['karma'];

					return $metric;
				}
			}
		} else {
			$metric = extractNagiosMetric($service, $field);
/*
			$metric = new stdClass;
			$metric->date = $service['date'];
			$metric->karma = $service['karma'];
			$metric->value = '[NO OUTPUT]';
*/
			return $metric;
		}

}

function getServiceMetrics($results, $field) {
	$matches = array();
	$metrics = array();

	foreach ($results as $service) {
		$metric = getSingleServiceMetric($service, $field);

		if (!empty($metric)) {
			$metrics[] = $metric;
		}
	}

	foreach ($metrics as &$metric) {
		$metric->date = strtotime($metric->date);
	}

	return $metrics;
}

function getClassInstance($id) {
	$sql = <<<SQL
SELECT
	i.id,
	i.title,
	p.icon
FROM 
	class_instances i 
LEFT JOIN class_instance_parents ip ON ip.instance = i.id
LEFT JOIN classes p ON ip.parent = p.id
WHERE 
	i.id = :instanceId
LIMIT 1
SQL;

	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':instanceId', $id);
	$stmt->execute();

	return $stmt->fetchRowNotNull();
}

function getInstanceRequirements($id) {
// TODO: It might be possible to have multiple checks assigned to a requirement
// if DISTINCT is removed and the query is slightly adjusted. 
$sql = <<<SQL
SELECT DISTINCT
	i.id AS instanceId,
	p.title AS owningClassTitle, 
	p.id AS owningClassId,
	r.title AS requirementTitle,
	r.command AS requirementRecommendedCommand,
	r.id AS requirementId,
	a.service,
	s.identifier,
	s.karma,
	s.identifier AS serviceIdentifier,
	s.lastUpdated AS serviceLastUpdated,
	m.icon
FROM 
	class_instances i
LEFT JOIN class_instance_parents ip ON
	ip.instance = i.id
LEFT JOIN classes p ON 
	ip.parent = p.id
RIGHT JOIN class_service_requirements r ON
	r.class = p.id
LEFT JOIN class_service_assignments a ON
	a.instance = ip.instance
	AND a.requirement = r.id
LEFT JOIN services s ON
	a.service = s.id
LEFT JOIN service_metadata m ON
	m.service = s.identifier
WHERE 
	ip.instance = :instanceId
SQL;

	$stmt = stmt($sql);
	$stmt->bindValue(':instanceId', $id);
	$stmt->execute();

	return $stmt->fetchAll();
}

function getImmediateClassInstances($id) {
	$sql = <<<SQL
SELECT DISTINCT 
	ci.id AS id,
	ci.title, 
	r.title AS requirementTitle,
	r.id AS requirementId,
	count(s.id) AS goodCount,
	count(a.id) AS assignedCount,
	count(r.id) AS totalCount
FROM 
	class_instances ci
LEFT JOIN class_instance_parents ip ON 
	ip.instance = ci.id
LEFT JOIN classes c ON
	ip.parent = c.id
LEFT JOIN class_service_requirements r ON
	r.class = c.id
LEFT JOIN class_service_assignments a ON
	a.instance = ci.id
	AND a.requirement = r.id
LEFT JOIN services s ON
	a.service = s.id
	AND s.karma = "GOOD"
WHERE ip.parent = :id
GROUP BY
	ci.id
SQL;

	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();

	$listInstances = $stmt->fetchAll();

	foreach ($listInstances as $index => $instance) {
			$row = &$listInstances[$index];
			$row['assignedKarma'] = 'unknown';

			if ($row['assignedCount'] == $row['totalCount']) {
				$row['overallKarma'] = 'good';
			} else {
				$row['overallKarma'] = 'bad';
			}

			if ($row['goodCount'] == $row['assignedCount']) {
				$row['assignedKarma'] = 'good';
			} else {
				$row['assignedKarma'] = 'bad';
			}
	}

	return $listInstances;
}

function getImmediateChildrenClasses($id) {
	$sqlImmediateChildren = <<<SQL
SELECT 
	n.id AS id,
	n.title,
	n.icon, 
	children.count AS childrenCount,
	(count(parent.title) - (children.depth + 1)) AS depth
FROM 
	classes AS n,
	classes AS parent,
	classes AS sub_parent, 
	(
		SELECT 
			n.title,
			(count(parent.title) - 1) AS depth,
			count(parent.title) AS count
		FROM
			classes AS n,
			classes AS parent
		WHERE
			n.l BETWEEN parent.l AND parent.r 
			AND n.id = :nodeId
		GROUP BY 
			n.title, 
			n.l
	) AS children
WHERE 
	n.l BETWEEN parent.l AND parent.r
	AND n.l BETWEEN sub_parent.l AND sub_parent.r
	AND parent.title = children.title
	AND n.id != :nodeIdOrig
GROUP BY n.title
HAVING depth <= 1
ORDER BY n.l
	
SQL;

	$sql = $sqlImmediateChildren;
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':nodeId', $id);
	$stmt->bindValue(':nodeIdOrig', $id);
	$stmt->execute();

	return $stmt->fetchall();
}

function getClass($id) {
	$sql = 'SELECT c.* FROM classes c WHERE c.id = :id';
	$stmt = stmt($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();

	$class = $stmt->fetchRowNotNull();

	return $class;
}

function getClassRequirements($id) {
	$sql = 'SELECT r.id, r.title FROM class_service_requirements r WHERE r.class = :id ';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();

	return $stmt->fetchAll();
}

function getElementServiceIcon($default) {
	$el = new \libAllure\ElementSelect('icon', 'Icon', null, '<span id = "serviceIconPreview"><em>No icon selected.</em></span>');
	$el->addOption('', '');

	$listIcons = scandir('resources/images/serviceIcons/');

	foreach ($listIcons as $k => $itemIcon) {
		if ($itemIcon[0] == '.') {
			continue;
		}

		if (stripos($itemIcon, '.png') == false) {
			continue;
		}

		$el->addOption($itemIcon, $itemIcon);
	}

	$el->setValue($default);
	$el->setOnChange('serviceIconChanged');
	
	return $el;
}

function getClassParents($class) {
	$sql = 'SELECT c.id, c.title FROM classes c WHERE c.l < :left AND c.r > :right ORDER BY c.l';
	$stmt = db()->prepare($sql);
	$stmt->bindValue(':left', $class['l']);
	$stmt->bindValue(':right', $class['r']);
	$stmt->execute();

	return $stmt->fetchAll();
}

function getDashboards() {
	$sql = 'SELECT d.title, d.id, count(w.id) AS widgetCount FROM dashboard d LEFT JOIN widget_instances w ON w.dashboard = d.id GROUP BY d.id';
	$stmt = stmt($sql);
	$stmt->execute();

	return $stmt->fetchAll();
}

function getCommands() {
	$sql = 'SELECT c.id, c.commandIdentifier, c.icon, count(s.id) AS serviceCount, count(ac.id) AS remoteConfigCommandCount FROM command_metadata c LEFT JOIN services s ON s.commandIdentifier = c.commandIdentifier LEFT JOIN remote_config_commands ac ON ac.metadata = c.id GROUP BY c.id';
	$stmt = stmt($sql);
	$stmt->execute();

	return $stmt->fetchAll();
}

function getUsers() {
	$sql = 'SELECT u.id, u.username FROM users u';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->execute();

	return $stmt->fetchAll();
}

function getVersion() {
	$version = '???';

	try {
		$buildId = getBuildId();
		$version = $buildId['tag'];
	} catch (Exception $e) {}

	return $version;
}

function getBuildId() {
	$buildIdFile = __DIR__ . '/../.buildid';

	if (file_exists($buildIdFile)) {
		$buildId = parse_ini_file($buildIdFile, false, INI_SCANNER_RAW);

		if (!$buildId) {
			throw new Exception('buildid found, but could not be parsed');
		}

		return $buildId;
	} else {
		throw new Exception('buildid does not exist.');
	}
}

function listMaintPeriods() {
	$sql = 'SELECT s.id, s.title, s.content, COUNT(m.id) AS countServices FROM acceptable_downtime_sla s LEFT JOIN service_metadata m ON m.acceptableDowntimeSla = s.id GROUP BY s.id';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->execute();
	$listMaintPeriods = $stmt->fetchAll();

	return $listMaintPeriods;
}

if (!function_exists('array_column')) {
	function array_column(array $arr, $valCol, $keyCol = null) {
		$ret = array();

		foreach ($arr as $val) {
			if ($keyCol == null) {
				$ret [] = $val[$valCol];
			} else {
				$ret[$val[$keyCol]] = $val[$valCol];
			}
		}

		return $ret;
	}
}

function getAllCommands() {
	$sql = 'SELECT c.id, c.command_Line, c.identifier, c.command_line, count(s.id) AS instanceCount, c.metadata AS metadataId, m.commandIdentifier AS metadataIdentifier, m.icon FROM remote_config_commands c LEFT JOIN command_metadata m ON c.metadata = m.id LEFT JOIN remote_config_services s ON s.command = c.id GROUP BY c.id';
	$stmt = stmt($sql)->execute();

	$commands = $stmt->fetchAll();

	return $commands;
}

function getAllRemoteConfigServices() {
	$sql = 'SELECT s.id, s.name, s.parent, c.id AS commandId, c.identifier AS commandIdentifier, count(a.id) AS instanceCount, m.icon FROM remote_config_services s LEFT JOIN remote_config_commands c ON s.command = c.id LEFT JOIN command_metadata m ON c.metadata = m.id LEFT JOIN remote_config_allocated_services a ON a.service = s.id GROUP BY s.id ORDER BY s.name';
	$stmt = stmt($sql)->execute();

	$services = $stmt->fetchAll();

	return $services;
}

function deleteConfigServiceInstance($id) {
	$sql = 'SELECT s.id, s.config FROM remote_config_allocated_services s WHERE s.id = :service LIMIT 1';
	$stmt = db()->prepare($sql);
	$stmt->bindValue(':service', $id);
	$stmt->execute();

	$service = $stmt->fetchRowNotNull();
	$config = $service['config'];

	$sql = 'DELETE FROM remote_config_allocated_services WHERE id = :service LIMIT 1';
	$stmt = db()->prepare($sql);
	$stmt->bindValue(':service', $id);
	$stmt->execute();

	return $config;
}

function getServiceArgumentValues($serviceId) {
	$sql = 'SELECT a.name, v.value FROM remote_config_service_arg_values v LEFT JOIN remote_config_command_arguments a ON v.argument = a.id WHERE v.service = :serviceId';
	$stmt = db()->prepare($sql);
	$stmt->bindValue(':serviceId', $serviceId);
	$stmt->execute();

	$args = array();

	foreach ($stmt->fetchAll() as $arg) {
		$args[$arg['name']] = $arg['value'];
	}

	return $args;
}

function getServiceResults($serviceIdentifier, $nodeIdentifier, $interval = 7, $resolution = null) {
	$interval = intval($interval);

	$interval = 2;
	if ($resolution == null) {
		$resolution = $interval * 50;
	}

	stmt('SET @row := -1')->execute();
	$sql = 'SELECT r.id, r.output, r.checked, r.karma, r.checked AS lastUpdated FROM service_check_results r INNER JOIN (SELECT ID from (SELECT @row := @row + 1 AS rowNum, id FROM (SELECT id FROM service_check_results WHERE checked > date_sub(now(), INTERVAL ' . $interval . ' DAY) AND service = :serviceIdentifier AND node = :nodeIdentifier) AS sorted) AS ranked where rowNum % :resolution = 0) AS subset on subset.id = r.id ORDER BY r.checked DESC ';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':serviceIdentifier', $serviceIdentifier);
	$stmt->bindValue(':nodeIdentifier', $nodeIdentifier);
	$stmt->bindValue(':resolution', $resolution);
	$stmt->execute();

	$listResults = $stmt->fetchAll();

	if (!empty($listResults)) {
		$k = sizeof($listResults) - 1;
		$lastDate = strtotime($listResults[$k]['checked']);

		for($i = 0; $i < sizeof($listResults); $i++) {
			$currentDate = strtotime($listResults[$k]['checked']);
			$listResults[$k--]['relative'] = getRelativeTimeSeconds($currentDate - $lastDate, true);
			$lastDate = $currentDate;
		}
	}

	foreach ($listResults as $result) {
		invalidateOldServices($result);
	}

	$listResultsDebug = array(
		array('checked' => '2016-05-26T14:35:02+00:00', 'value' => 1, 'output' => '', 'karma' => 'GOOD'),
		array('checked' => '2017-05-26T14:35:02+00:00', 'value' => 2, 'output' => '', 'karma' => 'GOOD'),
		array('checked' => '2018-05-26T14:35:02+00:00', 'value' => 3, 'output' => '', 'karma' => 'GOOD'),
	);

	return $listResults;
}

function allocateNodeToConfig($node, $config) {
	$sql = 'INSERT INTO remote_config_allocated_nodes (node, config) VALUES (:node, :config)';
	$stmt = db()->prepare($sql);
	$stmt->bindValue(':node', $node);
	$stmt->bindValue(':config', $config);
	$stmt->execute();	
}

function isUpgradeNeeded() {
	require_once 'includes/classes/Upgrader.php';

	$upgrader = new Upgrader();

	return $upgrader->isUpgradeNeeded();
}

function associateRemoteAndReportedConfigs($configString, $remoteConfigs) {
	$reportedConfigs = parseReportedConfigs($configString);

	foreach ($remoteConfigs as $index => $remoteConfig) {
		$remoteConfigs[$index]['reported'] = null;

		foreach ($reportedConfigs as $reportedConfig) {
			if ($remoteConfig['id'] == $reportedConfig['remoteId']) {
				$remoteConfigs[$index]['reported'] = $reportedConfig;
				break;
			}

		}
	}

	return $remoteConfigs;
}

function parseReportedConfigs($configString) {
	$configs = array();

	$configString = str_replace(array('[', ']', ','), '', $configString);

	$configLines = explode(" ", $configString);

	foreach ($configLines as $line) {
		$lineElements = explode(":", $line);

		if (count($lineElements) == 4) {
			$configs[$lineElements[1]] = array(
				'sourceTag' => $lineElements[0],
				'remoteId' => $lineElements[1],
				'updated' => $lineElements[2],
				'errors' => $lineElements[3] == "true",
				'karma' => 'GOOD',
				'status' => '???',
			);
		}
	}

	return $configs;
}

function deleteNodeById($id) {
	$sql = 'DELETE FROM nodes WHERE id = :id ';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();

	$sql = 'DELETE FROM';
}

function sessionOptions() {
	if (!isset($_SESSION['options'])) {
		$_SESSION['options'] = new SessionOptions();
	} 

	global $tpl;
	$tpl->assign('sessionOptions', $_SESSION['options']);

	return $_SESSION['options'];
}

function defineFromEnv($name) {
	if (defined($name)) {
		return;
	}

	if (isset($_ENV[$name])) {
		define($name, $_ENV[$name]);
	}
}

function configAutodiscover() {
	if (!isset($_SESSION['configAutodiscover'])) {
		$_SESSION['configAutodiscover'] = true;
	}

	defineFromEnv('CFG_DB_DSN');
	defineFromEnv('CFG_DB_USER');
	defineFromEnv('CFG_DB_PASS');
	defineFromEnv('CFG_PASSWORD_SALT');
}

function definedOrException($key) {
	if (!defined($key)) {
		throw new Exception("Constant not defined: $key");
	}
}

function defineFromEnv($name) {
    if (defined($name)) {
        return;
    }

    if (isset($_ENV[$name])) {
        define($name, $_ENV[$name]);
    }
}

function configAutodiscover() {
    if (!isset($_SESSION['configAutodiscover'])) {
        $_SESSION['configAutodiscover'] = true;
    }

    defineFromEnv('CFG_DB_DSN');
    defineFromEnv('CFG_DB_USER');
    defineFromEnv('CFG_DB_PASS');
    defineFromEnv('CFG_PASSWORD_SALT');
}


function isEssentialConfigurationProvided() {
	configAutodiscover();

	try {
		definedOrException('CFG_DB_DSN');
		definedOrException('CFG_DB_USER');
		definedOrException('CFG_DB_PASS');
		definedOrException('CFG_PASSWORD_SALT');
	} catch (Exception $e) {
		return false;
	}

	return true;
}

function getRelatedLogs($criteria, $limit = 5) {
	$qb = new \libAllure\QueryBuilder();
	$qb->from('logs')->fields('*');

	foreach ($criteria as $field => $value) {
		$qb->whereEquals($field, $value);
	}

	$qb->orderBy('timestamp DESC', 'id DESC');

	$stmt = stmt($qb->build() . ' LIMIT ' . $limit);
	$stmt->execute();

	return $stmt->fetchAll();
}

function processLogs($logs) {
	foreach ($logs as $key => $log) {
		$message = $logs[$key]['message'];

		$message = str_replace('_userId_', '<a href = "viewUser.php?id=' . $log['userId'] . '">' . $log['userId'] . '</a>', $message);
		$message = str_replace('_nodeConfigId_', '<a href = "viewRemoteConfig.php?id=' . $log['nodeConfigId'] . '">' . $log['nodeConfigId'] . '</a>', $message);
		$message = str_replace('_serviceDefinitionId_', '<a href = "updateRemoteConfigurationService.php?id=' . $log['serviceDefinitionId'] . '">' . $log['serviceDefinitionId'] . '</a>', $message);

		$logs[$key]['message'] = $message;
	}

	return $logs;
}

function instanceCoverageFilter() {
	$filters = new \libAllure\FilterTracker();
	$filters->addString('identifier', 'Identifier');
	$filters->addSelect('node', getNodes(), 'identifier');

	return $filters;
}

function getServiceMetadata($identifier) {
	$sql = 'SELECT sm.actions, sm.metrics, sm.defaultMetric, sm.room, cm.id AS commandMetadataId, IF(sm.icon IS NULL, cm.icon, sm.icon) AS icon, sm.criticalCast, sm.goodCast FROM services s LEFT JOIN service_metadata sm ON s.identifier = sm.service LEFT JOIN command_metadata cm ON s.commandIdentifier = cm.commandIdentifier WHERE s.identifier = :serviceIdentifier LIMIT 1';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':serviceIdentifier', $identifier);
	$stmt->execute();

	if ($stmt->numRows() == 0) {
		$metadata = array();
		$metadata['actions'] = null;
		$metadata['metrics'] = '';
		$metadata['defaultMetric'] = null;
	} else {
		$metadata = $stmt->fetchRow();
	}

	$metadata['metrics'] = explodeOrEmpty("\n", trim($metadata['metrics']));

	return $metadata;
}



?>
