<?php

require_once 'includes/common.php';

use \libAllure\HtmlLinksCollection;

$links = new HtmlLinksCollection('List of users');
$links->add('createUser.php', 'Create user');

$title = _('List Users');
require_once 'includes/widgets/header.php';

use \libAllure\DatabaseFactory;

$tpl->assign('listUsers', getUsers());
$tpl->display('listUsers.tpl');

require_once 'includes/widgets/footer.php';

?>
