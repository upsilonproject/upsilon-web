<?php

require_once 'includes/common.php';

use \libAllure\DatabaseFactory;
use \libAllure\HtmlLinksCollection;

$links = new HtmlLinksCollection();
$links->add('listRemoteConfigurations.php', 'Remote configurations');

$title = 'Nodes';
require_once 'includes/widgets/header.php';

$tpl->assign('listNodes', getNodes());

$sql = 'SELECT p.child, p.parent FROM peers p ';
$stmt = DatabaseFactory::getInstance()->prepare($sql);
$stmt->execute();

$tpl->assign('listPeers', $stmt->fetchAll());

$tpl->display('listNodes.tpl');

require_once 'includes/widgets/footer.php';

?>
