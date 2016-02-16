<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire;

class UpsilonMessage extends AMQPMessage {
	private $headers = array();
	public function __construct($type, $body = null) {
		parent::__construct($body, array(
			'reply_to' => 'upsilon-web-recv'
		));

		$this->headers['upsilon-msg-type'] = $type;
	}

	public function addHeader($key, $value) {
		$this->headers[$key] = $value;
	}

	function publish($routingKey = 'upsilon.cmds') {
		global $amqpChan;

		$headerTable = new Wire\AMQPTable($this->headers);

		$this->set('application_headers', $headerTable);

		$amqpChan->basic_publish($this, 'ex_upsilon', $routingKey);
	}
}

//$amqpConn = new AMQPStreamConnection('www2.teratan.net', 5672, 'guest', 'guest');
$host = getSiteSetting('amqpHost');
$port = intval(getSiteSetting('amqpPort'));
$user = getSiteSetting('amqpUser');
$pass = getSiteSetting('amqpPass');
$amqpConn = new AMQPStreamConnection($host, $port, $user, $pass);
$amqpChan = $amqpConn->channel();

