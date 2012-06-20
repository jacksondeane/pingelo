<?php
require_once APP_PATH . 'lib/helpers/EloRating.php';
require_once APP_PATH . 'models/result.php';
require_once APP_PATH . 'models/session_helper.php';
require_once APP_PATH . 'lib/apis/google/apiClient.php';
require_once APP_PATH . 'lib/apis/google/contrib/apiOauth2Service.php';

class MainController {
	private $_user;
	private $_email;
	private $_authUrl;

	public $models = array('User');

	public function _preprocess() {
		//$this->_user = User::logged_in_user();
		$this->_email = SessionHelper::get_active_user();
		$this->_authUrl = SessionHelper::get_authUrl();
	}

	public function index() {
		error_log('MainController->index');
		/*
		$authUrl = null;
		$email = null;

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

			} else {
				//error_log('getAccessToken->createAuthUrl');
			  	$authUrl = $client->createAuthUrl();
			}

		} else {
			error_log('no [token]');
		}
		

		$authUrl = $client->createAuthUrl();
		*/

		$leaders = User::get_leaders();
		$last_20_results = Result::top(20);
		
		//session_destroy();
		
		Paraglide::render_view('main/index', array(
			'leaders' => $leaders,
			'last_20_results' => $last_20_results,
			'authUrl' => $this->_authUrl,
			'email' => $this->_email,
		));
	}

	public function logout() {
		
		error_log('logout');
		
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

		unset($_SESSION['token']);
		$client->revokeToken();

		$authUrl = $client->createAuthUrl();

		$leaders = User::get_leaders();
		$last_20_results = Result::top(20);
		Paraglide::redirect('main');
	}

}
