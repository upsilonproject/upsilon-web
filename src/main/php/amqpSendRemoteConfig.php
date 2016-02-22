<?php

require_once 'includes/widgets/header.php';
require_once 'includes/classes/Amqp.php';
require_once 'includes/functions.remoteConfig.php';

$config = generateConfigFromNodeIdentifier(san()->filterString('node'));

$msg = new UpsilonMessage('UPDATED_NODE_CONFIG', $config);
$msg->addHeader('node-identifier', san()->filterString('node'));
$msg->addHeader('remote-config-source-identifier', getSiteSetting('configSourceIdentifier'));
$msg->publish();


$tpl->assign('message', 'Done. <a href = "listRemoteConfigurations.php">list</a>');
$tpl->display('message.tpl');

require_once 'includes/widgets/footer.php';

?>