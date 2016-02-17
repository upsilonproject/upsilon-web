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
		$this->addDefaultButtons();
	}

	public function process() {
		$sql = 'INSERT INTO remote_configs (name) VALUES (:name)';
		$stmt = DatabaseFactory::getInstance()->prepare($sql);
		$stmt->bindValue(':name', $this->getElementValue('name'));
		$stmt->execute();
	}
}

$fh = new FormHandler('FormCreateRemoteConfig');
$fh->setRedirect('listRemoteConfigurations.php');
$fh->handle();

?>
