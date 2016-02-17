<?php

require_once 'includes/common.php';

$id = san()->filterId();

require_once 'includes/functions.remoteConfig.php';

$configId = deleteConfigAllocatedNode($id);

redirect('viewRemoteConfig.php?id=' . $configId);
