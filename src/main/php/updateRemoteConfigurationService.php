<?php

$title = 'Update remote configuration service';
require_once 'includes/common.php';
require_once 'includes/functions.remoteConfig.php';

use \libAllure\Form;
use \libAllure\FormHandler;
use \libAllure\ElementInput;
use \libAllure\ElementSelect;

class UpdateRemoteConfigService extends Form {
	private $arguments;

	public function __construct() {
		parent::__construct('UpdateRemoteConfig', 'Update remote config');

		$id = san()->filterUint('id');

		$sql = 'SELECT s.* FROM remote_config_services s WHERE s.id = :id';
		$stmt = stmt($sql);
		$stmt->bindValue(':id', $id);
		$stmt->execute();

		$config = $stmt->fetchRowNotNull();
		$this->remoteConfig = $config;
		$this->serviceId = $id;

		$this->addElementReadOnly('Useful info', $this->getUsefulInfo($id));
		$this->addElementReadOnly('Service ID', $id, 'id');
		$this->addElement(new ElementInput('name', 'Name', $config['name']));
		$this->addElement(new ElementInput('parent', 'Parent', $config['parent']));
		$this->addElementCommand($config['command']);

		$this->addSection('Arguments');
		$this->addArgumentElements($config['command']);

		$this->addDefaultButtons();
	}

	private function getUsefulInfo($id) {
		$ret = '';

		$nodes = getNodesUsingRemoteService($id);

		foreach ($nodes as $node) {
			$ret .= 'Node: <a href = "viewNode.php?id=' . $node['id'] . '">' . $node['identifier'] . '</a>';
		}

		return $ret;
	}

	private function addElementCommand($command) {
		$el = new ElementSelect('command', 'Command', $command);

		foreach (getAllCommands() as $command) {
			$el->addOption($command['identifier'], $command['id']);
		}

		$this->addElement($el);
	}

	public function addArgumentElements($commandId) {
		$this->arguments = array();

		$sql = 'SELECT a.id, a.name FROM remote_config_command_arguments a WHERE a.command = :commandId';
		$stmt = db()->prepare($sql);
		$stmt->bindValue(':commandId', $commandId);
		$stmt->execute();

		foreach ($stmt->fetchAll() as $argument) {
			$this->arguments[$argument['name']] = $argument['id'];

			$el = new ElementInput($argument['name'], $argument['name']);
			$el->setMinMaxLengths(0, 512);
			$this->addElement($el);
		}

		foreach (getServiceArgumentValues($this->serviceId) as $argumentName => $argumentValue) {
			$this->getElement($argumentName)->setValue($argumentValue);
		}
	}

	public function process() {
		$sql = 'UPDATE remote_config_services SET name = :name, command = :command, parent = :parent WHERE id = :id';
		$stmt = stmt($sql);
		$stmt->bindValue(':id', $this->getElementValue('id'));
		$stmt->bindValue(':name', $this->getElementValue('name'));
		$stmt->bindValue(':command', $this->getElementValue('command'));
		$stmt->bindValue(':parent', $this->getElementValue('parent'));
		$stmt->execute();

		$this->processArguments();
	}

	private function processArguments() {
		$sql = 'INSERT INTO remote_config_service_arg_values (service, argument, `value`) VALUES (:service, :argument, :valueInsert) ON DUPLICATE KEY UPDATE `value` = :valueUpdate ';
		$stmt = db()->prepare($sql);

		foreach ($this->arguments as $argumentName => $argumentId) {
			$stmt->bindValue(':service', $this->serviceId);
			$stmt->bindValue(':argument', $argumentId);
			$stmt->bindValue(':valueInsert', $this->getElementValue($argumentName));
			$stmt->bindValue(':valueUpdate', $this->getElementValue($argumentName));
			$stmt->execute();
		}
	}
}

$fh = new FormHandler('UpdateRemoteConfigService');
$fh->setRedirect('updateRemoteConfigurationService.php?id=' . san()->filterUint('id'));
$fh->handle();

?>
