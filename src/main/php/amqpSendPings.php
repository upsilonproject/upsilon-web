<?php

require_once 'includes/widgets/header.php';
require_once 'includes/classes/Amqp.php';

try {
	$msg = new UpsilonMessage('REQ_NODE_SUMMARY');
	$msg->publish();
} catch (Exception $e) {
	$tpl->error($e);
}

echo 'Pings sent.';

require_once 'includes/widgets/footer.php';

?>
