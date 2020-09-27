<?php

require_once 'includes/widgets/header.php';
require_once 'includes/functions.remoteConfig.php';

$configId = san()->filterUint('configId');

sendUpdatedConfig($configId, san()->filterString('node'));

$tpl->assign('messageClass', 'box');
$tpl->assign('messageTitle', 'Configuration sent.');
$tpl->assign('message', '<ul><li><a href = "viewNode.php?identifier=' . san()->filterString('node') . '">Node: ' . san()->filterString('node') . '</a></li><li><a href = "listNodes.php">Node list</a></li><li><a href = "listRemoteConfigurations.php">Configuration list</a></li>');
$tpl->display('message.tpl');

require_once 'includes/widgets/footer.php';

?>
