<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire;

class UpsilonMessage extends AMQPMessage {
	public function __construct($type, $body = null) {
		parent::__construct($body, array(
			'reply_to' => 'upsilon-web-recv'
		));

		$headers = new Wire\AMQPTable(array(
			'upsilon-msg-type' => 'REQ_NODE_SUMMARY'
		));

		$this->set('application_headers', $headers);
	}

	function publish() {
		global $amqpChan;

		$amqpChan->basic_publish($this, 'ex_upsilon', 'upsilon.cmds');
	}
}

//$amqpConn = new AMQPStreamConnection('www2.teratan.net', 5672, 'guest', 'guest');
$host = getSiteSetting('amqpHost');
$port = intval(getSiteSetting('amqpPort'));
$user = getSiteSetting('amqpUser');
$pass = getSiteSetting('amqpPass');
$amqpConn = new AMQPStreamConnection($host, $port, $user, $pass);
$amqpChan = $amqpConn->channel();

