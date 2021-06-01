<?php
session_start();
$connection = $_SESSION['connection'];
$user=$_SESSION['user'];
$pass=$_SESSION['pass'];

include 'phpqstat_inc.php';

if(strcmp($user,"")!==0)
{
	include('Net/SSH2.php');
	$ssh = new Net_SSH2($rlogin_hostname);
	if (!$ssh->login($user, $pass)) 
	{
      		header("Location: world.php"); // Redirect browser 
	}
}
echo "<html><head><title>PHPQstat-Slurm</title>";
echo "<meta name=\"AUTHOR\" content=\"Jordi Blasco Pallares, Miran Ulbin \"><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";
echo "<meta name=\"KEYWORDS\" content=\"slurm hpc supercomputing batch queue linux jordi blasco solnu miran ulbin\">";
echo "<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css\" integrity=\"sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u\" crossorigin=\"anonymous\">";

	//<!-- Optional theme -->
echo "<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css\" integrity=\"sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp\" crossorigin=\"anonymous\">";

	//<!-- Latest compiled and minified JavaScript -->
echo "<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js\" integrity=\"sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa\" crossorigin=\"anonymous\"></script></head>";


$jobid  = $_GET['jobid'];
echo "<body><nav class=\"navbar navbar-default navbar-fixed-top\">
		<div class=\"container-fluid\">
			<div class=\"navbar-header\">
				<a class=\"navbar-brand\" href=\"#\">PHPQstat-Slurm</a></div>
				<ul class=\"nav navbar-nav\">
					<li><a href=\"index.php\">Login</a></li>
					<li><a href=\"prva.php\">Home</a></li>
					<li><a href=\"qhost.php\">Hosts status</a></li>
					<li><a href=\"qstat.php\">Queue status</a></li>
					<li><a href=\"qdisk.php\">Disk usage</a></li>
					<li class=\"disabled\"><a href=\"\">User jobs</a></li>
					<li class=\"active\"><a href=\"\">Job details</a></li>
					<li><a href=\"about.php\">About PHPQstat-Slurm</a></li>
				</ul>
			</div>
		</nav>";
echo "<br><br><br><br><br><br>";

$output= $ssh->exec("squeue -j $jobid -o %all");
$separator = "\r\n";
$line = strtok($output, $separator);

$i=0;
echo "<table class=\"table table-striped table-bordered table-responsive\">";
echo "<thead><tr><td><b>Job ID</b></td><td><b>";
echo $jobid;
echo "</b></td></tr></thead>";

while ($line !== false) {

	  if($i==0)
	  {
		  $head=explode("|",$line);
		  echo "<tbody>";
		  /*
		  echo "<thead><tr><td>";
		  echo str_replace("|","</td><td>",$line);
		  echo "</td></tr></thead><tbody>";
		  */
	  }
	  if($i>0)
	  {
		  $value=explode("|",$line);
		  $max = sizeof($head);
			for ($x = 0; $x < $max; $x++) {
				echo "<tr><td>";
				echo $head[$x];
				echo "</td><td>";
				echo $value[$x];
				echo "</td></tr>";				
			} 	  
			/*
		  echo "<tr><td>";
		  echo str_replace("|","</td><td>",$line);
		  echo "</td></tr></tbody>";
		  */
	  }

    $line = strtok( $separator );
	$i++;
}

echo " </tbody></table><br><br><br><br><br><br><br><br><br><br>";
	
include("bottom.php");

echo "</body></html>";
?>