<?php

$title = 'Logs';
require_once 'includes/widgets/header.php';

$sql = 'SELECT l.*, u.username FROM logs l LEFT JOIN users u ON l.userId = u.id GROUP BY l.id ORDER BY l.id DESC LIMIT 250';
$stmt = stmt($sql);
$stmt->execute();
$logs = $stmt->fetchAll();
$logs = processLogs($logs);

$tpl->assign('listLogs', $logs);
$tpl->display('listLogs.tpl');

require_once 'includes/widgets/footer.php';

?>
