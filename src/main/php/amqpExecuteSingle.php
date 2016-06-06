<?php

require_once 'includes/widgets/header.php';

$service = getServiceById(san()->filterUint('serviceId'));

var_dump($service);

require_once 'includes/widgets/footer.php';

?>
