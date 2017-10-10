<?php

class BestPractices {
	private $practices = array();
	private $attrs = array();

	public function getCount() {
		return count($this->practices);
	}

	public function checkAll() {
		$this->checkHasCommands();
		$this->checkOnlyWidget();
	}

	public function getAll() {
		return $this->practices;
	}

	public function set($k, $v) {
		$this->attrs[$k] = $v;
	}

	public function newPractice($message) {
		$this->practices[] = array(
			'message' => $message
		);
	}

	public function checkOnlyWidget() {
		if (isset($this->attrs['dashboardWidgetCount'])) {
			if ($this->attrs['dashboardWidgetCount'] == 1) {
				$this->newPractice('This is the only widget on the dashboard. Go to <strong>Actions</strong> &raquo; <strong>Add Widget</strong> to add more.');
			}
		}
	}

	public function checkHasCommands() {
		if (count(getCommands()) == 0) {
			$this->newPractice('There are no commands defined. You probably should <a href = "updateCatalog.php">perform a catalog update</a>.');
		}
	}
}

?>
