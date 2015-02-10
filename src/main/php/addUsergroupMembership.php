<?php

require_once 'includes/common.php';
require_once 'libAllure/FormHandler.php';
require_once 'includes/classes/FormAddUsergroupMembership.php';

use \libAllure\FormHandler;

$fh = new FormHandler('FormAddUserToGroup');
$fh->setConstructorArgument(1, san()->filterUint('id'));
$fh->setRedirect('listUsergroups.php');
$fh->handle();

?>
