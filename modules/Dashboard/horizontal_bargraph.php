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
function horizontal_graph($referdata,$refer_code,$width,$height,$left,$right,$top,$bottom,$title,$target_val,$cache_file_name,$html_image_name)
{

	global $log,$root_directory,$lang_crm,$theme;
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
		$name=$datax[$i];
		$pos = substr_count($name," ");
		$alts[]=htmlentities($name)."=%d";
//If the daatx value of a string is greater, adding '\n' to it so that it'll cme inh 2nd line
		 if(strlen($name)>=14)
                        $name=substr($name, 0, 44);
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
	$titlestr =& Image_Graph::factory('title', array($title,8));
   	$plotarea =& Image_Graph::factory('plotarea',array(
				'axis',
				'axis',
				'horizontal'
                ));
	$graph->add(
	    Image_Graph::vertical($titlestr,
			$plotarea,
    	5
	 	)
	);   


	// Now create a bar plot
	$max=0;
	// To create unique lables we need to keep track of lable name and its count
	$uniquex = array();
	
	$xlabels = array();
	$dataset = & Image_Graph::factory('dataset');
	if($theme == 'woodspice')
		$fill =& Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL_MIRRORED, '#804000', 'white'));
	elseif($theme == 'bluelagoon')
		$fill =& Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL_MIRRORED, 'blue', 'white'));
	elseif($theme == 'softed')
		$fill =& Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL_MIRRORED, 'blue', 'white'));
	else
		$fill =& Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL_MIRRORED, 'black', 'white'));
	
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

		// To have unique names even in case of duplicates let us add the id
		$datax_appearance = $uniquex[$datax[$i]];
		if($datax_appearance == null) {
			$uniquex[$datax[$i]] = 1;			
		} else {
			$xlabels[$x] = $datax[$i] .' ['. $datax_appearance.']';
			$uniquex[$datax[$i]] = $datax_appearance + 1;			
		}
	}

	$bplot = & $plotarea->addNew('bar', $dataset);
	$bplot->setFillStyle($fill);

	//You can change the width of the bars if you like
	$bplot->setBarWidth(50/count($datax),"%");
	$bplot->setPadding(array('top'=>10));

	// We want to display the value of each bar at the top
	//$bplot->value->Show();
	//$bplot->value->SetFont(FF_FONT2,FS_BOLD,12);
	//$bplot->value->SetAlign('left','center');
	//$bplot->value->SetColor("black","gray4");
	//$bplot->value->SetFormat('%d');

	//$graph->SetBackgroundGradient('#E5E5E5','white',GRAD_VER,BGRAD_PLOT);
	$bplot->setBackground(Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_HORIZONTAL, 'white', 'white')));
	//$bplot->setBackground(Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_HORIZONTAL, 'white', '#E5E5E5')));
	//$bplot->SetFillGradient("navy","lightsteelblue",GRAD_MIDVER);

	//$graph->SetFrame(false);
	//$graph->SetMarginColor('cadetblue2');
	//$graph->ygrid->SetFill(true,'azure1','azure2');
	//$graph->xgrid->Show();

	// Add the bar to the graph
	//$graph->Add($bplot);
	
	// Setup title
	//$titlestr->setText($title);

	// Setup X-axis
	$xaxis =& $plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
	$yaxis =& $plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
	$yaxis->setFontSize(10);
	
	// Invert X-axis and put Y-axis at bottom
	$xaxis->setInverted(true);
	$yaxis->setAxisIntersection('max');
			
	// set grid
	$gridY =& $plotarea->addNew('line_grid', IMAGE_GRAPH_AXIS_Y);
	$gridY->setLineColor('#FFFFFF@0.5');
	$gridY2 =& $plotarea->addNew('bar_grid', null, IMAGE_GRAPH_AXIS_Y); 
	$gridY2->setFillColor('#FFFFFF@0.2');


	// Add some grace to y-axis so the bars doesn't go
	// all the way to the end of the plot area
	$yaxis->forceMaximum(round(($max * 1.1) + 0.5));
	$ticks = get_tickspacing(round(($max * 1.1) + 0.5));

	// First make the labels look right
	$yaxis->setLabelInterval($ticks[0]);
	$yaxis->setTickOptions(-5,0);
	$yaxis->setLabelInterval($ticks[1],2);
	$yaxis->setTickOptions(-2,0,2);
	
	// Create the xaxis labels
	$array_data =& Image_Graph::factory('Image_Graph_DataPreprocessor_Array', 
	    array($xlabels) 
	); 

	// The fix the tick marks
	$xaxis->setDataPreprocessor($array_data);
	$xaxis->forceMinimum(0);
	$xaxis->forceMaximum(2*count($datay));
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
	$marker_pointing =& $graph->addNew('Image_Graph_Marker_Pointing', array(40,0,& $marker));
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
