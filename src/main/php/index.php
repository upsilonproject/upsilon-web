<?php

require_once 'includes/common.php';

$dashboards = getDashboards();

if (empty($dashboards)) {
	require_once 'listDashboards.php';
} else {
	$_REQUEST['id'] = $dashboards[0]['id'];
	require_once 'viewDashboard.php';
}

?>
