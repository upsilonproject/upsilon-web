<?php

require_once 'includes/widgets/header.php';

switch ($_REQUEST['action']) {
	case 'delete':
		foreach ($_REQUEST['services[]'] as $serviceIdentifier) {
			deleteServiceByIdentifier($serviceIdentifier);
		}
}

var_dump($_REQUEST['services']);

?>
