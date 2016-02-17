<?php

$title = 'Update remote configuration command instance';
require_once 'includes/common.php';

use \libAllure\Form;
use \libAllure\FormHandler;
use \libAllure\ElementInput;
use \libAllure\ElementSelect;

class UpdateRemoteConfigCommand extends Form {
	public function __construct() {
		parent::__construct('UpdateRemoteConfig', 'Update remote config command instance');

		$id = san()->filterUint('id');

		$sql = 'SELECT c.* FROM remote_config_allocated_commands c WHERE id = :id';
		$stmt = stmt($sql);
		$stmt->bindValue(':id', $id);
		$stmt->execute();

		$command = $stmt->fetchRowNotNull();
		$this->remoteCommand = $command;

		$this->addElementReadOnly('ID', $id, 'id');
		$this->addElementReadOnly('Config', $command['config'], 'config');
		$this->addElementCommands($command['command']);

		$this->addDefaultButtons();
	}

	public function addElementCommands($current) {
		$el = new ElementSelect('command', 'Commands');
		$el->addOption('(null)', null);
		$el->addOptions(array_column(getAllCommands(), 'identifier', 'id'));
		$el->setValue($current);

		$this->addElement($el);
	}

	public function process() {
		$sql = 'UPDATE remote_config_allocated_commands SET id = id, command = :command WHERE id = :id';
		$stmt = stmt($sql);
		$stmt->bindValue(':id', $this->remoteCommand['id']);
		$stmt->bindValue(':command', $this->getElementValue('command'));
		$stmt->execute();
	}
}

$fh = new FormHandler('UpdateRemoteConfigCommand');
$fh->setRedirect('viewRemoteConfig.php?id=' . san()->filterUint('config'));
$fh->handle();

?>
