<?php
//require_once APP_PATH . 'models/bet.php';
//require_once APP_PATH . 'models/transaction.php';
//require_once APP_PATH . 'models/user_email.php';
//require_once APP_PATH . 'models/user_role.php';
require_once APP_PATH . 'lib/apis/google/apiClient.php';
require_once APP_PATH . 'lib/apis/google/contrib/apiOauth2Service.php';

class SessionHelper  {
	//protected static $_table = 'users';
	/*
	public $id;
	
	public $date_created;
	public $date_updated;
	
	public $elo_rank = 100;
	public $twitter_username;
	public $profile_image_url;
	
	public $num_games = 0;
	public $num_wins = 0;
	*/

	public static function get_active_user() {
		session_start();
		$client = new apiClient();
		$client->setApplicationName("Pingelo");
		// Visit https://code.google.com/apis/console?api=plus to generate your
		// oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.

		$client->setClientId('191654827151.apps.googleusercontent.com');
		$client->setClientSecret('cX8eLDSE5mdFY6BXwzz1pXmn');
		$client->setRedirectUri('https://www.pingelo.com/oauth2callback');
		$client->setDeveloperKey('AIzaSyCOPD8gAKBEm4guY-o-TS8HOl600Zm3BHs');

		$oauth2 = new apiOauth2Service($client);


		if (isset($_SESSION['token'])) {
			
			$client->setAccessToken($_SESSION['token']);

			if ($client->getAccessToken()) {
				//error_log('getAccessToken');
				error_log('MainController-> have getAccessToken');

			  	$user = $oauth2->userinfo->get();
			  	$email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
			  	//$img = filter_var($user['picture'], FILTER_VALIDATE_URL);

			  	// The access token may have been updated lazily.
			  	$_SESSION['token'] = $client->getAccessToken();
			  	$_SESSION['email'] = $email;

			}
		} else {
			error_log('no [token]');
		}

		return $email;

	}

	public static function get_authUrl() {

		session_start();
		$client = new apiClient();
		$client->setApplicationName("Pingelo");
		$client->setClientId('191654827151.apps.googleusercontent.com');
		$client->setClientSecret('cX8eLDSE5mdFY6BXwzz1pXmn');
		$client->setRedirectUri('https://www.pingelo.com/oauth2callback');
		$client->setDeveloperKey('AIzaSyCOPD8gAKBEm4guY-o-TS8HOl600Zm3BHs');

		$oauth2 = new apiOauth2Service($client);

		$authUrl = $client->createAuthUrl();

		return $authUrl;
	}
}
