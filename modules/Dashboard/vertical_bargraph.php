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

require_once('config.php');
require_once('include/utils/GraphUtils.php');
include_once ('Image/Graph.php');
include_once ('Image/Canvas.php');

//$tmp_dir=$root_directory."cache/images/";

/** Function to render the Horizontal Graph
        * Portions created by vtiger are Copyright (C) vtiger.
        * All Rights Reserved.
        * Contributor(s): ______________________________________..
 */
function vertical_graph($referdata,$refer_code,$width,$height,$left,$right,$top,$bottom,$title,$target_val,$cache_file_name,$html_image_name)
{

	global $log,$root_directory,$lang_crm,$theme,$app_strings;
//We'll be getting the values in the form of a string separated by commas
	$datay=explode("::",$referdata); // The datay values  
	$datax=explode("::",$refer_code); // The datax values  

// The links values are given as string in the encoded form, here we are decoding it
	$target_val=urldecode($target_val);
	$target=explode("::",$target_val);

	$alts=array();
	$temp=array();
	for($i=0;$i<count($datax);$i++)
	{
		if($app_strings[$datax[$i]] != '') //HomePage Dashboard Strings i18nized - ahmed
			$name=$app_strings[$datax[$i]];
		else
			$name=$datax[$i];
		$pos = substr_count($name," ");
		$alts[]=htmlentities($name)."=%d";
//If the datax value of a string is greater, adding '\n' to it so that it'll cme inh 2nd line
		 if(strlen($name)>=15)
                        $name=substr($name, 0, 15);
		if($pos>=2)
		{
			$val=explode(" ",$name);
			$n=count($val)-1;

			$x="";
			for($j=0;$j<count($val);$j++)
			{
				if($j != $n)
				{
					$x  .=" ". $val[$j];
				}
				else
				{
					$x .= "@#".$val[$j];
				}
			}
			$name = $x;
		}
		$name=str_replace("@#", "\n",$name);
		$temp[]=$name; 
	}
	$datax=$temp;

	//datay is the values
	//datax is the status

	// Set the basic parameters of the graph
	$canvas =& Image_Canvas::factory('png', array('width' => $width, 'height' => $height, 'usemap' => true));
	$imagemap = $canvas->getImageMap();
	$graph =& Image_Graph::factory('graph', $canvas);
	$font =& $graph->addNew('font', calculate_font_name($lang_crm));
	// set the font size to 12
	$font->setSize(8);

	if($theme == "blue")
	{
		$font_color = "#212473";
	}
	else
	{
		$font_color = "#000000";
	}
	$font->setColor($font_color);
		
	$graph->setFont($font);
	$titlestr =& Image_Graph::factory('title', array($title,10));
   	$plotarea =& Image_Graph::factory('plotarea',array(
				'axis',
				'axis',
				'vertical'
                ));
	$graph->add(
	    Image_Graph::vertical($titlestr,
			$plotarea,
    	5
	 	)
	);   


	// Now create a bar plot
	$max=0;
	$xlabels = array();
	$dataset = & Image_Graph::factory('dataset');
	if($theme == 'woodspice')
		$fill =& Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_HORIZONTAL_MIRRORED, '#804000', 'white'));
	elseif($theme == 'bluelagoon')
		$fill =& Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_HORIZONTAL_MIRRORED, 'blue', 'white'));
	elseif($theme == 'softed')
		$fill =& Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_HORIZONTAL_MIRRORED, 'blue', 'white'));
	else
		$fill =& Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_HORIZONTAL_MIRRORED, 'black', 'white'));
	
		
	for($i=0;$i<count($datay); $i++)
	{
		$x=1+2*$i;
		if($datay[$i]>=$max) $max=$datay[$i];
		$dataset->addPoint(
			        $x,
			        $datay[$i],
			        array(
			            'url' => $target[$i],
			            'alt' => $alts[$i]
			        )
	    );
	    // build the xaxis label array to allow intermediate ticks
	    $xlabels[$x] = $datax[$i];
	    $xlabels[$x+1] = '';
	}


	//$bplot = new BarPlot($datay);
	$bplot = & $plotarea->addNew('bar', $dataset);
	$bplot->setFillStyle($fill);

	//You can change the width of the bars if you like
	$bplot->setBarWidth(50/count($datax),"%");
	$bplot->setPadding(array('top'=>20));

	$bplot->setBackground(Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'white')));

	// Setup X-axis
	$xaxis =& $plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
	$yaxis =& $plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
	$yaxis->setFontSize(10);
			
	// set grid
	$gridY =& $plotarea->addNew('line_grid', IMAGE_GRAPH_AXIS_Y);
	$gridY->setLineColor('#FFFFFF@0.5');
	$gridY2 =& $plotarea->addNew('bar_grid', null, IMAGE_GRAPH_AXIS_Y); 
	$gridY2->setFillColor('#FFFFFF@0.2'); 


	// Add some grace to y-axis so the bars doesn't go
	// all the way to the end of the plot area
	if($max<=10)
		$yaxis->forceMaximum(round(($max * 1.1) + 1.5));
	elseif($max>10 && $max<=100)
		$yaxis->forceMaximum(round(($max * 1.1) + 1.5));
	elseif($max>100 && $max<=1000)
		$yaxis->forceMaximum(round(($max * 1.1) + 10.5));
	else
		$yaxis->forceMaximum(round(($max * 1.1) + 100.5));	
	$ticks = get_tickspacing(round(($max * 1.1) + 2.0));

	// First make the labels look right
	$yaxis->setLabelInterval($ticks[0]);
	$yaxis->setTickOptions(5,0);
	$yaxis->setLabelInterval($ticks[1],2);
	$yaxis->setTickOptions(2,0,2);
	
	// Create the xaxis labels
	$array_data =& Image_Graph::factory('Image_Graph_DataPreprocessor_Array', 
	    array($xlabels) 
	); 

	// Then fix the tick marks
	$xaxis->setDataPreprocessor($array_data);
	$xaxis->forceMinimum(0);
	$xaxis->forceMaximum(2*count($datay));
	$xaxis->setFontAngle('vertical');
	$xaxis->setLabelInterval(1);
	$xaxis->setTickOptions(0,0);
	$xaxis->setLabelInterval(2,2);
	$xaxis->setTickOptions(5,0,2);
			
	// set markers
	$marker =& $graph->addNew('value_marker', IMAGE_GRAPH_VALUE_Y);
	$marker->setFillColor('000000@0.0');
	$marker->setBorderColor('000000@0.0');
	$marker->setFontSize(10);
	// shift markers 10 pix right
	$marker_pointing =& $graph->addNew('Image_Graph_Marker_Pointing', array(0,-10,& $marker));
	$marker_pointing->setLineColor('000000@0.0');
	$bplot->setMarker($marker_pointing);


	//Getting the graph in the form of html page	
	$img = $graph->done(
						    array(
							        'tohtml' => true,
							        'border' => 0,
							        'filename' => $cache_file_name,
							        'filepath' => '',
							        'urlpath' => ''
							    ));
	save_image_map($cache_file_name.'.map', $img);

	return $img;
}

?>
