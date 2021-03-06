<?php

require_once 'includes/common.php';
require_once 'includes/functions.remoteConfig.php';

$remoteConfig = getConfigById(san()->filterUint('id'));

use \libAllure\HtmlLinksCollection;

$links = new HtmlLinksCollection();
$links->add('updateConfigElementAssociations.php?config=' . $remoteConfig['id'], 'Command & Service Associations');
$links->addSeparator();
$links->add('createRemoteConfigNodeAllocation.php?id=' . $remoteConfig['id'], 'Allocate Node');
$links->add('createRemoteConfigServiceInstance.php?id=' . $remoteConfig['id'], 'Allocate Service');
$links->add('createRemoteConfigCommandInstance.php?id=' . $remoteConfig['id'], 'Allocate Command');
$links->addSeparator();
$links->add('deleteRemoteConfig.php?id=' . $remoteConfig['id'], 'Delete config ');
$links->add('updateRemoteConfig.php?id=' . $remoteConfig['id'], 'Update config ');

setNav(array('listRemoteConfigurations.php' => 'Remote configurations'), $remoteConfig['name']);
require_once 'includes/widgets/header.php';

$tpl->assign('nodes', getConfigNodes($remoteConfig['id']));
$tpl->assign('services', getConfigServices($remoteConfig['id']));
$tpl->assign('commands', getConfigCommands($remoteConfig['id']));

$tpl->assign('logs', processLogs(getRelatedLogs(array('nodeConfigId' => $remoteConfig['id']))));
$tpl->assign('remoteConfig', $remoteConfig);
$tpl->display('viewRemoteConfig.tpl');

require_once 'includes/widgets/footer.php';

?>
