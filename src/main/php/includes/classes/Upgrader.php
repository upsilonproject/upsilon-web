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

	protected function tableHasUniqueKey($table, $name) {
		$sql = 'SHOW CREATE TABLE ' . $table;
		$stmt = stmt($sql);
		$res = $stmt->execute()->fetchRow();
		$res = $res['Create Table'];

		if (stripos($res, "UNIQUE KEY `" . $name . "`") === FALSE) {
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
		return !$this->doesFieldExistInTable('configs', 'nodes');
	}

	public function perform() {
		$sql = 'ALTER TABLE nodes ADD configs varchar(255) ';
		$stmt = stmt($sql);
		$stmt->execute();
	}
}

upgrader::registerTask(new NodesTableHasConfigsColumn());

class RemoteConfigCommandsUnique extends DatabaseUpgradeTask {
	public function isNecessary() {
		if ($this->tableHasUniqueKey('remote_config_commands', 'identifier')) {
			return false;
		} else {
			return true;
		}

		var_dump($res);

		return true;
	}

	public function perform() {
		$sql = 'ALTER TABLE remote_config_commands ADD UNIQUE(identifier) ';
		stmt($sql)->execute();
	}
	
	public function isPossible() {
		return true;
	}
}

upgrader::registerTask(new RemoteConfigCommandsUnique());

class RemoteConfigCommandArgumentsUnique extends DatabaseUpgradeTask {
	public function isNecessary() {
		return !$this->tableHasUniqueKey('remote_config_command_arguments', 'unq_cmd_arg');
	}

	public function perform() {
		$sql = 'ALTER TABLE remote_config_command_arguments ADD UNIQUE unq_cmd_arg (command, name)';
		stmt($sql)->execute();
	}
}

upgrader::registerTask(new RemoteConfigCommandArgumentsUnique());

class CommandMetadataUnique extends DatabaseUpgradeTask {
	public function isNecessary() {
		return !$this->tableHasUniqueKey('command_metadata', 'unq_cmd');
	}

	public function perform() {
		$sql = 'ALTER TABLE command_metadata ADD UNIQUE unq_cmd (commandIdentifier)';
		stmt($sql)->execute();
	}
}

upgrader::registerTask(new CommandMetadataUnique());

class LoggerTable extends DatabaseUpgradeTask {
	public function isNecessary() {
		return $this->tableExists('logs');
	}

	public function perform() {
		$sql = 'CREATE TABLE logs (id int not null primary key auto_increment, userId int, usergroupId int, serviceResultId int, nodeId int, nodeConfigId int, serviceDefinitionId int, commandDefinitionId int, classId int, dashboardId int, serviceGroupId int');
		$stmt = stmt($sql);
		$stmt->execute();
	}
}

upgrader::registerTask(new LoggerTable());

?>
