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


$owner  = $_GET['owner'];
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
					<li class=\"active\"><a href=\"\">User jobs</a></li>
					<li class=\"disabled\"><a href=\"\">Job details</a></li>
					<li><a href=\"about.php\">About PHPQstat-Slurm</a></li>
				</ul>
			</div>
		</nav>";
echo "<br><br><br><br><br><br>";
echo "<table class=\"table table-striped table-bordered\">
        <thead>
		<tr>
                <td>Job id</td>
                <td>Partition</td>
                <td>Name</td>
                <td>User</td>
                <td>State</td>
                <td>Reason</td>
                <td>Time</td>
                <td>Time limit</td>
                <td>Nodes</td>
                <td>Nodelist</td>
                </tr></thead><tbody>";
				
$output= $ssh->exec("squeue -u $owner -o %i,%P,%j,%u,%T,%R,%M,%I,%D,%N");
$separator = "\r\n";
$line = strtok($output, $separator);
$i=0;
while ($line !== false) {
    if($i>0)
	{
		$l=citaj($line,$job,$partition,$name,$user,$state,$reason,$time,$limit,$ncpu,$hostlist);
		echo "                <tr>
						<td><a href=\"qstat_job.php?owner=$user&jobid=$job\">$job</a></td>
						<td>$partition</td>
						<td>$name</td>
						<td><a href=\"qstat_user.php?owner=$user\">$user</a></td>
						<td>$state</td>
						<td>$reason</td>
						<td>$time</td>
						<td>$limit</td>
						<td>$ncpu</td>
						<td><a href=\"qhost.php?owner=$owner\">$hostlist</a></td>
						</tr>";
	}
    	$line = strtok( $separator );
	$i++;
}

include("bottom.php");
echo "</body></html>";
function citaj($line,&$job,&$partition,&$name,&$user,&$state,&$reason,&$time,&$limit,&$ncpu,&$hostlist)
{

	$ostanek=$line;
	$first=strpos($ostanek,",");
	$job=substr($ostanek,0,$first);
	$ostanek=substr($ostanek,$first+1);
	$first=strpos($ostanek,",");
	$partition=substr($ostanek,0,$first);
	$ostanek=substr($ostanek,$first+1);
	$first=strpos($ostanek,",");
	$name=substr($ostanek,0,$first);
	$ostanek=substr($ostanek,$first+1);
	$first=strpos($ostanek,",");
	$user=substr($ostanek,0,$first);
	$ostanek=substr($ostanek,$first+1);
	$first=strpos($ostanek,",");
	$state=substr($ostanek,0,$first);
	$ostanek=substr($ostanek,$first+1);
	$first=strpos($ostanek,",");
	$firstoks=strpos($ostanek,"[");
	$firstoke=strpos($ostanek,"]");
	if($firstoks<$first && $firstoke>$first)
	{
		$first=$firstoke+1;
	}
	$reason=substr($ostanek,0,$first);
	$ostanek=substr($ostanek,$first+1);
	$se=true;
	do
	{
		$first=strpos($ostanek,",");
		$firstoks=strpos($ostanek,"[");
		$firstoke=strpos($ostanek,"]");
		if($firstoks<$first && $firstoke>$first)
		{
			$first=$firstoke+1;
		}
		if(is_numeric(substr($ostanek,0,1)))
		{
			$time=substr($ostanek,0,$first);
			$ostanek=substr($ostanek,$first+1);
			$se=false;
		} else
		{		
			$reason=$reason . "," . substr($ostanek,0,$first);
			$ostanek=substr($ostanek,$first+1);
		}
	} while ( $se);
	$first=strpos($ostanek,",");
	$limit=substr($ostanek,0,$first);
	$ostanek=substr($ostanek,$first+1);
	$first=strpos($ostanek,",");
	$ncpu=substr($ostanek,0,$first);
	$hostlist=substr($ostanek,$first+1); 

	return $line;

} 

?>


