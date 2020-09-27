<?php

require_once 'includes/common.php';
require_once 'includes/functions.remoteConfig.php';
require_once 'includes/classes/ElementFilteringSelect.php';

setNav(array('listServiceDefinitions.php' => 'Service definitions'), 'Update remote configuration service');

use \libAllure\Form;
use \libAllure\FormHandler;
use \libAllure\ElementInput;
use \libAllure\ElementInputRegex;

class UpdateRemoteConfigService extends Form {
	private $arguments;

	private $saveButtonTitle = 'Save service &amp; arguments';

	public function __construct() {
		parent::__construct('UpdateRemoteConfig', 'Update remote config service');

		$id = san()->filterUint('id');

		$this->addElementReadOnly('id', $id, 'id');
		
		$sql = 'SELECT s.* FROM remote_config_services s WHERE s.id = :id';
		$stmt = stmt($sql);
		$stmt->bindValue(':id', $id);
		$stmt->execute();

		$service = $stmt->fetchRowNotNull();
		$this->remoteService = $service;
		$this->serviceId = $id;

		$elName = $this->addElement(new ElementInputRegex('name', 'Name', $service['name']));
		$elName->setPatternToIdentifier('.-');
		$this->addElement(new ElementInput('parent', 'Parent', $service['parent']));
		$this->addElementCommand($service['command']);

		$this->addArgumentElements($service['command']);

		$this->addDefaultButtons($this->saveButtonTitle);
	}

	private function getUsefulInfo($id) {
		$ret = array();

	}

	private function addElementCommand($command) {
		$el = new ElementFilteringSelect('command', 'Command', getFilterCommands(), 'filterCommands');
		$el->setValue($command);
		
		$this->addElement($el);
	}

	public function addArgumentElements($commandId) {
		$this->arguments = array();

		if (empty($commandId)) {
			$this->addElementReadOnly('Note', 'Command is not yet set. Once it has been and saved, command arguments can be set here.');
			$this->saveButtonTitle = 'Save and add arguments';
			return;
		}

		$sql = 'SELECT a.id, a.name FROM remote_config_command_arguments a WHERE a.command = :commandId';
		$stmt = db()->prepare($sql);
		$stmt->bindValue(':commandId', $commandId);
		$stmt->execute();

		$this->addSection('Command argument values');
		if ($stmt->numRows() == 0) {
			$this->addElementReadOnly('', 'This check command does not have any arguments.');
		} else { 
			foreach ($stmt->fetchAll() as $argument) {
				$this->arguments[$argument['name']] = $argument['id'];

				$el = new ElementInput($argument['name'], $argument['name']);
				$el->setMinMaxLengths(0, 512);
				$this->addElement($el);
			}

			foreach (getServiceArgumentValues($this->serviceId) as $argumentName => $argumentValue) {
				try {
					$this->getElement($argumentName)->setValue($argumentValue);
				} catch (Exception $e) {
				}
			}
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


		$sql = 'SELECT alc.config AS id, c.autoSendOnUpdate FROM remote_config_allocated_services alc RIGHT JOIN remote_configs c ON alc.config = c.id WHERE alc.service = :serviceId ';
		$stmt = stmt($sql);
		$stmt->bindValue('serviceId', $this->getElementValue('id'));
		$stmt->execute();

		foreach ($stmt->fetchAll() as $touchedConfigs) {
			updateConfig($touchedConfigs['id'], 'Updated service: ' . $this->getElementValue('name') . ' in config ' . $touchedConfigs['id']);

			if ($touchedConfigs['autoSendOnUpdate']) {
				$nodesUsingConfigs = getNodesUsingConfig($touchedConfigs['id']);

				foreach ($nodesUsingConfigs as $node) {
					sendUpdatedConfig($touchedConfigs['id'], $node['identifier']);
				}
			}
		}
	}

	private function processArguments() {
		$sql = 'DELETE FROM remote_config_service_arg_values WHERE service = :service';
		$stmt = db()->prepare($sql);
		$stmt->bindValue(':service', $this->serviceId);
		$stmt->execute();

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

$links = linksCollection();
$links->add('deleteRemoteConfigurationService.php?id=' . san()->filterId(), 'Delete remote configuration service');

$f = new UpdateRemoteConfigService();

if ($f->validate()) {
	$f->process();
	redirect('updateRemoteConfigurationService.php?id=' . san()->filterUint('id'));
}

require_once 'includes/widgets/header.php';

$tpl->assign('listNodes', getNodesUsingRemoteService(san()->filterUint('id')));
$tpl->assign('service', $f->remoteService);
$tpl->display('updateServiceOverview.tpl');

$tpl->assignForm($f);
$tpl->display('form.tpl');

require_once 'includes/widgets/footer.php';

?>
