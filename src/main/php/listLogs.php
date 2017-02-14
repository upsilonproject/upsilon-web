<?php

require_once 'includes/widgets/header.php';

$sql = 'SELECT l.*, u.username FROM logs l LEFT JOIN users u ON l.userId = u.id ORDER BY l.timestamp DESC LIMIT 250';
$stmt = stmt($sql);
$stmt->execute();
$logs = $stmt->fetchAll();

foreach ($logs as $key => $log) {
	$logs[$key]['message'] = str_replace('_userId_', '<a href = "viewUser.php?id=' . $log['userId'] . '">' . $log['username'] . '</a>', $log['message']);
}

$tpl->assign('listLogs', $logs);
$tpl->display('listLogs.tpl');

require_once 'includes/widgets/footer.php';

?>
