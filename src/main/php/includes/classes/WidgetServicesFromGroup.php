<?php

require_once 'Widget.php';

use \libAllure\DatabaseFactory;
use \libAllure\ElementSelect;

class WidgetServicesFromGroup extends Widget {
	public function __construct() {
		$this->arguments['title'] = null;
		$this->arguments['group'] = null;
	}

	public function init() {
		$group = $this->getArgumentValue('group');

		if (!empty($group)) {
			$this->group = getGroup($this->getArgumentValue('group'));
		} else {
			$this->group = null;
		}
	}

	public function getHeaderLink() {
		if ($this->group == null) {
			return '#';
		} else {
			return 'viewGroup.php?id=' . $this->group['id'];
		}
	}

	public function getTitle() {
		$widgetTitle = $this->getArgumentValue('title');

		if (empty($widgetTitle)) {
 			if (empty($this->group['title'])) {
				return "Services from group";
			} else {
				return 'Group: ' . $this->group['title'];
			}
		} else {
			return $widgetTitle;
		}
	}

	public function render() {
		global $tpl;
		$tpl->assign('ref', rand());
		$tpl->assign('url', 'json/getGroup.php');
		$tpl->assign('queryParams', json_encode(array('id' => $this->getArgumentValue('group'))));
		$tpl->assign('callback', 'renderGroup');
		$tpl->assign('repeat', 60000);
		$tpl->display('widgetAjax.tpl');
	}

	public function addLinks() {
		if (!empty($this->group)) {
			$this->links->add('viewGroup.php?id=' . $this->group['id'], 'Group: ' . $this->group['title']);
		}
	}
}

?>
