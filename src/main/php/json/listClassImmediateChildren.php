<?php

require_once 'jsonCommon.php';

$id = san()->filterUint('id');

outputJson(getImmediateChildrenClasses($id));

?>
