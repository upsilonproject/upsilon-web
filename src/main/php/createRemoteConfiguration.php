<?php

$title = 'Create remote configuration';
require_once 'includes/common.php';

use \libAllure\Form;
use \libAllure\FormHandler;
use \libAllure\ElementInput;
use \libAllure\DatabaseFactory;

class FormCreateRemoteConfig extends Form {
	public function __construct() {
		parent::__construct('formCreateRemoteConfig', 'Create remote config');

		$this->addElement(new ElementInput('name', 'Name'));

		$node = san()->filterString('node');

		if ($node != null) {
			$this->addElementReadOnly('Node', $node, 'node');
			$this->getElement('name')->setValue('Config for ' . $node);
		}

		$this->addDefaultButtons();
	}

	public function process() {
		$sql = 'INSERT INTO remote_configs (name) VALUES (:name)';
		$stmt = DatabaseFactory::getInstance()->prepare($sql);
		$stmt->bindValue(':name', $this->getElementValue('name'));
		$stmt->execute();

		$configId = DatabaseFactory::getInstance()->lastInsertId();

		$node = $this->getElementValue('node');

		if (!empty($node)) {
			allocateNodeToConfig($node, $configId);
		}

		global $fh;
		$fh->setRedirect('viewRemoteConfig.php?id=' . $configId);
	}
}

$fh = new FormHandler('FormCreateRemoteConfig');
$fh->setRedirect('listRemoteConfigurations.php');
$fh->handle();

?>
