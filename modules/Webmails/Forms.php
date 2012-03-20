<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
  * ("License"); You may not use this file except in compliance with the License
  * The Initial Developer of the Original Code is FOSS Labs.
  * Portions created by FOSS Labs are Copyright (C) FOSS Labs.
  * Portions created by vtiger are Copyright (C) vtiger.
  * All Rights Reserved.
  *
  ********************************************************************************/

function get_validate_record_js() {

}
function get_new_record_form () {
	$the_form = '<table width="100%" class="leftFormTable" cellpadding="0" cellspacing="0" border="0" align="center"><tbody><tr>';
	$the_form .= '<td class="leftFormHeader" align="left" height="20" nowrap="nowrap" valign="middle">Folders</td></tbody></tr></table>';
	$the_form .= '<table width="100%" cellpadding="2" cellspacing="0" border="0" align="center" class="leftFormBorder1"><tr> <form><td nowrap>';
	$the_boxes=array();
	if (is_array($list)) {
   		foreach ($list as $key => $val) {
			$the_boxes[] = $val->name;
   		}
	}
	sort($the_boxes);
   	for($i=0;$i<count($the_boxes);$i++) {
        	$the_form .= "<a href='index.php?module=Webmails&action=index&mailbox=".preg_replace(array("/\{.*?\}/i"),array(""),$the_boxes[$i])."' id='".$the_boxes[$i]."'>".preg_replace(array("/\{.*?\}/i"),array(""),$the_boxes[$i])."</a><br>";
   	}

	$the_form .= get_left_form_footer();
	$the_form .= get_validate_record_js();

return $the_form;
}
?>
