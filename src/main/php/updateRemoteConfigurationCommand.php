<?php

$title = 'Update remote configuration command';
require_once 'includes/common.php';
require_once 'includes/functions.remoteConfig.php';

use \libAllure\Form;
use \libAllure\FormHandler;
use \libAllure\ElementInput;
use \libAllure\ElementSelect;
use \libAllure\HtmlLinksCollection;

class UpdateRemoteConfigCommand extends Form {
	public function __construct() {
		parent::__construct('UpdateRemoteConfig', 'Update remote config command');

		$id = san()->filterUint('id');

		$sql = 'SELECT c.* FROM remote_config_commands c WHERE id = :id';
		$stmt = stmt($sql);
		$stmt->bindValue(':id', $id);
		$stmt->execute();

		$config = $stmt->fetchRowNotNull();
		$this->remoteCommand = $config;

		$this->addElementReadOnly('ID', $id, 'id');
		$this->addElement(new ElementInput('identifier', 'Identifier', $this->remoteCommand['identifier']));
		$this->addElement(new ElementInput('command_line', 'Command Line', $this->remoteCommand['command_line']));
		$this->getElement('command_line')->setMinMaxLengths(0, 1024);
		$this->addElement($this->getElementMetadataSelection($this->remoteCommand['metadata']));

		$this->addDefaultButtons();
	}

	private function getElementMetadataSelection($currentVal) {
		$el = new ElementSelect('metadata', 'Metadata');
		$el->addOption('(none)', null);

		$sql = 'SELECT m.commandIdentifier, m.id FROM command_metadata m';
		$metadata = db()->prepare($sql)->execute()->fetchAll();

		foreach ($metadata as $row) {
			$el->addOption($row['commandIdentifier'], $row['id']);
		}

		$el->setValue($currentVal);

		return $el;
	}

	public function process() {
		$sql = 'UPDATE remote_config_commands SET identifier = :identifier, metadata = :metadata, command_line = :command_line WHERE id = :id';
		$stmt = stmt($sql);
		$stmt->bindValue(':id', $this->getElementValue('id'));
		$stmt->bindValue(':identifier', $this->getElementValue('identifier'));
		$stmt->bindValue(':metadata', $this->getElementValue('metadata'));
		$stmt->bindValue(':command_line', $this->getElementValue('command_line'));
		$stmt->execute();
	}
}

$links = new HtmlLinksCollection('Dashboard &nabla;');
$links->add('deleteRemoteConfigurationCommand.php?id=' . san()->filterUint('id'), 'Delete');

$fh = new FormHandler('UpdateRemoteConfigCommand');
$fh->setRedirect('listRemoteConfigurations.php');
$fh->showFooter = false;
$fh->handle();

$tpl->assign('commandId', san()->filterUint('id'));
$tpl->assign('arguments', getCommandArguments(san()->filterUint('id')));
$tpl->display('commandArguments.tpl');

require_once 'includes/widgets/footer.php';

?>
