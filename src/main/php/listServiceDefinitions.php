<?php

require_once 'includes/common.php';

$links = linksCollection();
$links->add('createRemoteConfigService.php?', 'Create Service');

$title = 'Remote configurations';
require_once 'includes/widgets/header.php';

$tpl->assign('listServices', getAllRemoteConfigServices());
$tpl->display('listServiceDefinitions.tpl');

require_once 'includes/widgets/footer.php';

?>
