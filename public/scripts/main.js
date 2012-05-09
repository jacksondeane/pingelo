$(function() {
	FB.init({
		appId: facebook_app_id
		,status: true
		,cookie: true
		,xfbml: true
	});

	Shadowbox.init({
		skipSetup: true
	});
});

var facebook_connect_onlogin = function(return_url) {
	var url = base_url + 'account/login?connect=1&redirect=' + encodeURIComponent(return_url);
	FB.login(function(response) {
		var permissions = response.perms.split(',');
		var permitted_email = false;
		var permitted_publish_stream = false;
			
		for (var p in permissions) {
			var permission = permissions[p];
			
			if (permission == 'email') {
				permitted_email = true;
				break;
			} else if (permission == 'publish_stream') {
				publish_stream = true;
				break;
			}
		}
		
		if (!permitted_email) {
			alert('You must give email permissions on Facebook to use Stakehouse. Please try again.');
			return;
		}

		window.location = url;
	}, {perms: 'email,publish_stream'});
};
