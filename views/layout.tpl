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
<br />
<?= $PAGE_CONTENT ?>
<br />
<br />
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">
			<a class="brand" href="#">Pingelo</a>
			<div class="btn-group pull-right">
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
					<i class="icon-user"></i> Username
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
					<li><a href="#about">Leaders</a></li>
					<li><a href="#contact">Results</a></li>
					<li><a href="#contact">About</a></li>
				</ul>
	          </div><!--/.nav-collapse -->
	        </div>
	      </div>
	    </div>

		<div class="container-fluid">
			<div class="row-fluid">
				
				
				<div class="span8">
					<div class="well">
						<div style="float:left;">
							<div style="float:left; margin-right:10px">
								<span style="float:left" class="label label-info">#1</span><br /><br />
								
							</div>
							
							<div style="float:left; margin-right:10px">
								<img src="http://placehold.it/60x60" alt="">
							</div>
							
							<div style="float:left; margin-right:10px">
								<span><a href="#">@jacksondeane</a></span><br />
								<p><span style="float:left" class="label">Default</span>(100 games)</p>
							</div>
						</div>
	          		</div>
	
					
				</div><!--/span-->
				
				<div class="span4">
					<div class="well sidebar-nav">
						<ul>
							<li>1</li>
							<li>2</li>
							<li>3</li>
						</ul>
					</div><!--/.well -->
				</div><!--/span-->
	      </div><!--/row-->
	      <hr>
	      <footer>
	        <p>&copy; Company 2012</p>
	      </footer>

	    </div><!--/.fluid-container-->
</body>
</html>
