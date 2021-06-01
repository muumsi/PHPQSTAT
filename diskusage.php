<?php
require 'phplot.php';
# The data labels aren't used directly by PHPlot. They are here for our
# reference, and we copy them to the legend below.
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
		$folder=substr($folder,2);
		$mydata[$i]=array( $size,$folder);
	}
}
fclose($myfile);
array_pop($mydata);
array_pop($mydata);
array_multisort($mydata, SORT_DESC);
$data=array();
foreach ($mydata as $row)
{
	$data[]=array( $row[1],$row[0]);
}


$plot = new PHPlot(800,600);
$plot->SetImageBorderType('plain');
$plot->SetPlotType('pie');
$plot->SetDataType('text-data-single');
$plot->SetDataValues($data);
# Set enough different colors;
//$plot->SetDataColors(array('red', 'green', 'blue', 'yellow', 'cyan','magenta', 'brown', 'lavender', 'pink', 'gray', 'orange'));
# Main plot title:
$plot->SetTitle("Maister\n(disk usage by users)");
# Build a legend from our data array.
# Each call to SetLegend makes one line as "label: value".
foreach ($data as $row)
 $plot->SetLegend($row[0]);
# Place the legend in the upper left corner:
$plot->SetLegendPixels(5, 5);
$plot->DrawGraph();
?>