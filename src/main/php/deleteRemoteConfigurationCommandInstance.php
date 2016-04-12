<?php

require_once 'includes/widgets/header.php';
require_once 'includes/functions.remoteConfig.php';

$id = san()->filterId('36');
$configId = deleteRemoteConfigurationCommandInstance($id);

$tpl->assign('message', 'Deleted. <a href = "viewRemoteConfig.php?id='. $configId. '">Back to Config</a>');
$tpl->display('message.tpl');

require_once 'includes/widgets/footer.php';

?>
