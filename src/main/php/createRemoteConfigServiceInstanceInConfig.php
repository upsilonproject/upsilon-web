<?php 

$title = 'Allocate service in to config ';
require_once 'includes/common.php';
require_once 'includes/functions.remoteConfig.php';

use \libAllure\FormHandler;
use \libAllure\ElementSelect;

class FormCreateRemoteConfigServiceInstanceInConfig extends \libAllure\Form {
	public function __construct() {
		parent::__construct('createServiceInConfig', 'Allocate Service to Config');

		$service = getRemoteConfigService(san()->filterUint('serviceInstanceId'));

		$this->addElementHidden('serviceInstanceId', $service['id']);
		$this->addElementReadOnly('Service', $service['name']);
		$this->addElementNodeConfig();
		$this->addDefaultButtons('Allocate Service');
	}

	public function addElementNodeConfig() {
		$el = new ElementSelect('config', 'Config');

		$sql = 'SELECT c.id, c.name FROM remote_configs c ';
		$stmt = stmt($sql);
		$stmt->execute();

		foreach ($stmt->fetchAll() as $config) {
			$el->addOption($config['name'], $config['id']);
		}

		$this->addElement($el);
	}

	public function process() {
		$sql = 'INSERT INTO remote_config_allocated_services (config, service) VALUES (:config, :service) ';
		$stmt = stmt($sql);
		$stmt->bindValue(':config', $this->getElementValue('config'));
		$stmt->bindValue(':service', $this->getElementValue('serviceInstanceId'));
		$stmt->execute();

		touchConfigService($this->getElementValue('serviceInstanceId'), $this->getElementValue('config'), 'Service allocated to config');

		redirectToLast('addInstanceCoverage', ''); 
	}
}

$fh = new FormHandler('FormCreateRemoteConfigServiceInstanceInConfig');
$fh->setRedirect('index.php');
$fh->handle();

?>
