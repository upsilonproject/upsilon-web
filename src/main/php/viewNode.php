<?php

$title = 'View Node';
require_once 'includes/common.php';

use \libAllure\HtmlLinksCollection;
use \libAllure\Sanitizer;

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

$node = $stmt->fetchRow(); 

$links = new HtmlLinksCollection();
$links->add('deleteNode.php?id=' . $node['id'], 'Delete');

require_once 'includes/widgets/header.php';
require_once 'libAllure/Sanitizer.php';

$sql = 'SELECT c.id, c.name FROM remote_config_allocated_nodes a LEFT JOIN remote_configs c ON a.config = c.id WHERE a.node = :nodeIdentifier ';
$stmt = stmt($sql);
$stmt->bindValue(':nodeIdentifier', $node['identifier']);
$stmt->execute();

$tpl->assign('remoteConfigs', $stmt->fetchAll());

$tpl->assign('itemNode', $node);
$tpl->display('viewNode.tpl');

$sql = 'SELECT s.id, s.identifier, s.lastUpdated, s.output, s.karma, aln.config AS remote_config, rcs.name AS remote_config_service_identifier FROM services s LEFT JOIN remote_config_allocated_nodes aln ON aln.node = :node1 LEFT JOIN remote_config_allocated_services als ON als.config = aln.config LEFT JOIN remote_config_services rcs ON als.service = rcs.id AND rcs.name = s.identifier WHERE s.node = :node2 GROUP BY s.id';
$stmt = stmt($sql);
$stmt->bindValue(':node1', $node['identifier']);
$stmt->bindValue(':node2', $node['identifier']);
$stmt->execute();

$tpl->assign('listServices', $stmt->fetchAll());
$tpl->display('listServices.tpl');

require_once 'includes/widgets/footer.php';

?>
