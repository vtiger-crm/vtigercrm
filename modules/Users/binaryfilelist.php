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

require_once('include/database/PearDatabase.php');

//or die("Couldn't connect to database $dbDatabase");

function getAttachmentsList()
{
	global $theme,$adb;
	global $app_strings;
	global $mod_strings;

	$dbQuery = "SELECT templateid, filename,filesize,filetype,description,module ";
	$dbQuery .= "FROM vtiger_wordtemplates" ;
	$dbQuery .= " ORDER BY filename ASC";

	//echo $dbQuery;

	$result = $adb->pquery($dbQuery, array()) or die("Couldn't get file list");

$list = '<table border="0" cellpadding="5" cellspacing="1" class="FormBorder" width="90%">';

$list .= '<tr height=20>';
$list .='<td width="20%" class="moduleListTitle" style="padding:0px 3px 0px 3px;"><div align="center"><b>'.$app_strings['LBL_OPERATION'].'</b></div></td>';
$list .= '';

$list .= '<td width="20%" class="moduleListTitle" style="padding:0px 3px 0px 3px;"><b>';
$list .= $app_strings['LBL_FILENAME'].'</b></td>';

$list .= '<td width="10%" class="moduleListTitle" style="padding:0px 3px 0px 3px;"><b>';
$list .= $mod_strings['LBL_MODULENAMES'].'</b></td>';

$list .= '<td width="20%" class="moduleListTitle" style="padding:0px 3px 0px 3px;"><b>';
$list .= $app_strings['LBL_UPD_DESC'].'</b></td>';

$list .= '<td width="15%" class="moduleListTitle" style="padding:0px 3px 0px 3px;"><b>';
$list .= $app_strings['LBL_TYPE'].'</td></b>';

$list .= '<td width="15%" class="moduleListTitle" style="padding:0px 3px 0px 3px;"><b>';
$list .= $app_strings['LBL_FILE'].'</b></td>';

$list .= '</tr>';

//$list .= '<tr><td COLSPAN="7" class="blackLine"><IMG SRC="themes/images/blank.gif"></td></tr>';

$i=1;
while($row = $adb->fetch_array($result))
{


if ($i%2==0)
$trowclass = 'evenListRow';
else
$trowclass = 'oddListRow';
	$list .= '<tr class="'. $trowclass.'"><td style="padding:0px 3px 0px 3px;" align="center"><a href="index.php?module=Users&action=deletewordtemplate&record='.$row["templateid"].'"> Del </a> </td><td height="21" style="padding:0px 3px 0px 3px;">';

	 $list .= $row["filename"]; 

	$list .= '</td>';
	
	$list .= '<td height="21" style="padding:0px 3px 0px 3px;">'.$row["module"].'</td>';
	$list .= '<td height="21" style="padding:0px 3px 0px 3px;">';

	 $list .= $row["description"]; 

	$list .= '</td>';
	
	$list .= '<td height="21" style="padding:0px 3px 0px 3px;">';

	$list .= $row["filetype"]; 

	$list .= '</td>';

	$list .= '<td height="21" style="padding:0px 3px 0px 3px;">';

	$list .= '<a href="index.php?module=Users&action=downloadfile&record='.$row['templateid'] .'">';

	$list .= $app_strings['LBL_DOWNLOAD'];

	$list .= '</a></td></tr>';
$i++;
}

	$list .= '</table>';

return $list;
}
?>
