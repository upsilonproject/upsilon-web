<?php

define('ANONYMOUS_PAGE', true);
require_once 'jsonCommon.php';

use \libAllure\Session;

function loginStatusMessage($newLogin = false) {
	$prefix = 'Already';

	if ($newLogin) {
		$prefix = '';
	}

	$status = array( 
		'message' => $prefix . ' logged in as ' . Session::getUser()->getUsername(),
		'sid' => session_id()
	);

	return $status;
}

if (Session::isLoggedIn()) {
	outputJson(loginStatusMessage());
}

try {
	handleApiLogin(false);

	outputJson(loginStatusMessage(true));
} catch (Exception $e) {
	denyApiAccess('Exception. ' . get_class($e) . ' = ' . $e->getMessage());
}

?>
