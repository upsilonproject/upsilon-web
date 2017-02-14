<?php

$title = 'Delete command';
require_once 'includes/widgets/header.php';
require_once 'includes/functions.remoteConfig.php';

$id = san()->filterUint('id');

deleteRemoteConfigurationCommand($id);

$tpl->assign('message', 'Deleted. <a href = "listCommandDefinitions.php">Command Definitions List</a>');
$tpl->display('message.tpl');

require_once 'includes/widgets/footer.php';

?>
