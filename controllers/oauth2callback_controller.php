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
			error_log('Oauth2callbackController -> have [code]');
			
			error_log('Oauth2callbackController -> authenticate');
		  	$client->authenticate();
		  	
		  	error_log('Oauth2callbackController -> setting SESSION[token]');
		  	$_SESSION['token'] = $client->getAccessToken();
		  	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);

		  	
			if ($client->getAccessToken()) {
				error_log('Oauth2callbackController -> getAccessToken');
		  		$user = $oauth2->userinfo->get();

		  		// These fields are currently filtered through the PHP sanitize filters.
		  		// See http://www.php.net/manual/en/filter.filters.sanitize.php
		  	
		  		$email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
		  		error_log('email= ' . $email);

		  		$domain = substr(strrchr($email, "@"), 1);
		  		error_log('domain= ' . $domain);

		  		if ($domain == 'thumb.it') {
		  			// The access token may have been updated lazily.
		  			error_log('Oauth2callbackController -> setting session token');
		  			$_SESSION['token'] = $client->getAccessToken();	
		  		}
		  		

			}
			
		} 

		error_log('Oauth2callbackController->redirecting to main');
		Paraglide::redirect('main');
	}

}
