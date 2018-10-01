<?php

require_once 'includes/common.php';

use \libAllure\HtmlLinksCollection;

$links = new HtmlLinksCollection();
$links->add('createRemoteConfigCommand.php?', 'Create Command');

$title = 'Commands';
require_once 'includes/widgets/header.php';

$tpl->assign('listCommands', getAllCommands());
$tpl->display('listCommandDefinitions.tpl');

require_once 'includes/widgets/footer.php';
