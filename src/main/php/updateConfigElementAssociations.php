<?php

require_once 'includes/common.php';

use \libAllure\FormHandler;
use \libAllure\Form;
use \libAllure\ElementSelect;
use \libAllure\ElementInput;
use \libAllure\FormHelperDoubleList;

class FormUpdateConfigElementAssociations extends Form {
	public function __construct() {
		parent::__construct('updateConfigElementAssociations');
		
		$this->addElementReadOnly('Config', san()->filterUint('config'), 'config');

		$this->addElementCommands();
		$this->addElementServices();


		$this->addDefaultButtons();
		$this->getElement('submit')->setCaption('Add selected');
	}


	public function process() {
		$sql = 'INSERT INTO remote_config_allocated_commands (config, command) VALUES (:config, :command) ';
		$stmt = db()->prepare($sql);

		$command = $this->getElement('associatedCommands')->getValue();

		if ($command != null) {
			$stmt->bindValue(':config', $this->getElementValue('config'));
			$stmt->bindValue(':command', $command);
			$stmt->execute();
		}

		$sql = 'INSERT INTO remote_config_allocated_services (config, service) VALUES (:config, :service) ';
		$stmt = db()->prepare($sql);

		$service = $this->getElement('associatedServices')->getValue();

		if ($service != null) {
			$stmt->bindValue(':config', $this->getElementValue('config'));
			$stmt->bindValue(':service', $service);
			$stmt->execute();
		}
	}
}

$f = new FormHandler('FormUpdateConfigElementAssociations');
$f->setRedirect('viewRemoteConfig.php?id=' . san()->filterUint('config'));
$f->handle();

?>
