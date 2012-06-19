<?php
require_once APP_PATH . 'lib/apis/twitter/EpiTwitter.php';
require_once APP_PATH . 'lib/apis/twitter/EpiCurl.php';
require_once APP_PATH . 'lib/apis/twitter/EpiOAuth.php';

Class Twitter {
	
	private static function _twitter() {	
		if (!empty($GLOBALS['data']['twitter_consumer_key']) && !empty($GLOBALS['data']['twitter_consumer_secret'])) {
			$twitter = new EpiTwitter($GLOBALS['data']['twitter_consumer_key'], $GLOBALS['data']['twitter_consumer_secret']);
			return $twitter;
		}
		return false;
	}
	
	public static function getAuthenticateUrl() {
		$twitter = self::_twitter();
		return $twitter->getAuthenticateUrl();
	}
	
	public function setToken($token = null, $secret = null) {
		try {
			$twitter = self::_twitter();
		} catch (Exception $e) { 	
			return false;
		}
		//$twitter = self::_twitter();
		return $twitter->getAuthenticateUrl($token, $secret);
	}
	
	public function getAccessToken($params = null) {
		$twitter = self::_twitter();
		return $twitter->getAccessToken($params);
	}
	
	public function postStatusUpdate($status = null) {
		$twitter = self::_twitter();
		return $twitter->post_statusesUpdate(array('status' => $status));
	}
} 