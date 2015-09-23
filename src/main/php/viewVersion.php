<?php

require_once 'includes/widgets/header.php';

try {
	var_dump(getBuildId());
} catch (Exception $e) {
	$tpl->error($e);
}

require_once 'includes/widgets/footer.php';

?>
