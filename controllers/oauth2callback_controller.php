<?php
require_once APP_PATH . 'lib/apis/google/apiClient.php';
require_once APP_PATH . 'lib/apis/google/contrib/apiOauth2Service.php';
require_once APP_PATH . 'lib/helpers/EloRating.php';
require_once APP_PATH . 'models/result.php';

class Oauth2callbackController {
	private $_user;
	
	public $models = array('User');

	public function _preprocess() {
		//$this->_user = User::logged_in_user();
	}

	public function index() {

		error_log('oauth2callbackController->index');

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


		if (isset($_GET['code'])) {
			if ($client->getAccessToken()) {

				error_log('Oauth2callbackController -> getAccessToken');
		  		$user = $oauth2->userinfo->get();

		  		// These fields are currently filtered through the PHP sanitize filters.
		  		// See http://www.php.net/manual/en/filter.filters.sanitize.php
		  	
		  		//$email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
		  		//$img = filter_var($user['picture'], FILTER_VALIDATE_URL);
		  		//$personMarkup = "$email<div><img src='$img?sz=50'></div>";

		  		// The access token may have been updated lazily.
		  		error_log('Oauth2callbackController -> setting session token');
		  		$_SESSION['token'] = $client->getAccessToken();

			} 

			//else {
			//	error_log('createAuthUrl');
			//  	$authUrl = $client->createAuthUrl();
			//}
		}


		/*
		if (isset($_GET['code'])) {
			error_log('authenticate');

		  	$client->authenticate();

		  	$_SESSION['token'] = $client->getAccessToken();
		  	$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
		  	header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));

		}

		if (isset($_SESSION['token'])) {
			error_log('HAVE TOKEN');
		 	$client->setAccessToken($_SESSION['token']);
		}

		if (isset($_REQUEST['logout'])) {
			error_log('logout');
		  	unset($_SESSION['token']);
		  	$client->revokeToken();
		}

		if ($client->getAccessToken()) {
			error_log('getAccessToken');
		  	$user = $oauth2->userinfo->get();

		  	// These fields are currently filtered through the PHP sanitize filters.
		  	// See http://www.php.net/manual/en/filter.filters.sanitize.php
		  	$email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
		  	$img = filter_var($user['picture'], FILTER_VALIDATE_URL);
		  	$personMarkup = "$email<div><img src='$img?sz=50'></div>";

		  	// The access token may have been updated lazily.
		  	$_SESSION['token'] = $client->getAccessToken();
		} else {
			error_log('createAuthUrl');
		  	$authUrl = $client->createAuthUrl();
		}
		*/
		
		Paraglide::redirect('main', 'index');

		/*
		Paraglide::render_view('main/index', array(
			//'breadcrumbs' => $this->_breadcrumbs,
			//'tabs' => $this->_tabs,
			//'title' => 'Admin Login',
			//'email' => $email,
			//'img' => $img,
			//'personMarkup' => $personMarkup,
		));
		*/
	}

}
