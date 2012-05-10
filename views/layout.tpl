<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Pingelo</title>
<meta name="description" content="Pingelo" /> 

<link rel="stylesheet" type="text/css" href="<?= SITE_ROOT ?>css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="<?= SITE_ROOT ?>css/bootstrap-responsive.min.css" />

<script type="text/javascript" src="<?= SITE_ROOT ?>js/bootstrap.min.js"></script>

<script type="text/javascript">
var base_url = '<?= SITE_URL ?>';
var controller_action = '<?= Paraglide::$action ?>';
</script>

<script type="text/javascript" src="<?= SITE_ROOT ?>scripts/main.js"></script>

</head>

<body>
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">
		          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
		          </a>			
		          <a class="brand" href="#">pingelo</a>
		          <div class="btn-group pull-right">
		            <a class="btn dropdown-toggle" data-toggle="dropdown" href="<?= Paraglide::url('results', 'add') ?>">
		              <i class="icon-plus-sign"></i> Add Game
		              <span class="caret"></span>
		            </a>
		            <ul class="dropdown-menu">
		              <li><a href="#">Profile</a></li>
		              <li class="divider"></li>
		              <li><a href="#">Sign Out</a></li>
		            </ul>
		          </div>
		          <div class="nav-collapse">
		            <ul class="nav">
		              <li class="active"><a href="#">Home</a></li>
		              <li><a href="#about">About</a></li>
		            </ul>
		          </div><!--/.nav-collapse -->
		        </div>
		      </div>
	</div>
</div>
<br /><br /><br />
<div class="container">
	<?= $PAGE_CONTENT ?>
</div>

</body>
</html>
