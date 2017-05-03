<?php

require_once 'includes/common.php';

class WidgetViewClassInstances extends Widget {
	public function __construct() {
		parent::__construct();
		$this->arguments['classInstanceId'] = null;
	}

	public function init() {
		try {
			if ($this->arguments['classInstanceId'] == null) {
				throw new Exception('Class Instnace ID is not set.');
			}

			$this->classInstance = getClassInstance($this->arguments['classInstanceId']);
			$this->listRequirements = getInstanceRequirements($this->arguments['classInstanceId']);
		} catch (Exception $e) {
			var_dump($e->getMessage());
			$this->classInstance = null;
		}
	}

	public function render() {
		global $tpl;

		if ($this->arguments['classInstanceId'] == null) {
			$tpl->assign('message', 'Class Instance is not set.');
			$tpl->display('message.tpl');
		} else if ($this->classInstance == null) {
			$tpl->assign('message', 'Failed to load classInstance.');
			$tpl->display('message.tpl');
		} else {
			$tpl->assign('classInstance', $this->classInstance);
			$tpl->assign('listRequirements', $this->listRequirements);
			$tpl->display('widgetViewClassInstance.tpl');
		}
	}
}

?>
