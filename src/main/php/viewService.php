<?php

require_once 'includes/common.php';

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

function getServiceMetadata($identifier) {
	$sql = 'SELECT sm.actions, sm.metrics, sm.defaultMetric, sm.room, cm.id AS commandMetadataId, IF(sm.icon IS NULL, cm.icon, sm.icon) AS icon, sm.criticalCast, sm.goodCast FROM services s LEFT JOIN service_metadata sm ON s.identifier = sm.service LEFT JOIN command_metadata cm ON s.commandIdentifier = cm.commandIdentifier WHERE s.identifier = :serviceIdentifier LIMIT 1';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':serviceIdentifier', $identifier);
	$stmt->execute();

	if ($stmt->numRows() == 0) {
		$metadata = array();
		$metadata['actions'] = null;
		$metadata['metrics'] = '';
		$metadata['defaultMetric'] = null;
	} else {
		$metadata = $stmt->fetchRow();
	}

	$metadata['metrics'] = explodeOrEmpty("\n", trim($metadata['metrics']));

	return $metadata;
}

$tpl->assign('metadata', getServiceMetadata($service['identifier']));

$listResults = getServiceResults($service['identifier']);

$tpl->assign('listResults', $listResults);

$tpl->assign('instanceGraphIndex', 0);
$tpl->assign('listServiceId', array($service['id']));
$tpl->assign('metric', 'karma');
$tpl->assign('yAxisMarkings', array());
$tpl->display('viewService.tpl');

require_once 'includes/widgets/footer.php';

?>
