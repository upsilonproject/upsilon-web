<?php

require_once 'includes/widgets/header.php';

$sql = 'SELECT l.* FROM logs l LEFT JOIN users u ON l.userId = u.id ORDER BY l.timestamp LIMIT 250';
$stmt = stmt($sql);
$stmt->execute();
$logs = stmt->fetchAll();

foreach ($logs as $log) {
	$log['message'] = str_replace($log['message'], ':userId', '<a href = "viewUser.php?id=' . $log['userId'] . '">' . $log['username'] . '</a>');
}

$tpl->assign('listLogs', $logs);
$tpl->display('listLogs.php');

require_once 'includes/widgets/footer.php';

?>
