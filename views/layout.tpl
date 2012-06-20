<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Pingelo</title>
<meta name="description" content="Pingelo" /> 

<link rel="stylesheet" type="text/css" href="<?= SITE_ROOT ?>css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="<?= SITE_ROOT ?>css/bootstrap-responsive.min.css" />

<script type="text/javascript" src="<?= SITE_ROOT ?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?= SITE_ROOT ?>scripts/jquery.js"></script>

<script type="text/javascript">
var base_url = '<?= SITE_URL ?>';
var controller_action = '<?= Paraglide::$action ?>';
</script>

<script type="text/javascript" src="<?= SITE_ROOT ?>scripts/main.js"></script>

<?php if (file_exists(APP_PATH . 'public/scripts/helpers/' . Paraglide::$controller . '.js')): ?>
	<script type="text/javascript" src="<?= SITE_ROOT ?>scripts/helpers/<?= Paraglide::$controller ?>.js"></script>
<?php endif; ?>

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
          <a class="brand" href="<?= Paraglide::url('', '') ?>">pingelo</a>
          
            <?php 
              session_start(); 
              $current_email = $_SESSION['email'];
              session_destroy();
            ?>
            <?php if(empty($current_email)): ?>
              <div class="btn-group pull-right">
                <a class="btn btn-primary" href="<?= $authUrl ?>">
                  <i class="icon-user icon-white"></i> Login with Google Apps    
                </a>
              </div>
            <?php else: ?>
            <div class="btn-group pull-right">
                <a class="btn btn-success" href="<?= Paraglide::url('results', 'add') ?>">
                  <i class="icon-plus-sign icon-white"></i> Add New Result
                </a>

                <a class="btn" href="<?= Paraglide::url('main', 'logout') ?>">
                <i class="icon-user"></i> Logout: <?= $current_email ?>
                </a>
              </div>

            <?php endif; ?>

          <div class="nav-collapse">
            <ul class="nav">
            	<li	class="<?= (Paraglide::$controller == 'main') ? 'active' : '' ?>"><a href="<?= Paraglide::url(null) ?>">Home</a></li>
            	<li	class="<?= (Paraglide::$controller == 'users') ? 'active' : '' ?>"><a href="<?= Paraglide::url('users') ?>">Players</a></li>
            	<li	class="<?= (Paraglide::$controller == 'results') ? 'active' : '' ?>"><a href="<?= Paraglide::url('results') ?>">Results</a></li>
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
