<?php

$title = 'Class candidates';
require_once 'includes/widgets/header.php';

use \libAllure\DatabaseFactory;

$sql = 'SELECT * FROM class_candidates';
$stmt = DatabaseFactory::getInstance()->prepare($sql);
$stmt->execute();

$tpl->assign('listCandidates', $stmt->fetchAll());
$tpl->display('listClassCandidates.tpl');

require_once 'includes/widgets/footer.php';

?>
