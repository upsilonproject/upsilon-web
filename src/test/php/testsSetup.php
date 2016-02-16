<?php

set_include_path(get_include_path() . PATH_SEPARATOR . realpath(__DIR__ . '/../../main/php/'));

if ((!@include 'includes/libraries/autoload.php') !== false) {
	throw new Exception('Could not include the library autoloader. Is composer up to date? Include path: ' . get_include_path());
}

require_once 'includes/common.php';

?>
