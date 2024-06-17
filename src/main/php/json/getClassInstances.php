<?php

require_once 'jsonCommon.php';

$gid = null;

if ($gid != null) {
    $classInstances = getClassInstancesNotInGroup($gid);
} else {
    $classInstances = getClassInstances();
}

outputJson($classInstances);

?>
