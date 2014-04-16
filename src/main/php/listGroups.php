<?php

require_once 'includes/common.php';

use \libAllure\HtmlLinksCollection;

$links = new HtmlLinksCollection();
$links->add('createGroup.php', 'Create group');

$title = 'Groups';

require_once 'includes/widgets/header.php';

use \libAllure\DatabaseFactory;

$tpl->assign('listGroups', getServiceGroups());
$tpl->display('listGroups.tpl');

require_once 'includes/widgets/footer.php';

?>
