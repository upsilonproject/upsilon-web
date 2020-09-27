<?php

require_once 'includes/common.php';

use \libAllure\FormHandler;

class FormRemoteConfigNodeAllocation extends \libAllure\Form {
	public function __construct() {
		parent::__construct('nodeAllocation', 'Allocated config to node');

		$this->addElementReadOnly('Config ID', san()->filterUint('id'), 'id');
		$this->addElementSelectNode();

		$this->addDefaultButtons('Allocate');
	}

	private function addElementSelectNode() {
		$el = new \libAllure\ElementSelect('node', 'Node');
		
		$nodes = getNodes();

		foreach ($nodes as $node) {
			$el->addOption($node['identifier']);
		}

		$this->addElement($el);
	}

	public function process() {
		allocateNodeToConfig($this->getElementValue('node'), $this->getElementValue('id'));
	}
}

$fh = new FormHandler('FormRemoteConfigNodeAllocation');
$fh->setRedirect('viewRemoteConfig.php?id=' . san()->filterUint('id'));
$fh->handle();

?>
