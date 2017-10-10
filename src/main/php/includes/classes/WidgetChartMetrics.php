<?php

require_once 'includes/classes/Widget.php';

use \libAllure\ElementNumeric;
use \libAllure\ElementTextbox;

class WidgetChartMetrics extends Widget {
	private static $graphIndex = 0;

	public function __construct() {
		parent::__construct();
	
		$this->instanceGraphIndex = self::$graphIndex++;

		$this->arguments['serviceList'] = null;
		$this->arguments['metric'] = null;
		$this->arguments['yAxisMarkings'] = null;
	}

	public function render() {
		$ids = $this->getArgumentValueArray('serviceList');

		global $tpl;
		$tpl->assign('listServiceId', $ids);
		$tpl->assign('metric', $this->getArgumentValue('metric'));

		$v = trim($this->getArgumentValue('yAxisMarkings'));
		if (empty($v)) {
			$v = array(); 
		} else {
			$v = explode("\n", $v);
		}

		$tpl->assign('yAxisMarkings', $v);
		$tpl->assign('instanceChartIndex', $this->instanceGraphIndex);
		$tpl->display('widgetChartMetric.tpl');

	}

	public function getArgumentFormElement($optionName) {
		switch ($optionName) {
		case 'height':
			return new ElementNumeric($optionName, 'Height');
		case 'yAxisMarkings':
			return new ElementTextbox($optionName, 'Y Axis Markings');
		default:
			return parent::getArgumentFormElement($optionName);
		}
	}

	public function addLinks() {
		$servicesMenu = linksCollection();

		foreach ($this->getArgumentValueArray('serviceList') as $service) {
			$servicesMenu->add('viewService.php?id=' . $service, 'Service ' . $service);
		}

		$this->links->add(null, 'Services');
		$this->links->addChildCollection('Services', $servicesMenu);
	}
}


?>
