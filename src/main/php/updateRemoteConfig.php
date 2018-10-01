<?php

$title = 'Update remote configuration service';
require_once 'includes/common.php';
require_once 'includes/functions.remoteConfig.php';

use \libAllure\FormHandler;
use \libAllure\Form;
use \libAllure\ElementCheckbox;
use \libAllure\ElementInput;

class FormUpdateRemoteConfig extends Form {
	public function __construct() {
		$id = san()->filterId();

		$this->addElementReadOnly('id', $id, 'id');

		$config = getConfigById($id);

		$this->addElement(new ElementInput('name', 'Name', $config['name']));
		$this->addElement(new ElementCheckbox('autoSendOnUpdate', 'Auto send on element update?', $config['autoSendOnUpdate']));

		$this->addDefaultButtons('Save');
	}

	public function process() {
		$sql = 'UPDATE remote_configs SET autoSendOnUpdate = :autoSend, name = :name WHERE id = :id';
		$stmt = stmt($sql);
		$stmt->bindValue(':id', $this->getElementValue('id'));
		$stmt->bindValue(':name', $this->getElementValue('name'));
		$stmt->bindValue(':autoSend', $this->getElementValue('autoSendOnUpdate'));
		$stmt->execute();
	}
}

$handler = new FormHandler('FormUpdateRemoteConfig');
$handler->handle();

redirect('listRemoteConfigurations.php');

?>
