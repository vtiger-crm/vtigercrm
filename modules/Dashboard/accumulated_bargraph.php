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
include ("../../jpgraph/src/jpgraph_bar.php");

$refer_code=(isset($_REQUEST['width']))?$_REQUEST['refer_code']:"0,0";
$referdata=(isset($_REQUEST['referdata']))?$_REQUEST['referdata']:"null"; //The Status Name
$datavalue=(isset($_REQUEST['datavalue']))?$_REQUEST['datavalue']:"0,K,0";
$width=(isset($_REQUEST['width']))?$_REQUEST['width']:410;
$height=(isset($_REQUEST['height']))?$_REQUEST['height']:270;
$left=(isset($_REQUEST['left']))?$_REQUEST['left']:110;
$right=(isset($_REQUEST['right']))?$_REQUEST['right']:20;
$top=(isset($_REQUEST['top']))?$_REQUEST['top']:40;
$bottom=(isset($_REQUEST['bottom']))?$_REQUEST['bottom']:50;
$title=(isset($_REQUEST['title']))?$_REQUEST['title']:"Horizontal graph";
$target_val=(isset($_REQUEST['test']))?$_REQUEST['test']:"";
//Exploding the data values
$datavalue=explode("K",$datavalue);
$name_value=explode(",",$referdata);
$datax=explode(",",$refer_code); //The values to the XAxis
$target_val=urldecode($target_val);
$target_val=explode("K",$target_val);



$color_array=array("#FFD0C7","#C9F7C9","#C2C3EF","#F7F7C1","#28D6D7","#E7BCE7","#DFD8C3","lightpink","burlywood2","cadetblu
e");
#$color_array=array("#FF8B8B","#8BFF8B","#A8A8FF","#FFFF6E","#C5FFFF","#FFA8FF","#FFE28B","lightpink","burlywood2","cadetblue");

// Create the graph. These two calls are always required
$graph = new Graph($width,$height,"auto");    
$graph->SetScale("textlin");

$graph->SetShadow();


// Create the lines of the Graph
for($i=0;$i<count($datavalue);$i++)
{
	$data=$datavalue[$i];
	$target=$target_val[$i];
	$graph_data=explode(",",$data);
	$data[$i]=$data;
	$bplot[$i] = new BarPlot($graph_data);
	$bplot[$i]->SetFillColor($color_array[$i]);
	$bplot[$i]->SetWidth(10);

	$bplot[$i]->value->Show();
	$bplot[$i]->value->SetFont(FF_FONT1,FS_NORMAL,8);
	$bplot[$i]->value->SetColor("black");
	$bplot[$i]->value->SetFormat('%d');
	$bplot[$i]->SetValuePos('max');

}

$gbplot = new AccBarPlot($bplot);
$gbplot->SetWidth(0.7);

// Add the bar to the graph
$graph->Add($gbplot);

$graph->xaxis->SetTickLabels($datax);

$graph->title->Set($title);

$graph->Set90AndMargin($left,$right,$top,$bottom);
//$graph->SetFrame(false);
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->SetColor("#7D9CB8");
$graph->SetMarginColor("#3D6A93");

// Display the graph
$graph->Stroke();

?>

