<?php

use \libAllure\ElementSelect;
use \libAllure\ElementCheckbox;
use \libAllure\ElementInput;
use \libAllure\DatabaseFactory;
use \libAllure\HtmlLinksCollection;

require_once 'includes/classes/ElementFilteringSelect.php';

class Widget {
    public $dashboard = 0;
    public $id = -1;
    public $group = -1;
    public $service = -1;
    public $links = null;

    protected $arguments = array();

    protected $widgetArguments = array();

    public function __construct() {
        $this->arguments['title'] = get_class($this);
    }

    public function getTitle() {
        return $this->getArgumentValue('title');
    }

    public function getHeaderLink() {
        return '#';
    }

    public function defineWidgetArgument($name, $caption, $type) {
        $this->widgetArguments[$name] = array (
            'name' => $name,
            'caption' => $caption,
            'type' => $type,
        );

        $this->arguments[$name] = null;
    }

    public function loadArguments($id) {
        $this->id = $id;

        $sql = 'SELECT name, value FROM widget_instance_arguments WHERE instance = :id';
        $stmt = DatabaseFactory::getInstance()->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        foreach ($stmt->fetchAll() as $arg) {
            $this->setArgument($arg['name'], $arg['value']);
        }
    }

    public function render() {
        echo 'Empty Widget!';
    }

    private function getFormElementService($multi = false) {
        $filters = getFilterServices();

        if (!$multi) {
            $el = new ElementFilteringSelect('service', 'Service', $filters, 'filterService');
        } else if ($multi) {
            $el = new ElementSelect('serviceList', 'Services');
            $el->setSize(5);
            $el->multiple = true;
        } else {
            throw new Exception('No service arg in widget');
        }

        $sql = 'SELECT s.id, s.identifier FROM services s';
        $stmt = DatabaseFactory::getInstance()->prepare($sql);
        $stmt->execute();

        foreach ($stmt->fetchAll() as $service) {
            #               $el->addOption($service['identifier'], $service['id']);
        }

        return $el;
    }

    public function getArguments() {
        return $this->arguments;
    }

    public function setArgument($key, $value) {
        $this->arguments[$key] = $value;
    }

    public function getArgumentValue($key) {
        if (!isset($this->arguments[$key])) {
            return null;
        }

        $val = $this->arguments[$key];

        return $val;
    }

    public function getArgumentValueArray($key) {
        $val = $this->getArgumentValue($key);
        $val = explode(';', $val);

        return $val;
    }

    private function getGroupSelectionElement($group = null) {
        $el = new ElementSelect('group', 'Group');

        foreach (getGroups() as $group) {
            $el->addOption($group['name'], $group['id']);
        }

        return $el;
    }

    public function getArgumentFormElement($optionName) {
        if (isset($this->widgetArguments[$optionName])) {
            $arg = $this->widgetArguments[$optionName];

            switch ($arg['type']) {
            case 'checkbox':
                return new ElementCheckbox($optionName, $arg['caption']);
            default:
                throw new Exception('Unhandled type ' . $arg['type']);
            }
        }

        switch ($optionName) {
        case 'serviceList':
            return $this->getFormElementService(true);
        case 'service':
            return $this->getFormElementService(false);
        case 'group':
            return $this->getGroupSelectionElement();
        default:
            $input = new ElementInput($optionName, ucwords($optionName), null);
            $input->setMinMaxLengths(0, 128);

            return $input;
        }
    }

    protected function addLinks() {}

        public function getLinks() {
            if (!isset($this->links)) {
                $this->links = new HtmlLinksCollection();
                $this->addLinks();
                $this->links->add('updateWidgetInstance.php?id=' . $this->id, 'Update');
                $this->links->add('deleteWidgetInstance.php?id=' . $this->id, 'Delete');
            }

            return $this->links;
        }

    public function init() {}

        public function isShown() {
            return true;
        }
}
?>
