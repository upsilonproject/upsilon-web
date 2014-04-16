<?php

$title = _('Commands');
require_once 'includes/common.php';

$links = linksCollection();
$links->add('createCommand.php', 'Create command');

require_once 'includes/widgets/header.php';

$sql = 'SELECT c.id, c.commandIdentifier, c.icon, count(s.id) AS serviceCount FROM command_metadata c LEFT JOIN services s ON s.commandIdentifier = c.commandIdentifier GROUP BY c.id';
$stmt = $db->prepare($sql);
$stmt->execute();

$tpl->assign('listCommands', $stmt->fetchAll());
$tpl->display('listCommands.tpl');

require_once 'includes/widgets/footer.php';

?>
