<?php

date_default_timezone_set('Europe/London');

putenv("LANG=en_GB");
setlocale(LC_ALL, 'en_GB');
bindtextdomain('messages', 'includes/locale/nocache');
bindtextdomain('messages', 'includes/locale/');
textdomain('messages');

function addIncludePath($path) {
	set_include_path($path . PATH_SEPARATOR . get_include_path());
}

add_include_path(dirname(__FILE__) . '/libraries/');
add_include_path(dirname(__FILE__) . '/libraries/jwread/lib-allure/src/main/php/');

require_once 'includes/functions.php';
require_once 'includes/libraries/autoload.php';

\libAllure\ErrorHandler::getInstance()->beGreedy();

$tpl = new \libAllure\Template('upsilonWeb');

if ((@include 'includes/config.php') !== false) {
	require_once 'includes/config.php';

	use \libAllure\AuthBackend;
	use \libAllure\AuthBackendDatabase;

	$db = connectDatabase();

	$backend = new AuthBackendDatabase();
	$backend->setSalt(null, CFG_PASSWORD_SALT);
	$backend->registerAsDefault();

	use \libAllure\Session;
	Session::setCookieLifetimeInSeconds(31104000);
	Session::start();

	if (!defined('ANONYMOUS_PAGE') && !Session::isLoggedIn()) {
		if (isApiPage()) {
			denyApiAccess();
		} else {
			require_once 'login.php';
		}
	}
} else if (!defined('INSTALLATION_IN_PROGRESS')) {
	redirect('installer.php', 'No config file found. Assuming installation.');
}

?>
