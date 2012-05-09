<?php
require_once APP_PATH . 'lib/apis/facebook.php';

class FacebookUser extends Paragon {
	protected static $_table = 'facebook_users';
	
	protected static $_belongs_to = array(
		'user' => 'User',
	);
	
	public $id;
	public $user_id;
	
	public $date_created;
	
	public $uid;
	
	private static function _facebook() {
		$fb_config = Paraglide::parse_config('facebook');
		$facebook = new Facebook(array(
			'appId' => $fb_config['app']['id'],
			'secret' => $fb_config['app']['secret'],
			'cookie' => true,
		));
		return $facebook;
	}
	
	public static function connect() {
		$facebook = self::_facebook();
		
		try {
			$me = $facebook->api('/me');
		} catch (Exception $e) {
			// nothing
		}

		if (!empty($me) && !empty($me['id'])) {
			$facebook_user = self::find_by_uid($me['id']);
			
			if ($facebook_user == null) {
				$user = new User();
				$user->active = true;
				$user->verified = true;
				$user->password = 1;
				$user->name = $me['name'];
				$user->sex = ($me['gender'] == 'male') ? 'm' : 'f';
//				$user->birthday = date('Y-m-d', strtotime($me['birthday']));
				$user->save();
				$facebook_user = new FacebookUser();
				$facebook_user->user_id = $user->id;
				$facebook_user->uid = $me['id'];
				$facebook_user->save();
			}

			$user = $facebook_user->user;
			
			if (!$user->verified) {
				$user->verified = true;
				$user->save();
			}
			
			if (empty($user->primary_email_id)) {
				$user->email = $me['email'];
				$user->save();
			}

			return $user;
		}
	
		return false;
	}
	
	public static function find_by_uid($uid) {
		return FacebookUser::find_one(array(
			'conditions' => array(
				'uid' => $uid,
			)
		));
	}
	
	public static function find_or_create_by_uid($uid) {
		$facebook_user = self::find_by_uid($uid);
	
		if (empty($facebook_user)) {
			$facebook = self::_facebook();
			
			try {
				$fb_user = $facebook->api('/' . $uid);
			} catch (Exception $e) {
				return false;
			}

			if (!empty($fb_user) && !empty($fb_user['id']) && !empty($fb_user['name'])) {
				$user = new User();
				$user->name = $fb_user['name'];
				$user->email = $fb_user['email'];
				$user->active = true;
				$user->verified = false;
				$user->password = 1;
				$user->save();
				$facebook_user = new FacebookUser();
				$facebook_user->user_id = $user->id;
				$facebook_user->uid = $uid;
				$facebook_user->save();
			}
		}
		
		return $facebook_user;
	}
	
	public static function login($redirect = true) {
		$user = self::connect();
		
		if (empty($user)) {
			return false;
		}
		
		$user->login($redirect);
	}
	
	public function friends() {
		$facebook = self::_facebook();
		
		try {
			$friends = $facebook->api('/me/friends');
		} catch (Exception $e) {
			return null;
			// nothing here
		}

		return $friends['data'];
	}
	
	public function is_friends_with($facebook_user) {
		$fb_config = Paraglide::parse_config('facebook');
		$facebook = new Facebook($fb_config['app']['api_key'], $fb_config['app']['secret']);
		
		try {
			$result = $facebook->api_client->friends_areFriends(array($this->uid), array($facebook_user->uid));

			return (!empty($result) && !empty($result[0]) && !empty($result[0]['are_friends']));
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function is_logged_in() {
		$facebook = self::_facebook();
		$session = $facebook->getSession();
		return !empty($session);
	}
	
	public function photo_url($type = 'normal') {
		$url = 'http://graph.facebook.com';
		$url .= '/' . $this->uid . '/picture';
		$url .= '?type=' . $type;
		return $url;
	}
	
	public function publish($message, $params = array()) {
		try {
			$facebook = self::_facebook();
			$path = '/' . $this->uid . '/feed';
			$params['message'] = $message;
			if (!isset($params['picture'])) $params['picture'] = 'http://www.sportit.com/logo.png';
			$result = $facebook->api($path, 'POST', $params);
		} catch (Exception $e) {
			// pot didn't go through, but whatever
			return false;
		}
		
		return true;
	}
}
