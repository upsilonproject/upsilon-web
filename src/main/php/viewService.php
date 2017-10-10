<?php

require_once 'includes/common.php';
require_once 'includes/functions.remoteConfig.php';

use \libAllure\DatabaseFactory;
use \libAllure\Sanitizer;

$id = Sanitizer::getInstance()->filterUint('id');
$service = getServiceById($id);

$tpl->assign('itemService', $service);

$links = linksCollection(); 
$links->add('updateServiceMetadata.php?id=' . $service['id'], 'Update metadata');
$links->add('addGroupMembership.php?serviceId[]=' . $service['identifier'], 'Add to Group');
$links->add('deleteService.php?identifier=' . $service['identifier'], 'Delete');

$title = 'View Service';
require_once 'includes/widgets/header.php';

$tpl->assign('listGroupMemberships', getMembershipsFromServiceIdentifier($service['identifier']));

$classList = array();

foreach (getClassInstancesUsingService($service['id']) as $class) {
	$ci = getClassInstance($class['id']);
	$ci['requirements'] = getInstanceRequirements($ci['id']);

	$classList[] = $ci;
}

$tpl->assign('listClassInstances', $classList);

$configSource = getConfigSourceFromServiceResultIdentifier($service['identifier'], $service['node']);

$tpl->assign('metadata', getServiceMetadata($service['identifier']));
$tpl->assign('configSource', $configSource);

if (isset($configSource['remote_config_command_id'])) {
	$tpl->assign('commandLineClickable', getClickableCommandLine($configSource));
}

$tpl->assign('instanceChartIndex', 0);
$tpl->assign('listServiceId', array($service['id']));
$tpl->assign('metric', 'karma');
$tpl->assign('yAxisMarkings', array());
$tpl->assign('linkToLarger', true);
$tpl->display('viewService.tpl');

require_once 'includes/widgets/footer.php';

?>
