<?php
function installer($host, $dbname, $dbuser, $dbpass)
{
	if(isset($host) && isset($dbname) && isset($dbuser) && isset($dbpass))
	{
		//create config file
		$config = fopen("config.php", "w");
		$db_config = "'".'mysql:host=' . $host . ";dbname=" . $dbname . "', '" . $dbuser . "', '" . $dbpass . "'";
		$config_content = "<?php R::setup(". $db_config ."); ?>";
		fwrite($config, $config_content);
		fclose($config);

		echo "Installation was successful you may now register as a first user without invite code!";
	}
	else
	{
	?>

	<!DOCTYPE html>
	<html lang="en">
	  <head>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	    <meta name="description" content="">
	    <meta name="author" content="">
	    <link rel="icon" href="../../favicon.ico">

	    <title>PrivateCube Installer</title>

	    <!-- Bootstrap core CSS -->
	    <link href="static/twbs/dist/css/bootstrap.min.css" rel="stylesheet">

	    <!-- Custom styles for this template -->
	    <link href="static/starter-template.css" rel="stylesheet">


	    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	    <!--[if lt IE 9]>
	      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	    <![endif]-->
	  </head>

	  <body>

	    <nav class="navbar navbar-inverse navbar-fixed-top">
	      <div class="container">
	        <div class="navbar-header">
	          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
	            <span class="sr-only">Toggle navigation</span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	          </button>
	          <a class="navbar-brand" href="#">PrivateCube</a>
	        </div>
	        <div id="navbar" class="collapse navbar-collapse">
	        </div><!--/.nav-collapse -->
	      </div>
	    </nav>

	    <div class="container">

	      <div class="starter-template">
	        <div id="main_content">
	          <div id="login_page">
			      <form class="form-signin" method="get" action="install">
			        <h2 class="form-signin-heading">PrivateCube Installer</h2>
			        <label for="host" class="sr-only">Database host</label>
			        <input type="text" name="host" id="host" class="form-control" placeholder="Database Host" required autofocus>
			        <label for="dbname" class="sr-only">Database Name</label>
			        <input type="text" name="dbname" id="dbname" class="form-control" placeholder="Database Name" required>
			        <label for="dbuser" class="sr-only">Database User</label>
			        <input type="text" id="dbuser" name="dbuser" class="form-control" placeholder="Database User" required>
			        <label for="dbpass" class="sr-only">Password</label>
			        <input type="password" id="dbpass" name="dbpass" class="form-control" placeholder="Password" required>
			        <button class="btn btn-lg btn-primary btn-block" value="Send" name="submit" type="submit">Install</button>
			      </form>
			 </div>
	        </div>
	      </div>

	    </div><!-- /.container -->


	    <!-- Bootstrap core JavaScript
	    ================================================== -->
	    <!-- Placed at the end of the document so the pages load faster -->
	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	    <script src="static/twbs/dist/js/bootstrap.min.js"></script>
	    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
	    <script src="static/twbs/assets/js/ie10-viewport-bug-workaround.js"></script>
	  </body>
	</html>

<?php 
	} 
}
?>
