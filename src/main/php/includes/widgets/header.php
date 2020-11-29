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

savePageInHistory();

$tpl->assign('siteTitle', getSiteSetting('siteTitle', 'Upsilon'));

if (!empty($nav)) {
	$navTitle = '';

	for ($i = 0; $i < sizeof($nav); $i++) {
		$arg = $nav[$i];

		if (is_array($arg)) {
			$navTitle .= '<a href = "' . key($arg) . '">' . current($arg) . '</a>';
		} else {
			$navTitle .= ' ' . $arg . ' ';
		}

		if ($i +1 < sizeof($nav)) {
			$navTitle .= ' &raquo; ';
		}
	}

	$tpl->assign('navTitle', $navTitle);

	end($nav);

	if (is_array(current($nav))) {
		throw new Exception('Last element of nav breadcrumb must not be an array');
	}

	$tpl->assign('pageTitle', current($nav));
} else {
	if (isset($title)) {
		$tpl->assign('navTitle', $title);
		$tpl->assign('pageTitle', $title);
	} else {
		$tpl->assign('navTitle', 'Untitled page');
		$tpl->assign('pageTitle', 'Untitled page');
	}
}

$tpl->assign('loggedIn', Session::isLoggedIn());

$tpl->assign('crypto', isUsingSsl());
$tpl->assign('sessionOptions', sessionOptions());
$tpl->assign('datetime', date('D H:i'));
$tpl->assign('apiClient', isset($_SESSION['apiClient']) ? $_SESSION['apiClient'] : false);

$generalLinks = linksCollection('General links');
$userLinks = linksCollection('User links');

if (Session::isLoggedIn()) {

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
	$generalLinksServices->add('listServiceDefinitions.php', 'Services');
	$generalLinksServices->add('listGroups.php', 'Groups');

	$generalLinksServices->addSeparator();
	$generalLinksServices->add('viewList.php', 'Results: All');
	$generalLinksServices->add('viewList.php?problems', 'Results: Problems');
	$generalLinksServices->add('viewList.php?ungrouped', 'Results: Ungrouped');

	$generalLinksServices->addSeparator();
	$generalLinksServices->add('listCommandDefinitions.php', 'Commands');
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
	$systemLinks->add('listUsergroups.php', 'Usergroups');
	$systemLinks->add('listUsers.php', 'Users');
	$systemLinks->add('listApiClients.php', 'API Clients');
	$systemLinks->add('updateCatalog.php', 'Perform catalog update');
	$systemLinks->add('settings.php', 'Settings');
	$systemLinks->add('listLogs.php', 'Logs');
	$systemLinks->addSeparator();
	$systemLinks->add('html5app.html', 'HTML5 Console (testing)');

	$generalLinks->add('#', 'System &blacktriangledown;');
	$generalLinks->addChildCollection('System &blacktriangledown;', $systemLinks);


	$userLinks = linksCollection();
	$userPersonalLinks = linksCollection();
	$userPersonalLinks->add('feedback.php', 'Give feedback about Upsilon!');
	$userPersonalLinks->addSeparator();
	$userPersonalLinks->add('preferences.php', 'Preferences');
	$userPersonalLinks->add('logout.php', 'Logout');
	$userLinks->add('#', Session::getUser()->getUsername() . ' &blacktriangledown;');
	$userLinks->addChildCollection(Session::getUser()->getUsername() . ' &blacktriangledown;', $userPersonalLinks);
}



$tpl->assign('generalLinks', $generalLinks);
$tpl->assign('userLinks', $userLinks);
$tpl->assign('lang', getUiLanguage());

$tpl->display('header.tpl');

?>
