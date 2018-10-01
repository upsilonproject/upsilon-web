<?php

require_once 'includes/widgets/header.php';

switch ($_REQUEST['action']) {
	case 'delete':
		$nodes = [];

		foreach ($_REQUEST['services'] as $serviceIdentifier) {
			$service = deleteServiceByIdentifier($serviceIdentifier);

			$nodes[$service['node']] = 1;
		}

		foreach ($nodes as $id => $dummy) {
			echo '<a href = "viewNode.php?identifier=' . $id . '">' . $id . '</a><br />';
		}
}

$sidList = implode($_REQUEST['services'], ',');
var_dump($_REQUEST['services']);

echo '<a href = "addGroupMembership.php?serviceId[]=' . $sidList . '">Add to group</a>';

?>
