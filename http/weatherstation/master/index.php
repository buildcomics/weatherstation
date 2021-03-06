<?php
require_once("functions.php"); //include stuff like mysql functions
date_default_timezone_set("Europe/Amsterdam");
$navigation = array("insert", "projruns", "graph", "meters");
$navigation_menu = array("insert" => "Insert Data", "projruns"=>"Projects and runs", "meters"=>"Meters");
if(!in_array(@$_GET["page"], $navigation)) {
	$page = "insert";
}
else {
	$page = $_GET["page"];
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EE Powerplot</title>

    <link href="css/bootstrap.min.css" rel="stylesheet"><!-- Bootstrap CSS, for styling -->
	<link href="css/main.css" rel="stylesheet"><!-- main css sheet -->	
	<link href="css/bootstrap-clockpicker.min.css" rel="stylesheet"> <!-- Jquery UI CSS, for date picker --> 
	<link href="css/jquery-ui.theme.min.css" rel="stylesheet"> <!-- Jquery UI CSS, for date picker -->
	<link href="css/jquery-ui.min.css" rel="stylesheet"> <!-- Jquery UI CSS, for date picker --> 
	<link href="css/bootstrap-treefy.min.css" rel="stylesheet"> <!-- Bootstrap Treefy Tables CSS -->
	<link href="css/upload.css" rel="stylesheet"> <!-- Formstone Upload CSS, for ajax responsive upload system -->

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	
    <script src="js/jquery.min.js"></script><!-- jQuery, for responsive system  -->
    <script src="js/bootstrap.min.js"></script><!-- bootstrap JS, for styling -->
	<script src="js/bootstrap-clockpicker.min.js"></script><!-- bootstrap JS, for styling -->
	<script src="js/jquery-ui.min.js"></script><!-- Jquery UI, for datepicker-->
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
          <a class="navbar-brand" href="index.php">EE Powerplotter</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <?php
			foreach($navigation_menu AS $link=>$text) {
				echo "\t\t\t<li";
				if ($link == $page) {
					echo " class=\"active\"";
				}
				echo "><a href=\"index.php?page=".$link."\">".$text."</a></li>\n";
			}
			?>
			</ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
<?php 
	include($page.".php");
?>
</body>
</html>
