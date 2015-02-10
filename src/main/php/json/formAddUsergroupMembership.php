<?php

require_once 'jsonCommon.php';
require_once 'includes/classes/FormAddUsergroupMembership.php';

$f = new FormAddUserToGroup();

echo json_encode($f->toJson());
?>
