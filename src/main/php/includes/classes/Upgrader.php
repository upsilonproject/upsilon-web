<?php

class Upgrader {
	private static $tasks = array();

	public function getTasks() {
		$ret = array();

		foreach (self::$tasks as $task) {
			if ($task->isNecessary()) {
				$ret[] = $task;
			}
		}

		return $ret;
	}

	public static function registerTask(UpgradeTask $task) {
		self::$tasks[] = $task;
	}

	public function doUpgrade() {
		foreach (self::$tasks as $task) {
			if ($task->isNecessary() && $task->isPossible()) {
				$task->perform();
			}
		}
	}

	public function isUpgradeNeeded() {
		$tasks = $this->getTasks();

		if (empty($tasks)) {
			return false;
		} else {
			return true;
		}
	}
}

abstract class UpgradeTask {
	public abstract function isNecessary();
	public abstract function isPossible();
	public abstract function perform();
	public function getName() {
		return get_class($this);
	}
}

abstract class DatabaseUpgradeTask extends UpgradeTask {
	protected function doesFieldExistInTable($field, $table) {
		$sql = 'DESC ' . $table .  ' :field';
		$stmt = stmt($sql);
		$stmt->bindValue(':field', $field);
		$stmt->execute();

		if ($stmt->numRows() == 0) {
			return false;
		} else {
			return true;
		}
	}
}

/**
class UsersNeedCake extends DatabaseUpgradeTask {
	public function isNecessary() {
		return !$this->doesFieldExistInTable('cake', 'users');
	}

	public function isPossible() {
		return true;
	}

	public function perform() {
		$sql = 'ALTER TABLE users ADD cake varchar(32)';
		$stmt = stmt($sql);
		$stmt->execute();
	}

};

Upgrader::registerTask(new UsersNeedCake());
*/
?>
