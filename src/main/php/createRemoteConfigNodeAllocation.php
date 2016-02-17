<?php

require_once 'includes/common.php';

use \libAllure\FormHandler;

class FormRemoteConfigNodeAllocation extends \libAllure\Form {
	public function __construct() {
		parent::__construct('nodeAllocation');

		$this->addElementReadOnly('Config ID', san()->filterUint('id'), 'id');
		$this->addElementSelectNode();

		$this->addDefaultButtons();
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
		$sql = 'INSERT INTO remote_config_allocated_nodes (node, config) VALUES (:node, :config)';
		$stmt = db()->prepare($sql);
		$stmt->bindValue(':node', $this->getElementValue('node'));
		$stmt->bindValue(':config', $this->getElementValue('id'));
		$stmt->execute();	
	}
}

$fh = new FormHandler('FormRemoteConfigNodeAllocation');
$fh->setRedirect('viewRemoteConfig.php?id=' . san()->filterUint('id'));
$fh->handle();

?>
