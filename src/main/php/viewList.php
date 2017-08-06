<?php

$title = 'List of Services';
require_once 'includes/widgets/header.php';

use \libAllure\DatabaseFactory;
use \libAllure\FilterTracker;

$filters = getFilterServices();
$listServices = getServicesWithFilter(null, $filters);

$tpl->assign('filters', $filters->getAll());
$tpl->assign('listServices', $listServices);
$tpl->display('listServices.tpl');

require_once 'includes/widgets/footer.php';

?>
