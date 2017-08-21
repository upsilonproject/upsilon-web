<?php

use \libAllure\Element;

class ElementFilteringSelect extends Element {
	private $filterTracker;

	public function __construct($name, $caption, \libAllure\FilterTracker $filters, $filterFunc) {
		parent::__construct($name, $caption);

		$this->filterTracker = $filters;
		$this->filterFunc = $filterFunc;
	}

	public function render() {
		global $tpl;

		echo '<label>' . $this->caption . '</label><div style = "display: inline-block; min-width: 80%;">';

		$tpl->assign('filters', $this->filterTracker->getAll());
		$tpl->assign('filterCallback', $this->filterFunc);
		$tpl->display('filters.tpl');

		echo '<br /><br /><select style = "width: 100%" multiple size = "10" id = "' . $this->name . '" name = "' . $this->name . '"></select>';
		echo '</div>';
		echo '<script>' . $this->filterFunc . '()</script>';
	}
}

?>
