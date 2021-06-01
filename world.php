<html>

<head>
  <title>PHPQstat-Slurm</title>
  <meta name="AUTHOR" content="Jordi Blasco Pallares, Miran Ulbin ">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="KEYWORDS" content="slurm hpc supercomputing batch queue linux jordi blasco solnu miran ulbin">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

</head>
<body>

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
 

<script type="text/javascript">
  function changeIt(view){document.getElementById('rta').src= view;}
</script>


<?php

include 'phpqstat_inc.php';


echo "<body><nav class=\"navbar navbar-default navbar-fixed-top\">
		<div class=\"container-fluid\">
			<div class=\"navbar-header\">
				<a class=\"navbar-brand\" href=\"#\">PHPQstat-Slurm</a></div>
				<ul class=\"nav navbar-nav\">
					 <li><a href=\"index.php\">Login</a></li>
					<li class=\"active\"><a href='world.php'>Home</a></li>
					<li class=\"disabled\"><a href=\"\">Hosts status</a></li>
					<li class=\"disabled\"><a href=\"\">Queue status</a></li>
					<li class=\"disabled\"><a href=\"qdisk.php\">Disk usage</a></li>
					<li class=\"disabled\"><a href=\"\">User jobs</a></li>
					<li class=\"disabled\"><a href=\"\">Job details</a></li>
					<li><a href=\"about1.php\">About PHPQstat-Slurm</a></li>
				</ul>
			</div>
		</nav>";




echo "<body>";
echo "<br><br><br>";
echo "<div class=\"container\">";
echo "<h1 style=\"text-align:center;\">" . $phpq_title . "</h1>";
echo "<br>";

echo "<div align=\"center\">Real-time Accounting : ";
echo "<a href=\"#\" onclick=\"changeIt('img/hour.png')\">hour</a> - ";
echo "<a href=\"#\" onclick=\"changeIt('img/day.png')\">day</a> - ";
echo "<a href=\"#\" onclick=\"changeIt('img/week.png')\">week</a> - "; 
echo "<a href=\"#\" onclick=\"changeIt('img/month.png')\">month</a> - ";
echo "<a href=\"#\" onclick=\"changeIt('img/year.png')\">year</a></div>";
echo "<div align=\"center\"><img src=\"img/hour.png\" id='rta' border='0'></div>";

include("bottom.php");
?>


</body>
</html>

