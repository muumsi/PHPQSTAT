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

echo "</head><body>";
	//<!-- Latest compiled and minified JavaScript -->
echo "<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js\" integrity=\"sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa\" crossorigin=\"anonymous\"></script>";

echo "<script type=\"text/javascript\">
  function changeIt(view){document.getElementById('rta').src= view;}
</script>";

$owner  = $_GET['owner'];

echo "<nav class=\"navbar navbar-default navbar-fixed-top\">
		<div class=\"container-fluid\">
			<div class=\"navbar-header\">
				<a class=\"navbar-brand\" href=\"#\">PHPQstat-Slurm</a></div>
				<ul class=\"nav navbar-nav\">
					<li><a href=\"index.php\">Login</a></li>
					<li><a href=\"prva.php\">Home</a></li>
					<li class=\"active\"><a href=\"qhost.php\">Hosts status</a></li>
					<li><a href=\"qstat.php\">Queue status</a></li>
					<li><a href=\"qdisk.php\">Disk usage</a></li>
					<li class=\"disabled\"><a href=\"\">User jobs</a></li>
					<li class=\"disabled\"><a href=\"\">Job details</a></li>
					<li><a href=\"about.php\">About PHPQstat-Slurm</a></li>
				</ul>
			</div>
		</nav>";


echo "<br><br><br><br><br><br>";

echo "<table class=\"table table-bordered\">";
echo "<thead><tr CLASS=\"header\">
		<td>Hostname</td>
                <td>NCPU</td>
                <td>Storage</td>
                <td>RAM</td>
				<td>Load avg</td>
				<td>State</td>
                </tr></thead><tbody>";

$command="sinfo -o %n,%c,%d,%e,%O,%T --sort=+N";
//echo $command;
$output = $ssh->exec($command);
//echo $output;
$separator = "\r\n";
$line = strtok($output, $separator);
$i=0;
while ($line !== false) {
    if($i>0){
		list($name, $ncpu, $disk, $mem, $load, $state)=explode(",",$line);
		if(strcmp($name,'hpc-core')!==0){
		      $class="class=\"active\"";
		      if(strcmp($state,'allocated')==0)$class="class=\"info\"";
	              if(strcmp($state,'down*')==0)$class="class=\"danger\"";
                      if(strcmp($state,'mixed')==0)$class="class=\"warning\"";
                      if(strcmp($state,'idle')==0)$class="class=\"success\"";
         	      echo "<tr " . $class . ">";
		      echo "          <td " . $class . "><a href=\"nodeproc.php?node=$name\">$name</a></td>";
		      echo "          <td " . $class . ">$ncpu</td>";
		      echo "          <td " . $class . ">$disk MB</td>";
		      echo "          <td " . $class . ">$mem MB</td>";
		      echo "          <td " . $class . ">$load</td>";
		      echo "          <td " . $class . ">$state</td>";
		      echo "</tr>";
		}
		/*
		if(strcmp($name,'hpc-core')!==0){	
			echo "<tr>";
				echo "          <td>$name</td>";
				echo "          <td>$ncpu</td>";
				echo "          <td>$disk MB</td>";
				echo "          <td>$mem MB</td>";
				echo "          <td>$load</td>";
				echo "          <td>$state</td>";
			echo "</tr>";
		}
		*/
	}
    $line = strtok( $separator );
	$i=$i+1;
}

echo "</tbody></table><br><br><br><br><br>";


include("bottom.php");
echo "</body></html>";
?>





