<?php

require_once 'jsonCommon.php';

use \libAllure\Session;

outputJson(array(
	'viewDashboard' => Session::hasPriv('VIEW_DASHBOARD'),
	'viewServices' => Session::hasPriv('VIEW_SERVICES'),
	'viewNodes' => Session::hasPriv('VIEW_NODES'),
	'viewClasses' => Session::hasPriv('VIEW_CLASSES'),
	'viewMaintPeriods' => Session::hasPriv('VIEW_MAINT_PERIODS'),
	'loggedIn' => Session::isLoggedIn(),
));

?>
