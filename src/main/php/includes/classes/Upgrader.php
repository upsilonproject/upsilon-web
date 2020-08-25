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


?>
