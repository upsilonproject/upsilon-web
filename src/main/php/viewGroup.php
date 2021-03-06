<?php

require_once 'includes/common.php';

$itemGroup = getGroup(san()->filterUint('id'));

setNav(array('listGroups.php' => 'Groups'), $itemGroup['title']);

use \libAllure\HtmlLinksCollection;

$links = new HtmlLinksCollection();
$links->add('updateGroup.php?id=' . $itemGroup['id'], 'Update');
$links->add('deleteGroup.php?id=' . $itemGroup['id'], 'Delete');
$links->add('addGroupMembership.php?group=' . $itemGroup['title'], 'Add services');
$links->add('addGroupMembership.php?classInstance=-1&group=' . $itemGroup['title'], 'Add class instance');
$links->add('updateMultipleServices.php?group=' . $itemGroup['id'], 'Update all services');

require_once 'includes/widgets/header.php';

$tpl->assign('hidden', false);
$tpl->assign('itemGroup', $itemGroup);
$tpl->assign('singleGroup', true);
$tpl->display('group.tpl');

require_once 'includes/widgets/footer.php';

?>
