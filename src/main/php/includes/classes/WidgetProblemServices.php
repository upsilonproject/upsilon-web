<?php

require_once 'Widget.php';

use \libAllure\DatabaseFactory;

class WidgetProblemServices extends Widget {
	public function __construct() {
		$this->arguments['title'] = 'Problem Services';
	}

	public function getHeaderLink() {
		return 'viewList.php?problems';
	}

	public function render() {
		global $tpl;
		$tpl->assign('ref', rand());

		$tpl->assign('url', 'json/getServices?status=!GOOD');
		$tpl->assign('callback', 'renderServiceList');
		$tpl->assign('queryParams', json_encode(array()));
		$tpl->assign('repeat', 60000);
		$tpl->display('widgetAjax.tpl');
	}

	public function addLinks() {
		$this->links->add('viewList.php?problems', 'Services with problems');
	}
}

?>
