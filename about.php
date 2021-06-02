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


$owner  = $user;
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
					<li class=\"disabled\"><a href=\"\">Job details</a></li>
					<li class=\"active\"><a href=\"about.php\">About PHPQstat-Slurm</a></li>
				</ul>
			</div>
		</nav>";
		


echo "<br><br><br><br><br><div align=\"center\"><table class=\"table table-bordered\">";
echo "<tr><td align=\"center\">";
echo "<b>PHPQstat</b> is a web interface that allows to connect to the useful commands of the Slurm workload managers. With this interface, you can monitor your job status and your queues health at real time.<br><br>";
echo "<b>AUTHOR: </b> Written by Miran Ulbin 2018-2021. (Based on PHPQstat by Jordi Blasco Pallarès (<a href=\"http://www.hpcnow.com\" target=\"hpcnow\">HPCNow!</a>)) <br><br>";

echo "<b>REPORTING BUGS</b> Report bugs to <a href=\"mailto:miran.ulbin@um.si\">miran.ulbin@um.si</a><br><br>";
echo "<b>LICENSE</b> This is free software: you are free to change and redistribute it. GNU General Public License version 3.0 (<a href=\"http://gnu.org/licenses/gpl.html\" target=\"gpl\">GPLv3</a>).<br><br>";
echo "<b>Version : 1.0.0MU (June 2021)</b><br><br>";
echo "<a href=\"https://github.com/muumsi/PHPQstat\" target=\"GH\">https://github.com/muumsi/PHPQstat</a><br>";
echo "</td></tr></table></div><br>";
 
include("bottom.php");
echo "</body></html>";
?>
 



