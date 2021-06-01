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

$node  = $_GET['node'];
echo "<nav class=\"navbar navbar-default navbar-fixed-top\">
		<div class=\"container-fluid\">
			<div class=\"navbar-header\">
				<a class=\"navbar-brand\" href=\"#\">PHPQstat-Slurm</a></div>
				<ul class=\"nav navbar-nav\">
					<li><a href=\"index.php\">Login</a></li>
					<li><a href=\"prva.php\">Home</a></li>
					<li><a href=\"qhost.php\">Hosts status</a></li>
					<li><a href=\"qstat.php\">Queue status</a></li>
					<li class=\"active\"><a href=\"qstat_node.php?node=$node\">Queue status node</a></li>
					<li><a href=\"qdisk.php\">Disk usage</a></li>
					<li class=\"disabled\"><a href=\"\">User jobs</a></li>
					<li class=\"disabled\"><a href=\"\">Job details</a></li>
					<li><a href=\"about.php\">About PHPQstat-Slurm</a></li>
				</ul>
			</div>
		</nav>";


echo "<br><br><br><br>";
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

$command= "squeue -o '%i,%P,%j,%u,%T,%R,%M,%l,%D,%N'";
$output = $ssh->exec($command);
//echo $output;
$separator = "\r\n";
$line = strtok($output, $separator);
$i=0;
while ($line !== false) {
    if($i>0)
	{
		$l=citaj($line,$job,$partition,$name,$userown,$state,$reason,$time,$limit,$ncpu,$hostlist);

		if(trim($node)===trim($hostlist))

		{
		echo "                <tr>
			<td><a href=\"qstat_job.php?owner=$userown&jobid=$job\">$job</a></td>
			<td>$partition</td>
			<td>$name</td>
			<td><a href=\"qstat_user.php?owner=$userown\">$userown</a></td>
			<td>$state</td>
			<td>$reason</td>
			<td>$time</td>
			<td>$limit</td>
			<td>$ncpu</td>
			<td><a href=\"qstat_node.php?node=$hostlist\">$hostlist</a></td>
			</tr>";
		}
	
	}
    	$line = strtok( $separator );
	//echo $line;
	$i++;
}

echo "                </tbody>
	</table>

<br>
	<table class=\"table table-striped table-bordered\">
        <thead>
		<tr>
		<td>Jobs status</td>
                <td>Total</td>
                <td>Slots</td>
                </tr></thead><tbody>

";

$command= "squeue -o '%T,%C'";
$output = $ssh->exec($command);
$nrun=0;
$srun=0;
$npen=0;
$spen=0;
$nzom=0;
$szom=0;
$ncan=0;
$scan=0;
$separator = "\r\n";
$line = strtok($output, $separator);
$i=0;
while ($line !== false) {
    if($i>0)
	{
		list($state, $ncpu)=explode(",",$line);
		if (strcmp($state,'RUNNING')==0){
			$nrun++;
			$srun+=$ncpu;
		}
		if (strcmp($state,'PENDING')==0){
			$npen++;
			$spen+=$ncpu;
		}
		if (strcmp($state,'SUSPENDED')==0){
			$nzom++;
			$szom+=$ncpu;
		}
		if (strcmp($state,'CANCELLED')==0){
			$ncan++;
			$scan+=$ncpu;
		}
	}
	$line = strtok( $separator );
	$i++;
}
echo "          <tr>
                <td>running</td>
                <td>$nrun</td>
                <td>$srun</td>
                </tr>
                <tr>
                <td>pending</td>
                <td>$npen</td>
                <td>$spen</td>
                </tr>
                <tr>
                <td>suspended</td>
                <td>$nzom</td>
                <td>$szom</td>
                </tr>
				<tr>
                <td>canceled</td>
                <td>$ncan</td>
                <td>$scan</td>
                </tr>
";



echo "</tbody></table<br>";

echo "<div align=\"center\">Real-time Accounting : 
		<a href=\"#\" onclick=\"changeIt('img/hour.png')\">hour</a> - 
		<a href=\"#\" onclick=\"changeIt('img/day.png')\">day</a> - 
		<a href=\"#\" onclick=\"changeIt('img/week.png')\">week</a> - 
		<a href=\"#\" onclick=\"changeIt('img/month.png')\">month</a> - 
		<a href=\"#\" onclick=\"changeIt('img/year.png')\">year</a></div>
		<div align=\"center\"><img src=\"img/hour.png\" id='rta' border='0'></div>
		<br><br><br><br><br>";

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
	$reason=substr($ostanek,0,$first);
	$ostanek=substr($ostanek,$first+1);
	$se=true;
	do
	{
		$first=strpos($ostanek,",");
		if(is_numeric(substr($ostanek,0,1)))
		{
			$time=substr($ostanek,0,$first);
			$ostanek=substr($ostanek,$first+1);
			$se=false;
		} else
		{		
			$reason=$reason . substr($ostanek,0,$first);
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




