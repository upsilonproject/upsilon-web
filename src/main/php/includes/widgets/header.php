<?php

require_once 'includes/common.php';
require_once 'includes/functions.php';

use \libAllure\Session;
use \libAllure\HtmlLinksCollection;

global $tpl, $title;

if (Session::isLoggedIn()) {
	$dtBegin = Session::getUser()->getData('daytimeBegin');
	$dtEnd = Session::getUser()->getData('daytimeEnd');

	$nowHour = intval(date('G'));

	$tpl->assign('isNighttime', !($nowHour >= $dtBegin && $nowHour <= $dtEnd));
	$tpl->assign('tutorialMode', Session::getUser()->getData('tutorialMode'));
	$tpl->assign('enableDebug', Session::getUser()->getData('enableDebug'));
	$tpl->assign('username', Session::getUser()->getUsername());
} else {
	$tpl->assign('isNighttime', false);
}

$tpl->assign('siteTitle', getSiteSetting('siteTitle', 'Upsilon'));

if (isset($title)) {
	$tpl->assign('title', $title);
} else {
	$tpl->assign('title', 'Untitled page');
}

$tpl->assign('loggedIn', Session::isLoggedIn());

$tpl->assign('crypto', isUsingSsl());
$tpl->assign('sessionOptions', sessionOptions());
$tpl->assign('datetime', date('D H:i'));
$tpl->assign('apiClient', isset($_SESSION['apiClient']) ? $_SESSION['apiClient'] : false);

$generalLinks = linksCollection();

if (Session::isLoggedIn()) {
	$generalLinks = linksCollection();

	global $links, $title;
	$generalLinks->add('#', 'Actions &blacktriangledown;');

	if (isset($links)) {
		$generalLinks->addChildCollection('Actions &blacktriangledown;', $links);
	} else {
		$generalLinks->setEnabled(0, false);
	}

	$generalLinks->add('listDashboards.php', 'Dashboards &blacktriangledown;');

	$dashboardLinks = linksCollection();
	$listDashboards = getDashboards();

	if (!empty($listDashboards)) {
		foreach (getDashboards() as $dashboard) {
			$dashboardLinks->add('viewDashboard.php?id=' . $dashboard['id'], $dashboard['title']);
		}

		$dashboardLinks->addSeparator();
	}

	$dashboardLinks->add('viewServiceDashboard.php', 'Service Dashboard');
	$dashboardLinks->add('listDashboards.php', 'All Dashboards');

	$generalLinks->addChildCollection('Dashboards &blacktriangledown;', $dashboardLinks);
	
	$generalLinks->add('#', 'Services &blacktriangledown;');

	$generalLinksServices = linksCollection();
	$generalLinksServices->add('viewList.php', 'Service Definitions');
	$generalLinksServices->add('#', 'Searches');

	$generalLinksServicesList = linksCollection();
	$generalLinksServicesList->add('viewList.php?problems', 'Services With Problems');
	$generalLinksServicesList->add('viewList.php?ungrouped', 'Services Without Group');
	$generalLinksServices->addChildCollection('Searches', $generalLinksServicesList);

	$generalLinksServices->add('listGroups.php', 'Groups');
	$generalLinksServices->addSeparator();
	$generalLinksServices->add('listCommandDefinitions.php', 'Command Definitions');
	$generalLinksServices->add('listCommands.php', 'Command Metadata');
	$generalLinksServices->addSeparator();
	$generalLinksServices->add('listMaintPeriods.php', 'Maintenance Periods');
	$generalLinksServices->addSeparator();
	$generalLinksServices->add('listClasses.php', 'Classes');

	$generalLinks->addChildCollection('Services &blacktriangledown;', $generalLinksServices);

	$generalLinks->add('#', 'Nodes &blacktriangledown;');
	$generalLinksNodes = linksCollection();
	$generalLinksNodes->add('listNodes.php', 'List');
	$generalLinksNodes->add('listRemoteConfigurations.php', 'Configurations');
	$generalLinks->addChildCollection('Nodes &blacktriangledown;', $generalLinksNodes);

	if (Session::getUser()->getData('experimentalFeatures')) {
		$experimentalLinks = linksCollection();
		$experimentalLinks->add('viewTasks.php', 'Tasks');
		$experimentalLinks->add('viewRoom.php?id=1', 'Rooms');

		$generalLinks->add('#', 'Experimental');
		$generalLinks->addChildCollection('Experimental', $experimentalLinks);
	}

	$systemLinks = linksCollection();
	$systemLinks->addIf(Session::getUser()->getData('enableDebug'), 'viewDebugInfo.php', 'Debug');
	$systemLinks->add('preferences.php', 'Preferences');
	$systemLinks->addSeparator();
	$systemLinks->add('listUsergroups.php', 'Usergroups');
	$systemLinks->add('listUsers.php', 'Users');
	$systemLinks->add('listApiClients.php', 'API Clients');
	$systemLinks->add('settings.php', 'Settings');
	$systemLinks->addSeparator();
	$systemLinks->add('html5app.html', 'HTML5 Console (testing)');
	$systemLinks->addSeparator();
	$systemLinks->add('logout.php', 'Logout');

	$generalLinks->add('#', 'System &blacktriangledown;');
	$generalLinks->addChildCollection('System &blacktriangledown;', $systemLinks);
}

$tpl->assign('generalLinks', $generalLinks);

$tpl->display('header.tpl');

?>
