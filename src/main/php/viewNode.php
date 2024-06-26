<?php

require_once 'includes/common.php';

use \libAllure\HtmlLinksCollection;
use \libAllure\Sanitizer;
use \libAllure\QueryBuilder;

if (isset($_REQUEST['identifier'])) {
	$sql = 'SELECT n.* FROM nodes n WHERE n.identifier = :nodeId LIMIT 1';
	$id = Sanitizer::getInstance()->filterString('identifier');
} else {
	$sql = 'SELECT n.* FROM nodes n WHERE n.id = :nodeId LIMIT 1';
	$id = Sanitizer::getInstance()->filterUint('id');
}

$stmt = $db->prepare($sql);
$stmt->bindValue(':nodeId', $id);
$stmt->execute();

$node = $stmt->fetchRowNotNull();

$links = new HtmlLinksCollection();
$links->add('deleteNode.php?id=' . $node['id'], 'Delete');

setNav(array('listNodes.php' => 'Nodes'), $node['identifier']);

require_once 'includes/widgets/header.php';
require_once 'libAllure/Sanitizer.php';

$sql = 'SELECT c.id, c.name FROM remote_config_allocated_nodes a LEFT JOIN remote_configs c ON a.config = c.id WHERE a.node = :nodeIdentifier ';
$stmt = stmt($sql);
$stmt->bindValue(':nodeIdentifier', $node['identifier']);
$stmt->execute();

$remoteConfigs = $stmt->fetchAll();
$remoteConfigs = associateRemoteAndReportedConfigs($node['configs'], $remoteConfigs); 

$tpl->assign('remoteConfigs', $remoteConfigs);

$tpl->assign('itemNode', $node);
$tpl->display('viewNode.tpl');

function getServicesConfiguredForNode($node) {
	$sql = ' SELECT rcs.id, rcs.name, aln.config AS configId, rc.name AS configName FROM remote_config_allocated_nodes aln LEFT JOIN remote_config_allocated_services als ON als.config = aln.config LEFT JOIN remote_config_services rcs ON als.service = rcs.id LEFT JOIN remote_configs rc ON aln.config = rc.id WHERE aln.node = :node';

	$stmt = stmt($sql);
	$stmt->bindValue(':node', $node);
	$stmt->execute();

	$configuredServices = $stmt->fetchAll();

	return $configuredServices;
}

function getServicesReportedForNode($node) {
	$qb = new QueryBuilder();
	$qb->from('services')->fields('id', 'identifier', 'lastUpdated', 'output', 'karma', 'node');
	$qb->where('node', '=', ':node');
	$qb->whereLikeValue('identifier', 'waffles');

	$sql = 'SELECT s.id, s.identifier, s.lastUpdated, trim(s.output), s.karma, s.node FROM services s WHERE s.node = :node';

	$stmt = stmt($sql);
	$stmt->bindValue(':node', $node);
	$stmt->execute();

	$reportedServices = $stmt->fetchAll();

	return $reportedServices;
}

function getServicesForNode($node) {
	$configuredServices = getServicesConfiguredForNode($node);
	$reportedServices = getServicesReportedForNode($node);

	$ret = [];

	foreach ($reportedServices as $reported) {
		foreach ($configuredServices as $configId => $configured) {
			if ($configured['name'] == $reported['identifier']) {
				unset ($configuredServices[$configId]);
	
				$reported['remote_config_service_id'] = $configured['id'];
				$reported['remote_config_service_identifier'] = $configured['name'];
				$reported['remote_config_id'] = $configured['configId'];
				$reported['remote_config_name'] = $configured['configName'];
				break;
			}
		}

		if (!isset($reported['remote_config_service_identifier'])) {
			$reported['remote_config_service_id'] = null;
			$reported['remote_config_service_identifier'] = null;
			$reported['remote_config_id'] = null;
			$reported['remote_config_name'] = null;
		}

		$ret[] = $reported;
	}

	foreach ($configuredServices as $configured) {
		$row = array();
		$row['remote_config_service_id'] = $configured['id'];
		$row['remote_config_service_identifier'] = $configured['name'];
		$row['remote_config_id'] = $configured['configId'];
		$row['remote_config_name'] = $configured['configName'];
		$row['id'] = null;
		$row['identifier'] = null;
		$row['lastUpdated'] = null;
		$row['output'] = null;
		$row['karma'] = null;
		$row['node'] = null;

		$ret[] = $row;
	}


	return $ret;
}

$filters = new \libAllure\FilterTracker();
$filters->addString("name");
$filters->setHiddenValue("node", $node['identifier'], 'identifier');

$tpl->assign('filters', $filters->getAll());
$tpl->assign('listServices', getServicesWithFilter(null, $filters));
$tpl->display('listServices.tpl');

require_once 'includes/widgets/footer.php';

?>
