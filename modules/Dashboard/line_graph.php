<?php

/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

include ("../../jpgraph/src/jpgraph.php");
include ("../../jpgraph/src/jpgraph_line.php");


$refer_code=(isset($_REQUEST['refer_code']))?$_REQUEST['refer_code']:"0,0";
$datax=explode(",",$refer_code); //The values to the XAxis

$names_value=(isset($_REQUEST['referdata']))?$_REQUEST['referdata']:"null"; //The Status Name 
$name_value=explode(",",$names_value);

//Giving the colors to the Line graph
$color_array=array("red","blue","orange","green","darkorchid","gold1","gray3","lightpink","burlywood2","cadetblue");
$datavalue=(isset($_REQUEST['datavalue']))?$_REQUEST['datavalue']:"0,K,0";

//Exploding the Ticket status 
$datavalue=explode("K",$datavalue); 


$width=(isset($_REQUEST['width']))?$_REQUEST['width']:410;
$height=(isset($_REQUEST['height']))?$_REQUEST['height']:270;
$left=(isset($_REQUEST['left']))?$_REQUEST['left']:50;
$right=(isset($_REQUEST['right']))?$_REQUEST['right']:130;
$top=(isset($_REQUEST['top']))?$_REQUEST['top']:50;
$bottom=(isset($_REQUEST['bottom']))?$_REQUEST['bottom']:60;
$title=(isset($_REQUEST['title']))?$_REQUEST['title']:"Horizontal graph";
$target_val=(isset($_REQUEST['target_val']))?$_REQUEST['target_val']:"";


// Setup the graph

$graph = new Graph($width,$height);
$graph->SetMarginColor('white');
$graph->SetScale("textlin");
$graph->SetMargin($left,$right,$top,$bottom);
$graph->tabtitle->Set($title );
$graph->tabtitle->SetFont(FF_FONT2,FS_BOLD,13);
$graph->yaxis->HideZeroLabel();
$graph->xgrid->Show();

$thick=6;
// Create the lines of the Graph
for($i=0;$i<count($datavalue);$i++)
{
	$data=$datavalue[$i];
	$graph_data=explode(",",$data);

	$name=$name_value[$i];
	$color_val=$color_array[$i];
	$temp="p".$i;
	$$temp = new LinePlot($graph_data);
	$$temp->SetColor($color_val);
	$$temp->SetLegend($name);

	$x_thick=$thick-$i;	
	if($x_thick<=1)
		$x_thick=1;

	$$temp->SetWeight($x_thick);
	$graph->Add($$temp);

	$max=0;
	for($j=0;$j<count($graph_data); $j++)
	{
		$x=$graph_data[$j];
		if($x>=$max)
			$max=$x;
		else
			$max=$max;
	}
}


if($max>=5)
{
	$graph->yaxis->SetLabelFormat('%d');
}
$graph->legend->Pos(0,0.4,"right","center");

// Set some other color then the boring default
$graph->SetColor("#6F96FF");
//$graph->SetColor("#CCDFCC");
$graph->SetMarginColor("#2447A7");
//$graph->SetMarginColor("#98C098");
$graph->xaxis->SetTickLabels($datax);
$graph->xaxis->SetLabelAngle(90);

// Output line
$graph->Stroke();



?>
