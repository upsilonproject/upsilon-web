<?php

$title = 'Login';
require_once 'includes/common.php';
require_once 'libAllure/util/FormLogin.php';

use \libAllure\util\FormLogin;
use \libAllure\ElementCheckbox;

handleApiLogin();

$f = new FormLogin();
$f->setTitle('Upsilon Login');

if ($f->validate()) {
	header('Location: index.php');
}

require_once 'includes/widgets/header.php';
global $crypto;

if (!isUsingSsl()) {
	$httpsUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'];
	$tpl->assign('message', 'You are not using SSL! Would you like to switch to the <a href = "' . $httpsUrl . '">HTTPS</a> version?');
	$tpl->assign('messageClass', 'loginFormContainer box tutorialMessage');
	$tpl->display('message.tpl');
}

$f->containerClass = 'loginFormContainer box';
$tpl->displayForm($f);

require_once 'includes/widgets/footer.php';

?>

