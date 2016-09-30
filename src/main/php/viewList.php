<?php

$title = 'List of Services';
require_once 'includes/widgets/header.php';

use \libAllure\DatabaseFactory;

class FilterTracker {
	private $filters = array();
	private $vars = array();

	private function add($name, $type, $label = null, $requestVar = null) {
		if ($requestVar == null) {
			$requestVar = $name;
		}

		if ($label == null) {
			$label = ucwords($name);
		}

		$this->types[$name] = $type;
		$this->vars[$name] = $requestVar;
		$this->labels[$name] = $label; 

	}

	public function addInt($name, $label = null, $requestVar = null) {
		$this->add($name, 'int', $label, $requestVar);
	}

	public function addBool($name, $label = null, $requestVar = null) {
		$this->add($name, 'bool', $label, $requestVar = null);
	}

	public function addString($name, $label = null, $requestVar = null) {
		$this->add($name, 'string', $label, $requestVar);
	}

	public function isUsed($name) {
		if (isset($_REQUEST[$this->vars[$name]])) {
			if ($this->types[$name] != 'bool' && empty($_REQUEST[$this->vars[$name]])) {
				return false;
			}

			return true;
		}

		return false;
	}

	public function getAll() {
		$ret = array();

		foreach ($this->vars as $name => $value) {
			$ret[] = array(
				'name' => $name,
				'isUsed' => $this->isUsed($name),
				'type' => $this->types[$name],
				'value' => $this->getValue($name),
				'label' => $this->labels[$name]
			);
		}

		return $ret;
	}

	public function getValue($name) {
		if ($this->isUsed($name)) {
			if ($this->types[$name] == "bool") {
				return true;
			} else {
				return $_REQUEST[$name];
			}
		}

		return false;
	}
}

$filters = new FilterTracker();
$filters->addBool('problems', 'Problems');
$filters->addBool('ungrouped');
$filters->addInt('maintPeriod', 'Maintenance Period');
$filters->addString('name');

$qb = new \libAllure\QueryBuilder();
$qb->from('services')->fields('id', 'identifier', 'output', 'description', 'lastUpdated', 'karma', 'secondsRemaining', 'node');

if ($filters->isUsed('problems')) {
	$qb->whereNotEquals('karma', 'good');
}

if ($filters->isUsed('ungrouped'))  {
	$qbGroupMemberships = new \libAllure\QueryBuilder();
	$qbGroupMemberships->from('service_group_memberships', 'g')->fields('service');

	$qb->whereSubquery('s.identifier', 'NOT IN', $qbGroupMemberships);
} 

if ($filters->isUsed('maintPeriod')) {
	$id = san()->filterUint('maintPeriod');

	$qb->leftJoin('service_metadata', 'm')->on('s.identifier', 'm.service');
	$qb->whereEquals('m.acceptableDowntimeSla', $id);

	$activeFilters[] = 'Maint Period';
}

if ($filters->isUsed('name')) {
	$qb->where('identifier', 'LIKE', '"%' . $filters->getValue('name') . '%"');
}

$stmt = DatabaseFactory::getInstance()->prepare($qb->build());
$stmt->execute();
$listServices = $stmt->fetchAll();

$tpl->assign('filters', $filters->getAll());
$tpl->assign('listServices', $listServices);
$tpl->display('listServices.tpl');

require_once 'includes/widgets/footer.php';

?>
