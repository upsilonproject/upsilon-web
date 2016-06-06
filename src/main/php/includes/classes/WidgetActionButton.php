<?php

require_once 'Widget.php';

class WidgetActionButton extends Widget {
	public function render() {
		global $tpl;

		$tpl->assign('buttonText', 'untitled button');
		$tpl->assign('buttonUrl', '?url');
		$tpl->display('widgetActionButton.tpl');
	}
}

?>
