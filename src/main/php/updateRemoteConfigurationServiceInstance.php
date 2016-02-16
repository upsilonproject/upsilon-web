<?php

$title = 'Update remote configuration service instance';
require_once 'includes/common.php';

use \libAllure\Form;
use \libAllure\FormHandler;
use \libAllure\ElementInput;
use \libAllure\ElementSelect;

class UpdateRemoteConfigServiceInstance extends Form {
	public function __construct() {
		parent::__construct('UpdateRemoteConfig', 'Update remote config instance');

		$id = san()->filterUint('id');

		$sql = 'SELECT s.* FROM remote_config_allocated_services s WHERE s.id = :id';
		$stmt = stmt($sql);
		$stmt->bindValue(':id', $id);
		$stmt->execute();

		$service = $stmt->fetchRowNotNull();

		$this->addElementReadOnly('ID', $id, 'id');
		$this->addElementReadOnly('Config', $service['config'], 'config');
		$this->addElementService($service['service']);

		$this->addDefaultButtons();
	}

	public function addElementService($current) {
		$el = new ElementSelect('service', 'services');
		$el->addOption('(null)', null);
		$el->addOptions(array_column(getAllRemoteConfigServices(), 'name', 'id'));
		$el->setValue($current);

		$this->addElement($el);
	}


	public function process() {
		$sql = 'UPDATE remote_config_allocated_services SET id = id, service = :service  WHERE id = :id';
		$stmt = stmt($sql);
		$stmt->bindValue(':id', $this->getElementValue('id'));
		$stmt->bindValue(':service', $this->getElementValue('service'));
		$stmt->execute();
	}
}

$fh = new FormHandler('UpdateRemoteConfigServiceInstance');
$fh->setRedirect('viewRemoteConfig.php?id=' . san()->filterUint('config'));
$fh->handle();

?>
