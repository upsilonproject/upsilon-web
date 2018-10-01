<?php

$title = 'Add Candidate Coverage';
require_once 'includes/widgets/header.php';

class FormAddCandidateCoverage extends \libAllure\Form {
	public function __construct() {
		parent::__construct('addCandidateCoverage', 'Add Candidate Coverage');

		$candidate = getClassCandidate(san()->filterUint('candidate'));

		$this->addElementCandidate($candidate);
		$this->addElement(new \libAllure\ElementInput('name', 'Name', $candidate['externalAlias']));
		$this->addElementSelectClass();
	
		$this->addDefaultButtons('Instanciate');
	}

	private function addElementCandidate($candidate) {
		$this->addElementReadOnly('Candidate', $candidate['id'], 'candidate');
	}

	public function addElementSelectClass() {
		$el = new \libAllure\ElementSelect('Class', 'class');
		
		foreach (getClasses() as $class) {
			$el->addOption($class['title'], $class['id']);
		}

		$this->addElement($el);
	}

	public function process() {
		$sql = 'INSERT INTO class_instances (name) VALUES (:name)';
		$stmt = stmt($sql); 
		$stmt->bindValue(':name', $this->getElementValue('name'));
		$stmt->execute();

		$id = $stmt->insertId();

		redirect('viewClassInstance.php?id=' . $id);
	}
}

$handler = new \libAllure\FormHandler('FormAddCandidateCoverage');
$handler->handle();

require_once 'includes/widgets/footer.php';

?>
