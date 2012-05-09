<?php
class Mailer {
	public static function send($params) {
		$mail_config = Web::parse_config('mail');
		$from_email = $mail_config['default']['address'];
		$from_name = $mail_config['default']['name'];
		
		$from = array($from_email => $from_name);
		$subject = $params['subject'];
		$to = is_array($params['to']) ? $params['to'] : array($params['to']);
		$message = $params['message'];
		require_once APP_PATH . 'lib/classes/swift-mailer/swift_required.php';
		$transport = Swift_SendmailTransport::newInstance();
		$mailer = Swift_Mailer::newInstance($transport);
		$message = Swift_Message::newInstance()
					->setSubject($subject)
					->setFrom($from)
					->setTo($to)
					->setBody($message, 'text/html')
					;
		$mailer->send($message);
		return true;
	}
}
