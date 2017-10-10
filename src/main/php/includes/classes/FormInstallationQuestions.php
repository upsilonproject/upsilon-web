<?php

use \libAllure\Form;
use \libAllure\ElementAlphaNumeric;
use \libAllure\ElementEmail;
use \libAllure\ElementPassword;
use \libAllure\ElementHtml;
use \libAllure\ElementInput;
use \libAllure\Database;

class FormInstallationQuestions extends Form {
        public function __construct() {
                parent::__construct('formInstallation', 'Generate config.php - installation questions');

                $this->addSection('Database');

		        if ($this->isDatabaseEnvVarsSpecified()) {
					$this->addElement(new ElementHtml('dialog', 'Database credentials provided', '<p>Your database credentials have been provided by environment variables, so the installer has completed the database section of the installer. You still need to setup your initial administrator.</p>'));

					$this->addElement(new ElementInput('dsn', 'DSN', getenv('CFG_DB_DSN')));
					$this->addElementReadOnly('Database user', getenv('CFG_DB_USER'), 'dbUser');
					$this->addElementReadOnly('Database pass', getenv('CFG_DB_PASS'), 'dbPass');
                
				} else {
					$this->addElement(new ElementInput('dbHost', 'Database host or unix socket', 'localhost'));
                	$this->getElement('dbHost')->setMinMaxLengths(0, 128);
	                $this->addElement(new ElementAlphaNumeric('dbName', 'Database name', 'upsilon'));
    	            $this->addElement(new ElementAlphaNumeric('dbUser', 'Database username'));
					$this->addElement(new ElementPassword('dbPass', 'Database user password'));
					$this->getElement('dbPass')->setOptional(true);
				}

                $this->addSection('Administrator');
                $this->addElement(new ElementAlphaNumeric('adminUsername', 'First Admin Username', 'admin'));
                $this->addElement(new ElementPassword('adminPassword1', 'First Admin Password'));
                $this->addElement(new ElementPassword('adminPassword2', 'First Admin Password (confirm)'));

				$this->requireFields('adminUsername', 'adminPassword1', 'adminPassword2');

                $this->addDefaultButtons('Start install');
        }

        public function getDsn() {
		        if ($this->isDatabaseEnvVarsSpecified()) {
					$dsn = $this->getElementValue('dsn');
				} else { 
					$hostOrSocket = $this->getElementValue('dbHost');

					if (stripos($hostOrSocket, '/') !== FALSE) {
							$dsn = 'mysql:unix_socket=' . $hostOrSocket . ';dbname=' . $this->getElementValue('dbName');
					} else {
							$dsn = 'mysql:host=' . $hostOrSocket . ';dbname=' . $this->getElementValue('dbName');
					}
				}


                return $dsn;
        }

        public function isDatabaseEnvVarsSpecified() {
                $dsn = getenv('CFG_DB_DSN');
                return !empty($dsn);
        }

        public function validateExtended() {
                $this->validateDatabase();
                $this->validateAdministrator();
        }

        private function validateAdministrator() {
                $password1 = $this->getElementValue('adminPassword1');
                $password2 = $this->getElementValue('adminPassword2');

                if ($password1 != $password2) {
                        $this->getElement('adminPassword2')->setValidationError('The passwords do not match.');
                }
        }

		private function getElementForDatabaseError() {
				$el = null;
				try {
					$el = $this->getElement('dbName');
				} catch (Exception $e2) {
					$el = $this->getElement('dsn');
				}

				return $el;
		}

        private function validateDatabase() {
				$el = $this->getElementForDatabaseError();

                try {
                        $this->validateDatabaseConnection();
                } catch (Exception $e) {
                        $el->setValidationError('Could not connect to database: ' . $e->getMessage());

                        return;
                }

                try {
                        $this->validateDatabaseTables();
                } catch (Exception $e) {
                        $el->setValidationError('Settings table does not exist. Did you import the setup/databases/schema.sql file?');
                        return;
                }

                $this->validateDatabaseInitialData();
        }

        private function validateDatabaseConnection() {
                $dbUser = $this->getElementValue('dbUser');
                $dbPass = $this->getElementValue('dbPass');

                $this->db = new Database($this->getDsn(), $dbUser, $dbPass);              
        }

        private function validateDatabaseTables() {
                $sql = 'DESC settings';
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
        }

        private function validateDatabaseInitialData() {
                $sql = 'SELECT s.* FROM settings s';
                $stmt = $this->db->prepare($sql);
                $stmt->execute();

                $settings = $stmt->fetchAll();

                if (count($settings) == 0) {
                        $this->getElementForDatabaseError()->setValidationError('There is nothing in the settings table. Did you import the setup/databases/initialData.sql file?');
                }
        }

        public function process() {
				$this->saltPrefix = getenv('CFG_PASSWORD_SALT');

				if (empty($this->saltPrefix)) {
					$this->saltPrefix = uniqid();
				}

                try {
                        $this->createAdministratorAccount();
                        $this->writeConfigFile();
                } catch (Exception $e) {
                        global $tpl;
                        $tpl->assign('configFailReason', $e->getMessage());
                        return;
                }

                redirect('index.php', 'upsilon-web installed.'); // We wrote the config file okay, redirect.
        }

        private function writeConfigFile() {
                $writeCfg = @file_put_contents('includes/config.php', $this->generateConfigFile());

                if ($writeCfg === false) {
                        throw new Exception('Could not write config file, file_put_contents returned false');
                }
        }

        // This method is exceedingly messy. Improvements to the technique are welcome.
        public function generateConfigFile() {
                $ret = '';
                $ret .= "<?php\n";
                $ret .= "date_default_timezone_set('" . date_default_timezone_get() . "');\n";
                $ret .= "ini_set('display_errors', 'on');\n";
                $ret .= "ini_set('session.gc_maxlifetime', '31557600');\n";
                $ret .= "\n";
                $ret .= "define('CFG_DB_DSN', '{$this->getDsn()}');\n";
                $ret .= "define('CFG_DB_USER', '{$this->getElementValue('dbUser')}');\n";
                $ret .= "define('CFG_DB_PASS', '{$this->getElementValue('dbPass')}');\n";
                $ret .= "\n// The following is configuration for advanced users only.\n";
                $ret .= "define('CFG_PASSWORD_SALT', '" . $this->saltPrefix . "'); // If you change this value, you will break all existing user passwords.  \n";
                $ret .= '?' . '>';

                return $ret;
        }

        private function createAdministratorAccount() {
                $sql = 'TRUNCATE users';
                $this->db->query($sql);

                $sql = 'INSERT INTO users (username, password, `group`) VALUES (:adminUsername, :adminPassword, 1) ';
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':adminUsername', $this->getElementValue('adminUsername'));
                $stmt->bindValue(':adminPassword', sha1($this->saltPrefix . $this->getElementValue('adminPassword1')));
                $stmt->execute();
        }

}
?>
