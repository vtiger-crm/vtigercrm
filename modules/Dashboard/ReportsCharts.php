<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include("modules/Dashboard/Entity_charts.php");
include("modules/Dashboard/horizontal_bargraph.php");
include("modules/Dashboard/pie_graph.php");
	
global $tmp_dir;
function get_graph_by_type($graph_by,$graph_title,$module,$where,$query)
{
	global $user_id,$date_start,$end_date,$type;

	//Giving the Cached image name	
	$cache_file_name=abs(crc32($user_id))."_".$type."_".crc32($date_start.$end_date).".png";
        $html_imagename=$graph_by; //Html image name for the graph

        $graph_details=module_Chart($user_id,$date_start,$end_date,$query,$graph_by,$graph_title,$where,$module,$type);

        if($graph_details!=0)
        {
                $name_val=$graph_details[0];
                $cnt_val=$graph_details[1];
                $graph_title=$graph_details[2];
                $target_val=$graph_details[3];
                $graph_date=$graph_details[4];
                $urlstring=$graph_details[5];
                $cnt_table=$graph_details[6];
	       	$test_target_val=$graph_details[7];


                $width=600;
                $height=400;
                $top=30;
                $left=140;
                $bottom=120;
                $title=$graph_title;

                return get_graph($cache_file_name,$html_imagename,$cnt_val,$name_val,$width,$height,$left,$right,$top,$bottom,$title,$target_val,$graph_date,$urlstring,$test_target_val,$date_start,$end_date);
        }
	else
	{
	
	}
 
}

/** Returns  the Horizontal,vertical, pie graphs and Accumulated Graphs 
for the details
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
* Contributor(s): ______________________________________..
*/


// Function for get graphs
function get_graph($cache_file_name,$html_imagename,$cnt_val,$name_val,$width,$height,$left,$right,$top,$bottom,$title,$target_val,$graph_date,$urlstring,$test_target_val,$date_start,$end_date)
{

	global $tmp_dir;
     global $graph_title;
	$val=explode(":",$title); 		
	$display_title=$val[0];	
			
	
$sHTML .= "<tr>
	   <td><table width=20%  border=0 cellspacing=0 cellpadding=0 align=left>
	  	 <tr>
		   <td rowspan=2 valign=top><span class=dashSerial>1</span></td>
 		   <td nowrap><span class=genHeaderSmall>".$graph_title."</span></td>
 		 </tr>
   		 <tr>
		   <td><span class=big>Horizontal Bar Chart</span> </td>
		 </tr>
		</table>
  	   </td>
	</tr>
	<tr>
	   <td height=200>"; 

	   $sHTML .= render_graph($tmp_dir."hor_".$cache_file_name,$html_imagename."_hor",$cnt_val,$name_val,$width,$height,$left,$right,$top,$bottom,$title,$target_val,"horizontal");
//Commented by Minnie -- same content displayed in to graphs
/*$sHTML .= "</td>
	</tr>
	<tr>
	   <td><hr noshade='noshade' size='1' /></td>
	</tr>";
	
$sHTML .= "<tr>
	   <td><table width=20%  border=0 cellspacing=0 cellpadding=0 align=left>
	  	 <tr>
		   <td rowspan=2 valign=top><span class=dashSerial>2</span></td>
 		   <td nowrap><span class=genHeaderSmall>".$graph_title."</span></td>
 		 </tr>
   		 <tr>
		   <td><span class=big>Vertical Bar Chart</span> </td>
		 </tr>
		</table>
  	   </td>
	</tr>
	<tr>
	   <td height=200>"; 

	   $sHTML .= render_graph($tmp_dir."vert_".$cache_file_name,$html_imagename."_vert",$cnt_val,$name_val,$width,$height,$left,$right,$top,$bottom,$title,$target_val,"vertical");*/

$sHTML .= "</td>
	</tr>
	<tr>
	   <td><hr noshade='noshade' size='1' /></td>
	</tr>";

$sHTML .= "<tr>
	   <td><table width=20%  border=0 cellspacing=0 cellpadding=0 align=left>
	  	 <tr>
		   <td rowspan=2 valign=top><span class=dashSerial>2</span></td>
 		   <td nowrap><span class=genHeaderSmall>".$graph_title."</span></td>
 		 </tr>
   		 <tr>
		   <td><span class=big>Pie Chart</span> </td>
		 </tr>
		</table>
  	   </td>
	</tr>
	<tr>
	   <td height=200>"; 

	   $sHTML .= render_graph($tmp_dir."pie_".$cache_file_name,$html_imagename."_pie",$cnt_val,$name_val,$width,$height,40,$right,$top,$bottom,$title,$target_val,"pie");

$sHTML .= "</td>
	</tr>
	<tr>
	   <td><hr noshade='noshade' size='1' /></td>
	</tr>";

	return $sHTML;
}

/** Returns graph, if the cached image is present it'll display that image,
otherwise it will render the graph with the given details	
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
* Contributor(s): ______________________________________..
*/

// Function to get the chached image if exists
function render_graph($cache_file_name,$html_imagename,$cnt_val,$name_val,$width,$height,$left,$right,$top,$bottom,$title,$target_val,$graph_type)
{

	//Checks whether the cached image is present or not
	if(file_exists($cahce_file_name))
	{
		unlink($cache_file_name);
	}
	if(file_exists($cache_file_name.'.map'))
	{
		unlink($cache_file_name.'.map');
	}	
	if (!file_exists($cache_file_name) || !file_exists($cache_file_name.'.map')) 
	{
		//If the Cached image is not present
		if($graph_type=="horizontal")
		{
		 	return horizontal_graph($cnt_val,$name_val,$width,$height,$left,$right,$top,$bottom,$title,$target_val,$cache_file_name,$html_imagename);
		}
		else if($graph_type=="vertical")	
		{
			return vertical_graph($cnt_val,$name_val,$width,$height,$left,$right,$top,$bottom,$title,$target_val,$cache_file_name,$html_imagename);
		}
		else if($graph_type=="pie")
		{
			return pie_chart($cnt_val,$name_val,$width,$height,$left,$right,$top,$bottom,$title,$target_val,$cache_file_name,$html_imagename);
			
		}
	}
	else
	{
		//Getting the cached image
		$imgMap_fp = fopen($cache_file_name.'.map', "rb");
		$imgMap = fread($imgMap_fp, vtiger_filesize($cache_file_name.'.map'));
		fclose($imgMap_fp);
		$base_name_cache_file=basename($cache_file_name);
		$ccc="cache/images/".$base_name_cache_file;
		$return = "\n$imgMap\n";
		$return .= "<img src=$ccc ismap usemap=#$html_imagename border='0'>";
		return $return;
	}
}
?>