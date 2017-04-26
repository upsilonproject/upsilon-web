<?php

$title = 'Add group membership';
require_once 'includes/common.php';

use \libAllure\Form;
use \libAllure\FormHandler;
use \libAllure\Sanitizer;
use \libAllure\DatabaseFactory;
use \libAllure\ElementSelect;
use \libAllure\ElementHidden;

class FormAddMembership extends Form {
	public function __construct() {
		parent::__construct('formAddMembership', 'Form Add Membership');

		$classInstance = san()->filterInt('classInstance');
		if (!empty($classInstance)) {
			$this->addElement($this->getElementClassInstance($classInstance));
			$this->addType = 'classInstance';
		} else {
			$this->addElement($this->getElementService());
			$this->addType = 'service';
		}

		$this->addElementGroupSelect();
		
		$this->addDefaultButtons();
	}

	public function getElementClassInstance($classInstanceId) {
		$sql = 'SELECT ci.id, ci.title, group_concat(c.title) AS classList FROM class_instances ci LEFT JOIN class_instance_parents cp ON cp.instance = ci.id LEFT JOIN classes c ON cp.parent = c.id GROUP BY ci.id ORDER BY ci.title ASC';
		$stmt = db()->prepare($sql);
		$stmt->execute();

		$el = new ElementSelect('classInstance', 'Class Instance');
		$el->setSize(10);
		
		foreach ($stmt->fetchAll() as $classInstance) {
			$el->addOption($classInstance['title'] . ' - ' . $classInstance['classList'], $classInstance['id']);
		}

		$el->setValue($classInstanceId);

		return $el;
	}

	private function getElementService() {
		$sql = 'SELECT s.id, s.identifier, count(m.id) AS groups, s.node FROM services s LEFT JOIN service_group_memberships m ON m.`service` = s.identifier GROUP BY s.id ORDER BY s.node ASC, s.identifier, groups DESC, s.identifier ASC';
		$stmt = db()->prepare($sql);
		$stmt->execute();

		$el = new ElementSelect('serviceId', 'Service');
		$el->setSize(10);
		$el->multiple = true;

		foreach ($stmt->fetchall() as $service) {
			$el->addOption($service['node'] . '::' . $service['identifier'] . ' (' . $service['groups'] . ' groups)', $service['identifier']);
		}

		if (isset($_REQUEST['serviceId'])) {
			$el->setValue($_REQUEST['serviceId']);
		}

		return $el;
		
	}

	private function addElementGroupSelect() {
		$el = new ElementSelect('group', 'Group');
		
		$sql = 'SELECT g.id, g.title FROM service_groups g';
		$stmt = DatabaseFactory::getInstance()->prepare($sql);
		$stmt->execute();

		$this->groups = array();

		foreach ($stmt->fetchAll() as $group) {
			$el->addOption($group['title'], $group['id']);
			$this->groups[$group['id']] = $group['title'];
		}

		$groupFromRequest = san()->filterString('group');
		if (!empty($groupFromRequest)) {
			$el->setValue(array_search($groupFromRequest, $this->groups));
		}

		$el->description = ('If the group you want is not listed, <a href = "createGroup.php">create a new group</a>.');

		$this->addElement($el);
	}

	private function getService($id) {
		$sql = 'SELECT s.id, s.identifier FROM services s WHERE s.id = :sid';
		$stmt = DatabaseFactory::getInstance()->prepare($sql);
		$stmt->bindValue(':sid', $id);
		$stmt->execute();

		return $stmt->fetchRowNotNull();
	}

	public function process() {
		switch ($this->addType) {
			case 'service':
				$sql = 'INSERT INTO service_group_memberships (`group`, service) VALUES (:group, :service)';
				$stmt = DatabaseFactory::getInstance()->prepare($sql);
				$stmt->bindValue(':group', $this->groups[$this->getElementValue('group')]);

				$services = $this->getElementValue('serviceId');

				foreach ($services as $service) {
					$stmt->bindValue(':service', $service);
					$stmt->execute();
				}

				break;

			case 'classInstance':
				$sql = 'INSERT INTO class_instance_group_memberships (gid, class_instance) VALUES (:gid, :classInstance) ';
				$stmt = stmt($sql);
				$stmt->bindValue(':gid', $this->getElementValue('group'));
				$stmt->bindValue(':classInstance', $this->getElementValue('classInstance'));
				$stmt->execute();

				break;
		}

		// hack
		redirect('viewGroup.php?id=' . $this->getElementValue('group'), 'Membership Added');
	}

	public function getServiceId() {
		if (!empty($this->service['id'])) {
			return $this->service['id'];
		} else {
			return null;
		}
	}
}

$groups = getGroups();
if (empty($groups)) {
	redirect('listGroups.php', 'You need to create some groups.');
}

$fh = new FormHandler('FormAddMembership');
$fh->handle();

?>
