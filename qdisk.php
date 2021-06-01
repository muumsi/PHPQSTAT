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

echo "<script type=\"text/javascript\" src=\"https://www.gstatic.com/charts/loader.js\"></script>";

echo "</head><body>";
	//<!-- Latest compiled and minified JavaScript -->
echo "<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js\" integrity=\"sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa\" crossorigin=\"anonymous\"></script>";

echo "<script type=\"text/javascript\">
  function changeIt(view){document.getElementById('rta').src= view;}
</script>";

$owner  = $user;
echo "<nav class=\"navbar navbar-default navbar-fixed-top\">
		<div class=\"container-fluid\">
			<div class=\"navbar-header\">
				<a class=\"navbar-brand\" href=\"#\">PHPQstat-Slurm</a></div>
				<ul class=\"nav navbar-nav\">
					<li><a href=\"index.php\">Login</a></li>
					<li><a href=\"prva.php\">Home</a></li>
					<li><a href=\"qhost.php\">Hosts status</a></li>
					<li><a href=\"qstat.php\">Queue status</a></li>
					<li class=\"active\"><a href=\"qdisk.php\">Disk usage</a></li>
					<li class=\"disabled\"><a href=\"\">User jobs</a></li>
					<li class=\"disabled\"><a href=\"\">Job details</a></li>
					<li><a href=\"about.php\">About PHPQstat-Slurm</a></li>
				</ul>
			</div>
		</nav>";


echo "<br><br><br><br>";

echo "<div class=\"container\">";
echo "<h1 style=\"text-align:center;\"> Disk usage </h1>";
$command="df -h " . $fs_root;
$output = $ssh->exec($command);
echo "<table class=\"table table-striped table-bordered\">";
$separator = "\r\n";
$line = strtok($output, $separator);
//echo $line;
$i=0;
while ($line !== false) {
    if($i==0)
	{
        echo "<thead><tr>";
		$deli=explode(" ",$line);
		foreach($deli as $del) 
		{
			if(ord($del)!=0)
			{
				if($del=="Mounted")
				{
					echo "<th>Mounted on</th>";
				}
				else
				{
					if($del!="on")echo "<th>$del</th>";
				}						
			}
				
		}
		echo "</tr></thead><tbody>";
	}
	else
	{
        echo "<tr>";
		$deli=explode(" ",$line);
		foreach($deli as $del) 
		{
			if(ord($del)!=0)echo "<td>$del</td>";
		}

		echo "</tr>";
	}
	$line = strtok( $separator );
	//echo $line;
	$i++;
}	
echo "</tbody></table>";

echo "<script type=\"text/javascript\">";

echo "google.charts.load('current', {packages:['corechart']});";
echo "google.charts.setOnLoadCallback(drawChart);";
echo "function drawChart() {";
echo "var data = google.visualization.arrayToDataTable([";
echo "['User', 'Diskusage'],";

$myfile = fopen("diskusage.txt", "r") or die("Unable to open file!");
$i=-1;

$mydata = array();

while(!feof($myfile)) {
	$line=fgets($myfile);
	list($size,$folder)=explode("\t",$line);
	
	if(strpos($folder, 'ps0')!==false || strpos($folder, 'en0')!==false )
	{
		$i=$i;
	}
	else
	{ 
		$i=$i+1;
		//$folder=substr($folder,2);
		$mydata[$i]=array( $size,$folder);
	}
}
fclose($myfile);

array_pop($mydata);
array_pop($mydata);

array_multisort($mydata, SORT_DESC);

$data=array();
$ci=0;
foreach ($mydata as $row)
{
	$ci++;
	if($ci<20)echo "['",trim(substr($row[1],16)),"',",$row[0],"],";
	if($ci===20)echo "['",trim(substr($row[1],16)),"',",$row[0],"]";
}
echo " ]);";

      echo " var options = {";
        echo "  title: 'Diskusage by user [MB]',";
        echo "  is3D: true,";
        
echo " };";
 
   echo "     var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));";
    echo "    chart.draw(data, options);";
 echo "     }";
/*
*/
echo "    </script>";


echo "<div class=\"container\" style=\"text-align:center;width:100%:\">";

echo "<div id=\"piechart_3d\" ></div>"; 

echo "</div><br><br><br><br><br>";


include("bottom.php");
echo "</body></html>";
?>




