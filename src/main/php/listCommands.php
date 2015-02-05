<?php

$title = _('Commands');
require_once 'includes/common.php';

$links = linksCollection();
$links->add('createCommand.php', 'Create command');

require_once 'includes/widgets/header.php';

$tpl->assign('listCommands', getCommands());
$tpl->display('listCommands.tpl');

require_once 'includes/widgets/footer.php';

?>
