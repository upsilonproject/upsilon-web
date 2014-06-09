<?php

$title = 'Settings';
require_once 'includes/common.php';

require_once 'libAllure/FormHandler.php';

use \libAllure\FormHandler;
use \libAllure\Form;
use \libAllure\ElementInput;
use \libAllure\ElementPassword;
use \libAllure\ElementCheckbox;
use \libAllure\ElementNumeric;
use \libAllure\DatabaseFactory;
use \libAllure\Session;
use \libAllure\AuthBackend;

var_dump($settings);

class UserSettings extends Form {
        public function __construct() {
                parent::__construct('userSettings', 'Settings');

                $this->addSection('General');


                $warn = getSiteSetting('warnNotUsingHttps');
                if ($warn == null) {
                        $warn = false;
                }

                $this->addElement(new ElementCheckbox('warnNotUsingHttps', 'Warn when not using HTTPS', $warn));
                $this->addElement(new ElementInput('title', 'Site title', getSiteSetting('siteTitle', 'Upsilon')));

                $this->addDefaultButtons();
        }

        public function process() {
                setSiteSetting('warnNotUsingHttps', $this->getElementValue('warnNotUsingHttps'));
                setSiteSetting('siteTitle', $this->getElementValue('siteTitle'));
        }
}

$handler = new FormHandler('UserSettings');
$handler->setRedirect('settings.php');
$handler->handle();

?>
