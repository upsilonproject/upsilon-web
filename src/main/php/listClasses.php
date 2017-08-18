<?php

require_once 'includes/common.php';

use \libAllure\HtmlLinksCollection;
use \libAllure\Sanitizer;

$id = (Sanitizer::getInstance()->filterUint('id'));

if (empty($id)) {
	$id = 1;
}

$links = new HtmlLinksCollection();
$links->add('createClass.php?parent=' . $id, 'Create Class');
$links->add('createClassRequirement.php?id=' . $id, 'Create Requirement');
$links->add('updateClass.php?id=' . $id, 'Update class');
$links->add('deleteClass.php?id=' . $id, 'Delete class');
$links->add('createClassInstance.php?parent=' . $id, 'Create class instance');

$tpl->assign('listSubClasses', getImmediateChildrenClasses($id));
$tpl->assign('listInstances', getImmediateClassInstances($id));

$itemClass = getClass($id);

addNavBreadcrumb('Classes');

foreach (getClassParents($itemClass) as $parent) {
	addNavBreadcrumb('listClasses.php?id=' . $parent['id'], $parent['title']);
}

addNavBreadcrumb($itemClass['title']);

require_once 'includes/widgets/header.php';

try {
	$tpl->assign('itemClass', $itemClass);
} catch (Exception $e) {
	$tpl->error('Could not find class: ' . $id);
}

$tpl->assign('listRequirements', getClassRequirements($id));
$tpl->assign('listParents', getClassParents($itemClass));

$tpl->display('listClasses.tpl');
require_once 'includes/widgets/footer.php';

?>
