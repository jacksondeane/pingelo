<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="background: transparent;">
<head>
<title>Admin</title>
<link rel="stylesheet" type="text/css" href="<?= SITE_ROOT ?>styles/reset.css" />
<link rel="stylesheet" type="text/css" href="<?= SITE_ROOT ?>scripts/shadowbox/shadowbox.css" />
<link rel="stylesheet" type="text/css" href="<?= SITE_ROOT ?>styles/form.css" />
<link rel="stylesheet" type="text/css" href="<?= SITE_ROOT ?>styles/admin.css" />
<link rel="stylesheet" type="text/css" href="<?= SITE_ROOT ?>styles/admin.modal.css" />

<script type="text/javascript" src="<?= SITE_ROOT ?>scripts/jquery.js"></script>
<script type="text/javascript" src="<?= SITE_ROOT ?>scripts/jquery.qtip.js"></script>

<script type="text/javascript" src="<?= SITE_ROOT ?>scripts/shadowbox/shadowbox.js"></script>

<script src="http://connect.facebook.net/en_US/all.js" type="text/javascript"></script>
<script type="text/javascript">
var base_url = '<?= SITE_URL ?>';
var controller_action = '<?= Paraglide::$action ?>';
var modal = true;
var stay_modal = <?= !empty($stay_modal) ? 1 : 0 ?>;

$(function() {
	$('a').live('click', function() {
		var url = $(this).attr('href');
		
		if (url.indexOf('?') == -1) {
			url += '?modal=1';
		} else {
			url += '&modal=1';
		}
		
		window.location = url;
		return false;
	});
	$('form').live('submit', function() {
		if (stay_modal) {
			if ($(this).attr('method').toLowerCase() == 'get') {
				$('<input type="hidden" name="modal" value="1" />').appendTo(this);
			}
			
			return true;
		}
		
		var action = $(this).attr('action') || location.href;
		var new_action = '';

		if (action.indexOf('?') == -1) {
			new_action = action;
		} else {
			new_action = action.substring(0, action.indexOf('?') + 1);
			var params_string = action.substr(action.indexOf('?') + 1);
			var params = params_string.split('&');
			
			for (var key in params) {
				var param_array = params[key].split('=');
				if (param_array[0] == '') continue;
				if (param_array[0] == 'modal') continue;
				new_action += params[key];
			}
			
			new_action = action.substring(0, new_action.length);
		}
		
		$(this).attr({
			action: new_action
			,target: '_parent'
		});
	});
	$('a#cancel').live('click', function() {
		parent.Shadowbox.close();
		return false;
	});

	if (parent && parent.modalEvents) {
		parent.modalEvents(window);
	}
});
</script>

<script type="text/javascript" src="<?= SITE_ROOT ?>scripts/main.js"></script>

<?php if (file_exists(APP_PATH . 'public/scripts/helpers/' . Paraglide::$controller . '.js')): ?>
	<script type="text/javascript" src="<?= SITE_ROOT ?>scripts/helpers/<?= Paraglide::$controller ?>.js"></script>
<?php endif; ?>

<?php if (file_exists(APP_PATH . 'public/styles/helpers/' . Paraglide::$controller . '.css')): ?>
	<link rel="stylesheet" type="text/css" href="<?= SITE_ROOT ?>styles/helpers/<?= Paraglide::$controller ?>.css"></script>
<?php endif; ?>
</head>
<body id="<?= Paraglide::$controller ?>-<?= Paraglide::$action ?>" class="modal">
<div id="modal-container">
	<div id="main">
		<h1><?= $title ?></h1>
		<div id="content">
			<?= $PAGE_CONTENT ?>
		</div>
	</div>
</div>
</body>
</html>
