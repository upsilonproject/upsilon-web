<?php

$title = 'Add instance coverage';
require_once 'includes/common.php';
require_once 'includes/classes/ElementFilteringSelect.php';

use \libAllure\Form;
use \libAllure\Sanitizer;
use \libAllure\DatabaseFactory;
use \libAllure\ElementSelect;
use \libAllure\FormHandler;
use \libAllure\ElementButton;

class FormUpdateInstanceCoverage extends Form {
	public function __construct() {
		parent::__construct('update', 'Update instance coverage');

		$instId = Sanitizer::getInstance()->filterUint('instance');
		$inst = $this->getClassInstance($instId);

		$reqId = Sanitizer::getInstance()->filterUint('requirement');
		$req = $this->getRequirement($reqId);

		$this->addElementHidden('instance', Sanitizer::getInstance()->filterUint('instance'));
		$this->addElementReadOnly('Instance title', $inst['title']);

		$this->addElementHidden('requirement', Sanitizer::getInstance()->filterUint('requirement'));
		$this->addElementReadOnly('Requirement', $req['title']);
		$this->addElementSelectServiceCheck($req, $inst);
		$this->addDefaultButtons('Associate');
	}

	private function getClassInstance($id) { 
		$sql = 'SELECT i.* FROM class_instances i WHERE i.id = :id';
		$stmt = DatabaseFactory::getInstance()->prepare($sql);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
	
		return $stmt->fetchRow();
	}

	private function getRequirement($id) { 
		$sql = 'SELECT r.* FROM class_service_requirements r WHERE r.id = :id';
		$stmt = DatabaseFactory::getInstance()->prepare($sql);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
	
		return $stmt->fetchRow();
	}

	private function addElementSelectServiceCheck($requirement, $inst) {
		$filters = instanceCoverageFilter();

		$el = new ElementFilteringSelect('service', 'Service', $filters, 'filterInstanceCoverageOptions');
		$el->description = 'Cannot find an existing service? <a href = "createRemoteConfigService.php?commandId=' . $requirement['command'] . '&requirementId=' . $requirement['id'] . '&classInstanceId=' . $inst['id'] . '">Create</a>';

		$this->addElement($el);
	}

	public function process() {
		$sql = 'DELETE FROM class_service_assignments WHERE instance = :instance AND requirement = :requirement ';
		$stmt = DatabaseFactory::getInstance()->prepare($sql);
		$stmt->bindValue(':instance', $this->getElementValue('instance'));
		$stmt->bindValue(':requirement', $this->getElementValue('requirement'));
		$stmt->execute();

		if ($this->getElementValue('service') != '') {
			$sql = 'INSERT INTO class_service_assignments(instance, requirement, service) values (:instance, :requirement, :service)';
			$stmt = DatabaseFactory::getInstance()->prepare($sql);
			$stmt->bindValue(':instance', $this->getElementValue('instance'));
			$stmt->bindValue(':requirement', $this->getElementValue('requirement'));
			$stmt->bindValue(':service', $this->getElementValue('service'));
			$stmt->execute();
		}
	}
}

$instId = Sanitizer::getInstance()->filterUint('instance');
$fh = new FormHandler('FormUpdateInstanceCoverage');
$fh->setRedirect('viewClassInstance.php?id=' . $instId);
$fh->handle();

?>
