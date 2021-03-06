<?php

require_once 'includes/classes/Widget.php';

use \libAllure\ElementNumeric;
use \libAllure\ElementCheckbox;

class WidgetListMetrics extends Widget {
	public function __construct() {
		parent::__construct();
		$this->arguments['service'] = null;
		$this->arguments['serviceDetail'] = null;
		$this->arguments['lastUpdatedShort'] = null;
		$this->arguments['metricsTitle'] = null;
	}

	public function init() {
		try {
			$this->service = getServiceById($this->getArgumentValue('service'));
		} catch (Exception $e) {
			$this->service = null;
		}

		parseOutputJson($this->service);
	}

	public function render() {
		global $tpl;

		if ($this->service == null) {
			$tpl->assign('message', 'Service is not set. <a href = "updateWidgetInstance.php?id=' . $this->id. '">Set Service</a> ');
			$tpl->display('message.tpl');
		} else {
			$tpl->assign('service', $this->service);
			$tpl->assign('lastUpdatedShort', $this->getARgumentValue('lastUpdatedShort'));
			$tpl->assign('serviceDetail', $this->getArgumentValue('serviceDetail'));
			$tpl->assign('metricsTitle', $this->getArgumentValue('metricsTitle'));
			$tpl->display('widgetListMetrics.tpl');
		}
	}

	public function getArgumentFormElement($name) {
		switch ($name) {
		case 'lastUpdatedShort':
			return new ElementCheckbox('lastUpdatedShort', 'Last Updated (short display)');
		case 'serviceDetail':
			return new ElementCheckbox('serviceDetail', 'Service detail');
		default:
			return parent::getArgumentFormElement($name);
		}

		$metric = getSingleServiceMetric($this->service, ['karma']);

		return !empty($this->service['metrics']);
	}

	public function addLinks() {
		$this->links->add('viewService.php?id=' . $this->service['id'], 'Service: ' . $this->service['identifier']);
	}

	public function getHeaderLink() {
		return 'viewService.php?id=' . $this->service['id'];
	}
}

?>
