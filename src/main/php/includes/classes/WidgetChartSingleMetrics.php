<?php

require_once 'includes/classes/Widget.php';

use \libAllure\ElementNumeric;
use \libAllure\ElementTextbox;

class WidgetChartSingleMetrics extends Widget {
	public function __construct() {
		parent::__construct();
	
		$this->instanceChartIndex = uniqid();

		$this->arguments['service'] = null;
		$this->arguments['metric'] = null;
	}

	public function render() {
		$ids = $this->getArgumentValue('service');

		global $tpl;
		$tpl->assign('service', $ids);
		$tpl->assign('metric', $this->getArgumentValue('metric'));
		
		$tpl->assign('instanceChartIndex', $this->instanceChartIndex);
		$tpl->display('widgetChartSingleMetrics.tpl');
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
