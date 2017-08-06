<?php

require_once 'includes/common.php';

use \libAllure\Form;
use \libAllure\FormHandler;
use \libAllure\ElementInputRegex;

class CreateCommandArgument extends Form {
	public function __construct() {
		parent::__construct('createCommandArgument', 'Create Command Argument');

		$this->addElementReadOnly('Command', san()->filterUint('command'), 'command');
		$this->addElement(new ElementInputRegex('name', 'Name'));
		$this->getElement('name')->setPatternToIdentifier();

		$this->addDefaultButtons();
	}

	public function process() {
		$sql = 'INSERT INTO remote_config_command_arguments (command, name) VALUES (:command, :name) ';
		$stmt = db()->prepare($sql);
		$stmt->bindValue(':command', $this->getElementValue('command'));
		$stmt->bindValue(':name', $this->getElementValue('name'));
		$stmt->execute();
	}
}

$fh = new FormHandler('CreateCommandArgument');
$fh->setRedirect('updateRemoteConfigurationCommand.php?id=' . san()->filterId());
$fh->handle();

?>
