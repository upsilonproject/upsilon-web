<?php

require_once 'includes/widgets/header.php';
require_once 'includes/functions.remoteConfig.php';

$configId = san()->filterUint('configId');

sendUpdatedConfig($configId, san()->filterString('node'));

$tpl->assign('message', 'Done. <a href = "listRemoteConfigurations.php">list</a>');
$tpl->display('message.tpl');

require_once 'includes/widgets/footer.php';

?>
