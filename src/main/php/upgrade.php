<?php

define('ANONYMOUS_PAGE', true);
define('UPGRADE_IN_PROGRESS', true);
define('DRAW_NAVIGATION', false);
$title = 'Upgrade';

require_once 'includes/widgets/header.php';
require_once 'includes/classes/Upgrader.php';

$upgrader = new Upgrader();

if (!$upgrader->isUpgradeNeeded()) {
	$tpl->assign('message', 'No upgrade tasks necessary.<br/><br/> <a href = "login.php">Proceed to login</a>');
	$tpl->display('message.tpl');

	require_once 'includes/widgets/footer.php';
}

if (isset($_REQUEST['doUpgrade'])) {
	$upgrader->doUpgrade();

	$tpl->assign('message', 'Finished running upgrade tasks.<br /><br /><a href = "login.php">Proceed to login</a>');
	$tpl->display('message.tpl');

	require_once 'includes/widgets/footer.php';
}

$tpl->assign('upgradeTasks', $upgrader->getTasks());
$tpl->assign('canStartUpgrade', $upgrader->canStartUpgrade());
$tpl->display('upgrade.tpl');

require_once 'includes/widgets/footer.php';

?>
