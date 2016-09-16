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

	public function canStartUpgrade() {
		foreach ($this->getTasks() as $task) {
			if (!$task->isPossible()) {
				return false;
			}
		}

		return true;
	}
}

abstract class UpgradeTask {
	public abstract function isNecessary();
	public abstract function isPossible();
	public abstract function perform();
	public function getName() {
		return get_class($this);
	}

	public function getDescription() {
		return '<strong>No description.</strong>';
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

	public function isPossible() {
		return true;
	}

	protected function doesTableExist($tbl) {
		try {
			$sql = 'DESC ' . $tbl;
			$stmt = stmt($sql);
			$stmt->execute();

			if ($stmt->numRows() == false) {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}

		return true;
	}
}

class NodeGroupMembershipTableExists extends DatabaseUpgradeTask {
	public function isNecessary() {
		return !$this->doesTableExist('node_group_memberships');
	}

	public function isPossible() {
		return true;
	}

	public function perform() {
		$sql = 'CREATE TABLE node_group_memberships (id int not null primary key auto_increment, gid int not null, node int not null)';
		$stmt = stmt($sql);
		$stmt->execute();
	}
}

Upgrader::registerTask(new NodeGroupMembershipTableExists());

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

class HttpdCanNetworkConnect extends UpgradeTask {
	public function isNecessary() {
		if (trim(`getenforce`) == 'Enforcing') {
			if (stripos(`getsebool httpd_can_network_connect`, '--> on') !== FALSE) {
				return false;
			} else {
				return true;
			}
		}

		return false;
	}

	public function isPossible() {
		return false;
	}

	public function getDescription() {
		return 'The webserver needs to be able to network connect to talk to the AMQP server. On Red Hat based machines, the sebool httpd_can_network_connect needs to be turned on.';
	}

	public function perform() {}
}

upgrader::registerTask(new HttpdCanNetworkConnect());

class NodesTableHasConfigsColumn extends DatabaseUpgradeTask {
	public function isNecessary() {
		return $this->doesFieldExistInTable('configs', 'nodes');
	}

	public function perform() {
		$sql = 'ALTER TABLE nodes ADD configs varchar(255) ';
		$stmt = stmt($sql);
		$stmt->execute();
	}
}

upgrader::registerTask(new NodesTableHasConfigsColumn());

?>
