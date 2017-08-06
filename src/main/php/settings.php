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

class UserSettings extends Form {
	private $simpleSettings = array();

        public function __construct() {
                parent::__construct('userSettings', 'Settings');

                $this->addSection('General');

                $warn = getSiteSetting('warnNotUsingHttps');
                if ($warn == null) {
                        $warn = false;
                }

                $this->addElement(new ElementCheckbox('warnNotUsingHttps', 'Warn when not using HTTPS', $warn));
                $this->addElement(new ElementInput('siteTitle', 'Site title', getSiteSetting('siteTitle', 'Upsilon')));
                $this->addElement(new ElementInput('loginBanner', 'Login Page Banner', getSiteSetting('loginPageBanner', getSiteSetting('loginBanner'))));
				$this->getElement('loginBanner')->setMinMaxLengths(0, 512);

				$this->addSection('AMQP');
                $this->addElement(new ElementInput('amqpHost', 'AMQP Host', getSiteSetting('amqpHost', 'localhost')));
                $this->addElement(new ElementInput('amqpPort', 'AMQP Port', getSiteSetting('amqpPort', 5672)));
                $this->addElement(new ElementInput('amqpUser', 'AMQP User', getSiteSetting('amqpUser', 'guest')));
                $this->addElement(new ElementInput('amqpPass', 'AMQP Pass', getSiteSetting('amqpPass', 'guest')));

				$this->addSection('PHP Settings');
				if (ini_get('zlib.output_compression') != "1") {
					$this->addElementReadOnly('zlib Compression', 'Zlib compression is <span class = "bad">off</span>.');
				} else {
					$this->addElementReadOnly('zlib Compression', 'Zlib compression is <span class = "good">on</span>.');
				}

                
		$this->addSection('Other');

		$this->simpleSettings[] = array('configSourceIdentifier', 'Config Source Identifier', 'upsilon-web');
		$this->addSimpleSettings();

                $this->addDefaultButtons();
        }

	private function addSimpleSettings() {
		foreach ($this->simpleSettings as $setting) {
			list($key, $label, $def) = $setting;

			$this->addElement(new ElementInput($key, $label, getSiteSetting($key, $def)));
		}
	}

        public function process() {
                setSiteSetting('warnNotUsingHttps', $this->getElementValue('warnNotUsingHttps'));
                setSiteSetting('siteTitle', $this->getElementValue('siteTitle'));
                setSiteSetting('loginBanner', $this->getElementValue('loginBanner'));
                setSiteSetting('amqpHost', $this->getElementValue('amqpHost'));
                setSiteSetting('amqpPort', $this->getElementValue('amqpPort'));
                setSiteSetting('amqpUser', $this->getElementValue('amqpUser'));
                setSiteSetting('amqpPass', $this->getElementValue('amqpPass'));

		foreach ($this->simpleSettings as $setting) {
			list($key, $label, $def) = $setting;

			setSiteSetting($key, $this->getElementValue($key));
		}
        }
}

$handler = new FormHandler('UserSettings');
$handler->setRedirect('settings.php');
$handler->handle();

?>
