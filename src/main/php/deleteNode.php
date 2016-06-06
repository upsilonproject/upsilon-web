<?php

$title = 'Delete node';
require_once 'includes/common.php';
require_once 'includes/functions.php';

use \libAllure\Sanitizer;
use \libAllure\DatabaseFactory;

$id = Sanitizer::getInstance()->filterUint('id');

deleteNodeById($id);

redirect('index.php');

?>
