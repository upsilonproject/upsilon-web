<?php

require_once 'Widget.php';
require_once 'includes/classes/BestPractices.php';

class WidgetBestPractices extends Widget {
	public function render() {
		global $tpl;

		$bestPractices = new BestPractices();
		$bestPractices->set('dashboardWidgetCount', count($this->dashboard->getWidgetInstances()));
		$bestPractices->checkAll();

		$ret = '';

		if ($bestPractices->getCount() == 0) {
			$ret = 'Everything looks fine!';
		} else {
			foreach ($bestPractices->getAll() as $practice) {
				$ret .= '<div class = "warning metricIndicator">-</div><div class = "metricText">' . $practice['message'] . '</div><br /><br />';
			}
		}

		$tpl->assign('messageTitle', $bestPractices->getCount() . ' issues(s) found.');
		$tpl->assign('message', $ret);
		$tpl->display('message.tpl');
	}

	public function getTitle() {
		return "Best Practices";
	}

}

?>
