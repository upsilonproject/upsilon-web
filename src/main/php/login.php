<?php

$title = 'Login';
require_once 'includes/common.php';
require_once 'libAllure/util/FormLogin.php';

use \libAllure\util\FormLogin;
use \libAllure\ElementCheckbox;

if (isUpgradeNeeded() && !defined('UPGRADE_IN_PROGRESS')) {
	redirect('upgrade.php', 'An upgrade is needed.');
}

handleApiLogin();

$f = new FormLogin();
$f->setTitle('Upsilon Login');

if ($f->validate()) {
	logger('User _userId_, logged in', array('userId' => \libAllure\Session::getUser()->getUsername()));
	$_SESSION['options'] = new SessionOptions();
	header('Location: index.php');
}

require_once 'includes/widgets/header.php';
global $crypto;

if (!isUsingSsl() && getSiteSetting('warn_not_using_https', true)) {
	$httpsUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'];
	$tpl->assign('message', 'You are not using SSL! Would you like to switch to the <a href = "' . $httpsUrl . '">HTTPS</a> version?');
	$tpl->assign('messageClass', 'loginFormContainer box tutorialMessage');
	$tpl->display('message.tpl');
}

$f->containerClass = 'loginFormContainer box';
$tpl->displayForm($f);

require_once 'includes/widgets/footer.php';

?>

