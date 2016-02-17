<?php

require_once 'includes/common.php';

$configId = deleteConfigServiceInstance(san()->filterUint('id'));

redirect('viewRemoteConfig.php?id=' . $configId)

?>
