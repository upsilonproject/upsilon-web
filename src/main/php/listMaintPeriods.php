<?php

require_once 'includes/common.php';

$links = linksCollection();
$links->add('createMaintPeriod.php', 'Create new');

$title = 'Maintenance Periods';

require_once 'includes/widgets/header.php';

use \libAllure\DatabaseFactory;

$tpl->assign('listMaintPeriods', listMaintPeriods());
$tpl->display('listMaintPeriods.tpl');

require_once 'includes/widgets/footer.php';

?>
