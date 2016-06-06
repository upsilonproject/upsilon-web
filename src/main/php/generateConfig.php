<?php

set_include_path(get_include_path() . PATH_SEPARATOR . '../');

require_once 'includes/common.php';
require_once 'includes/functions.remoteConfig.php';

$nodeConfig = getConfigById(san()->filterString('id'));

$tpl->assign('comment', 'Generated config: ' . $nodeConfig['name']);

$configXml = generateConfigFromId($nodeConfig['id']);

header('Last-Modified: ' . $nodeConfig['mtime']);
header('Content-Type: application/xml');

echo $configXml;

?>
