<?php

$title = _('Command Metadata');
require_once 'includes/common.php';

$links = linksCollection();
$links->add('createCommand.php', 'Create command metadata');

require_once 'includes/widgets/header.php';

$tpl->assign('listCommands', getCommands());
$tpl->display('listCommands.tpl');

require_once 'includes/widgets/footer.php';

?>
