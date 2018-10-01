<?php

require_once 'jsonCommon.php';

$keyword = san()->filterString('identifier');
$keyword = str_replace('*', '', $keyword);

function buildSearchSql($table, $searchField) {
	return '(SELECT ' . $searchField . ' AS identifier, id, "' . $table . '" AS type FROM ' . $table . ' WHERE ' . $searchField . ' LIKE concat("%", :keyword_' . $table . ', "%"))';
}

$sql = '';
$sql .= buildSearchSql('services', 'identifier') . ' UNION ';
$sql .= buildSearchSql('nodes', 'identifier') . ' UNION ';
$sql .= buildSearchSql('users', 'username');
$stmt = stmt($sql);

$stmt->bindValue(':keyword_services', $keyword);
$stmt->bindValue(':keyword_nodes', $keyword);
$stmt->bindValue(':keyword_users', $keyword);

$stmt->execute();

$results = $stmt->fetchAll();

foreach ($results as $key => $val) {
	$prefix = "";
	$suffix = "";

	switch($val['type']) {
		case 'services':
			$prefix = 'Service';
			$results[$key]['url'] = 'viewService.php?id=' . $val['id'];
			break;
		case 'nodes':
			$prefix = 'Node';
			$results[$key]['url'] = 'viewNode.php?id=' . $val['id'];
			break;
		default:
			$results[$key]['url'] = 'index.php';
	}

	$results[$key]['identifier'] = $prefix . ': ' . $results[$key]['identifier'] . $suffix;
}

outputJson($results);

?>
