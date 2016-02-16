<?php

require_once 'includes/common.php';

use \libAllure\DatabaseFactory;
use \libAllure\HtmlLinksCollection;

$sql = 'SELECT rc.id, rc.name, count(an.id) AS nodeCount  FROM remote_configs rc LEFT JOIN remote_config_allocated_nodes an ON rc.id = an.config GROUP BY rc.id';
$stmt = DatabaseFactory::getInstance()->prepare($sql);
$stmt->execute();

$configs = $stmt->fetchAll();

$links = new HtmlLinksCollection();
$links->add('createRemoteConfiguration.php', 'Create remote configuration');
$links->addSeparator();
$links->add('createRemoteConfigService.php?', 'Create Service');
$links->add('createRemoteConfigCommand.php?', 'Create Command');

$title = 'Remote configurations';
require_once 'includes/widgets/header.php';

$tpl->assign('listRemoteConfigs', $configs);
$tpl->assign('listCommands', getAllCommands());
$tpl->assign('listServices', getAllRemoteConfigServices());
$tpl->display('listRemoteConfigs.tpl');

require_once 'includes/widgets/footer.php';
?>
