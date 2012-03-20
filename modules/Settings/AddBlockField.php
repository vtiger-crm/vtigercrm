<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
 
require_once('include/CustomFieldUtil.php');
require_once('Smarty_setup.php');


global $mod_strings,$app_strings,$app_list_strings,$theme,$adb,$log;

$theme_path="themes/".$theme."/";

require_once($theme_path.'layout_utils.php');
$tabid=vtlib_purify($_REQUEST['tabid']);
$mode = vtlib_purify($_REQUEST['mode']);
$fieldid=vtlib_purify($_REQUEST['fieldselect']);
if(isset($_REQUEST['uitype']) && $_REQUEST['uitype'] != '')
	$uitype=vtlib_purify($_REQUEST['uitype']);
else
	$uitype=1;
$readonly = '';
$smarty = new vtigerCRM_Smarty;
if($_REQUEST['mode']=='edit')
{
	$mode='edit';
	$customfield_columnname=getCustomFieldData($tabid,$fieldid,'columnname');
	$customfield_typeofdata=getCustomFieldData($tabid,$fieldid,'typeofdata');
	$customfield_fieldlabel=getCustomFieldData($tabid,$fieldid,'fieldlabel');
	
	
	$customfield_typename=getCustomFieldTypeName($uitype);
	$fieldtype_lengthvalue=getFldTypeandLengthValue($customfield_typename,$customfield_typeofdata);
	list($fieldtype,$fieldlength,$decimalvalue)= explode(";",$fieldtype_lengthvalue);
	$readonly = "readonly";

	$selectedvalue = $typeVal[$fieldtype];
}

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("FLD_MODULE", vtlib_purify($_REQUEST['fld_module']));

$output = '';

$combo_output = '';



$output .= '<div id="orgLay" style="display:block;" class="layerPopup"><script language="JavaScript" type="text/javascript" src="include/js/customview.js"></script>
			<form action="index.php" method="post" name="addtodb" onsubmit="VtigerJS_DialogBox.block();">
			<input type="hidden" name="module" value="Settings">
			<input type="hidden" name="fld_module" value="'.vtlib_purify($_REQUEST['fld_module']).'">
			<input type="hidden" name="parenttab" value="Settings">
			<input type="hidden" name="action" value="AddBlockFieldToDB">
			<input type="hidden" name="blockid" id="blockid" value="'.vtlib_purify($_REQUEST['blockid']).'">
			<input type="hidden" name="tabid" id="tabid" value="'.vtlib_purify($_REQUEST['tabid']).'">
			<input type="hidden" name="fieldselect" value="'.vtlib_purify($_REQUEST['fieldselect']).'">
			<input type="hidden" name="column" value="'.$customfield_columnname.'">
			<input type="hidden" name="mode" id="cfedit_mode" value="'.$mode.'">
			<input type="hidden" name="cfcombo" id="selectedfieldtype" value="">

	  
			<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
				<tr>';
			if($mode == 'edit')
				$output .= '<td width="60%" align="left" class="layerPopupHeading">Edit Field</td>';
			else
				$output .= '<td width="95%" align="left" class="layerPopupHeading">'.getTranslatedString('LBL_MOVE_BLOCK_FIELD').vtlib_purify($_REQUEST['blockname']).'</td>';
				
			$output .= '<td width="5%" align="right"><a href="javascript:fninvsh(\'orgLay\');"><img src="'. vtiger_imageurl('close.gif', $theme) .'" border="0"  align="absmiddle" /></a></td>
			</tr>';
			$output .='</table><table border=0 cellspacing=0 cellpadding=0 width=95% align=center> 
							<tr>
								<td class=small >
									<table border=0 celspacing=0 cellpadding=0 width=100% align=center bgcolor=white>
										<tr>';
			if($mode != 'edit')
		    {						
				$output .= '<td><table>
					<tr><td>'.getTranslatedString('LBL_SELECT_FIELD_TO_MOVE').'</td></tr>
					<tr><td>COMBO_OUTPUT_CHANGE</td></tr>
					</table></td>';
			}
			$output .= '</tr>
			</table>
		</td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
			<tr>
				<td align="center">';
					$output .= '<input type="submit" name="save" value=" &nbsp; '.$app_strings['LBL_ASSIGN_BUTTON_LABEL'].' &nbsp; " class="crmButton small save" />';
					$output .= '&nbsp;
					<input type="button" name="cancel" value=" '.$app_strings['LBL_CANCEL_BUTTON_LABEL'].' " class="crmButton small cancel" onclick="fninvsh(\'orgLay\');" />
				</td>
			</tr>
	</table>
		<input type="hidden" name="fieldType" id="fieldType" value="'.$selectedvalue.'">
	</form></div>';


function InStrCount($String,$Find,$CaseSensitive = false) {
    $i=0;
    $x=0;
    while (strlen($String)>=$i) {
     unset($substring);
     if ($CaseSensitive) {
      $Find=strtolower($Find);
      $String=strtolower($String);
     }
     $substring=substr($String,$i,strlen($Find));
     if ($substring==$Find) $x++;
     $i++;
    }
    return $x;
   }
 
$sql="SELECT fieldid,fieldlabel,fieldname FROM vtiger_field WHERE tabid=? AND block != ? AND block NOT IN (SELECT blockid from vtiger_blocks where blocklabel='LBL_RELATED_PRODUCTS') AND displaytype in (1,2,4) and vtiger_field.presence in (0,2) ORDER BY fieldlabel ASC"; // added by projjwal on 22-11-2007
$res= $adb->pquery($sql,array($_REQUEST['tabid'], $_REQUEST['blockid']));

$combo_output='<select name="field_assignid[]" style="width:250px" size=10 multiple>';// added by projjwal on 22-11-2007
while($row_field = $adb->fetch_array($res))
{
	$combo_output.='<option value="'.$row_field['fieldid'].'">'.getTranslatedString($row_field['fieldlabel'],$_REQUEST['fld_module']).'</option>'; 
}
$combo_output.='</select>';


$output=str_replace('COMBO_OUTPUT_CHANGE',$combo_output,$output);	
 
echo $output;
?>