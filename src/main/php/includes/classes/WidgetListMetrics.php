<?php

require_once 'includes/classes/Widget.php';

use \libAllure\ElementNumeric;
use \libAllure\ElementCheckbox;

class WidgetListMetrics extends Widget {
    public function __construct() {
        parent::__construct();
        $this->arguments['service'] = null;
        $this->arguments['metricsTitle'] = null;

        $this->defineWidgetArgument('lastUpdatedShort', 'Last Updated (short display)', 'checkbox');
        $this->defineWidgetArgument('serviceDetail', 'Service detail', 'checkbox');
        $this->defineWidgetArgument('hideMetricsTitle', 'Hide metrics title', 'checkbox');
    }

    public function init() {
        try {
            $this->service = getServiceById($this->getArgumentValue('service'));
        } catch (Exception $e) {
            $this->service = null;
        }

        parseOutputJson($this->service);
    }

    public function render() {
        global $tpl;

        if ($this->service == null) {
            $tpl->assign('message', 'Service is not set. <a href = "updateWidgetInstance.php?id=' . $this->id. '">Set Service</a> ');
            $tpl->display('message.tpl');
        } else {
            $tpl->assign('service', $this->service);

            foreach ($this->widgetArguments as $name => $arg) {
                $tpl->assign($name, $this->getArgumentValue($name));
            }

            $tpl->display('widgetListMetrics.tpl');
        }
    }

    public function addLinks() {
        if ($this->service == null) {
            return;
        }

        $this->links->add('viewService.php?id=' . $this->service['id'], 'Service: ' . $this->service['identifier']);
    }

    public function getHeaderLink() {
        if ($this->service == null) {
            return '';
        }

        return 'viewService.php?id=' . $this->service['id'];
    }
}

?>
