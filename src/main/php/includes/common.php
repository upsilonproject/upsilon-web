<?php

@include_once 'includes/init.php';

date_default_timezone_set('Europe/London');

putenv("LANG=en_GB");
setlocale(LC_ALL, 'en_GB');
bindtextdomain('messages', 'includes/locale/nocache');
bindtextdomain('messages', 'includes/locale/');
textdomain('messages');

function add_include_path($path) {
    set_include_path($path . PATH_SEPARATOR . get_include_path());
}

add_include_path(dirname(__FILE__) . '/libraries/');
add_include_path(dirname(__FILE__) . '/libraries/jwread/lib-allure/src/main/php/');
add_include_path('/etc/upsilon-web/');

require_once 'includes/functions.php';
if (!@include_once 'includes/libraries/autoload.php') {
    die('Composer autoloader not found.');
}

\libAllure\ErrorHandler::getInstance()->beGreedy();

$tpl = new \libAllure\Template('upsilonWeb');
$tpl->registerModifier('strtolower', 'strtolower');
$tpl->registerModifier('implode', 'implode');
$tpl->registerModifier('trim', 'trim');

use \libAllure\AuthBackendDatabase;
use \libAllure\Session;

@include_once 'config.php';

if (isEssentialConfigurationProvided()) {
    $db = connectDatabase();

    $backend = new AuthBackendDatabase();
    $backend->setSalt(CFG_PASSWORD_SALT);
    $backend->registerAsDefault();

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
    redirect('installer.php', 'Initial config not valid, assuming installation.');
}

?>
