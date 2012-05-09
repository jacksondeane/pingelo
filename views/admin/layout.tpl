<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Admin</title>
<link rel="stylesheet" type="text/css" href="<?= SITE_ROOT ?>styles/reset.css" />
<link rel="stylesheet" type="text/css" href="<?= SITE_ROOT ?>jquery-ui/themes/base/ui.core.css" />
<link rel="stylesheet" type="text/css" href="<?= SITE_ROOT ?>jquery-ui/themes/base/ui.theme.css" />
<link rel="stylesheet" type="text/css" href="<?= SITE_ROOT ?>jquery-ui/themes/base/ui.datepicker.css" />
<link rel="stylesheet" type="text/css" href="<?= SITE_ROOT ?>jquery-ui/themes/base/ui.slider.css" />
<link rel="stylesheet" type="text/css" href="<?= SITE_ROOT ?>styles/admin.css" />
<script type="text/javascript" src="<?= SITE_ROOT ?>scripts/jquery.js"></script>
<script type="text/javascript" src="<?= SITE_ROOT ?>jquery-ui/ui/ui.core.js"></script>
<script type="text/javascript" src="<?= SITE_ROOT ?>jquery-ui/ui/ui.datepicker.js"></script>
<script type="text/javascript" src="<?= SITE_ROOT ?>jquery-ui/ui/ui.slider.js"></script>
<script type="text/javascript" src="<?= SITE_ROOT ?>scripts/jquery.corner.js"></script>
<script type="text/javascript" src="<?= SITE_ROOT ?>scripts/jquery.timepicker.js"></script>
<script type="text/javascript" src="<?= SITE_ROOT ?>scripts/tiny_mce/tiny_mce.js"></script>

<script type="text/javascript" src="<?= SITE_ROOT ?>scripts/shadowbox/shadowbox.js"></script>
<link rel="stylesheet" type="text/css" href="<?= SITE_ROOT ?>scripts/shadowbox/shadowbox.css" />

<script type="text/javascript">
var base_url = '<?= SITE_ROOT ?>';
</script>
<script type="text/javascript" src="<?= SITE_ROOT ?>scripts/admin.js"></script>
</head>
<body>
<div id="container">
	<div id="info">
		<?php $company_config = Paraglide::parse_config('company') ?>
		<h1><?= htmlentities($company_config['main']['name']) ?> <span>Admin</span></h1>
		<?php Paraglide::render_view('admin/info') ?>
	</div>
	<div id="nav">
		<?php Paraglide::render_view('admin/nav') ?>
	</div>
	<div id="subnav">
	<?php Paraglide::render_view('admin/subnav') ?>
	</div>
	<div id="main">
		<div id="heading">
			<h2><span><?= $title ?></span></h2>
			<div id="breadcrumbs">
				<?= Paraglide::render_view('admin/breadcrumbs') ?>
			</div>
		</div>
		<div id="actions">
			<?= Paraglide::render_view('admin/actions') ?>
		</div>
		<div id="body">
			<?= $PAGE_CONTENT ?>
		</div>
	</div>
	<div id="footer">
		<?= Paraglide::render_view('admin/footer') ?>
	</div>
</div>
</body>
</html>
