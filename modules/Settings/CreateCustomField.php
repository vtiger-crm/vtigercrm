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
$image_path="themes/images/";

$tabid=vtlib_purify($_REQUEST['tabid']);
$fieldid=vtlib_purify($_REQUEST['fieldid']);

// Set the tab type only during Custom Field creation for Calendar module based on the activity type
if ($fieldid == '' && $_REQUEST['fld_module'] == 'Calendar' && isset($_REQUEST['activity_type'])) {
	$activitytype = vtlib_purify($_REQUEST['activity_type']);
	if ($activitytype == 'E') $tabid = '16';
	if ($activitytype == 'T') $tabid = '9';
}

 $blockid = getBlockId($tabid,'LBL_CUSTOM_INFORMATION');

if(isset($_REQUEST['uitype']) && $_REQUEST['uitype'] != '')
	$uitype=vtlib_purify($_REQUEST['uitype']);
else
	$uitype=1;

$readonly = '';
$smarty = new vtigerCRM_Smarty;
$cfimagecombo = Array($image_path."text.gif",
                        $image_path."number.gif",
                        $image_path."percent.gif",
                        $image_path."cfcurrency.gif",
                        $image_path."date.gif",
                        $image_path."email.gif",
                        $image_path."phone.gif",
                        $image_path."cfpicklist.gif",
                        $image_path."url.gif",
                        $image_path."checkbox.gif",
                        $image_path."text.gif",
                        $image_path."cfpicklist.gif",
						$image_path."skype.gif");

$cftextcombo = Array($mod_strings['Text'],
                        $mod_strings['Number'],
                        $mod_strings['Percent'],
                        $mod_strings['Currency'],
                        $mod_strings['Date'],
                        $mod_strings['Email'],
                        $mod_strings['Phone'],
                        $mod_strings['PickList'],
                        $mod_strings['LBL_URL'],
                        $mod_strings['LBL_CHECK_BOX'],
                        $mod_strings['LBL_TEXT_AREA'],
                        $mod_strings['LBL_MULTISELECT_COMBO'],
						$mod_strings['Skype']
				);	
				
$typeVal = Array(
	'0'=>'Text',
	'1'=>'Number',
	'2'=>'Percent',
	'3'=>'Currency',
	'4'=>'Date',
	'5'=>'Email',
	'6'=>'Phone',
	'7'=>'Picklist',
	'8'=>'URL',
	'9'=>'Checkbox',
	'11'=>'MultiSelectCombo',
	'12'=>'Skype');
if(isset($fieldid) && $fieldid!='')
{
	$mode='edit';
	$customfield_columnname=getCustomFieldData($tabid,$fieldid,'columnname');
	$customfield_typeofdata=getCustomFieldData($tabid,$fieldid,'typeofdata');
	$customfield_fieldlabel=getCustomFieldData($tabid,$fieldid,'fieldlabel');
	$customfield_typename=getCustomFieldTypeName($uitype);
	$fieldtype_lengthvalue=getFldTypeandLengthValue($customfield_typename,$customfield_typeofdata);
	list($fieldtype,$fieldlength,$decimalvalue)= explode(";",$fieldtype_lengthvalue);
	$readonly = "readonly";
	if($fieldtype == '7' || $fieldtype == '11')
	{
		$query = "select * from vtiger_". $adb->sql_escape_string($customfield_columnname);
		$result = $adb->pquery($query, array());
		$fldVal='';
		while($row = $adb->fetch_array($result))
		{
			$fldVal .= $row[$customfield_columnname];
			$fldVal .= "\n";
		}
		$smarty->assign("PICKLISTVALUE",$fldVal);
	}
	$selectedvalue = $typeVal[$fieldtype];
}
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("FLD_MODULE", vtlib_purify($_REQUEST['fld_module']));
if(isset($_REQUEST["duplicate"]) && $_REQUEST["duplicate"] == "yes")
{
	$error=$mod_strings['ERR_CUSTOM_FIELD_WITH_NAME']. $_REQUEST["fldlabel"] .$mod_strings['ERR_ALREADY_EXISTS'] . ' ' .$mod_strings['ERR_SPECIFY_DIFFERENT_LABEL'];
	$smarty->assign("DUPLICATE_ERROR", $error);
	$customfield_fieldlabel=vtlib_purify($_REQUEST["fldlabel"]);
	$fieldlength=vtlib_purify($_REQUEST["fldlength"]);
	$decimalvalue=vtlib_purify($_REQUEST["flddecimal"]);
	$fldVal = vtlib_purify($_REQUEST["fldPickList"]);
	$selectedvalue = $typeVal[$_REQUEST["fldType"]];
}
elseif($fieldid == '')
{
	$selectedvalue = "0";
}

if($mode == 'edit')
	$disable_str = 'disabled' ;
else
	$disable_str = '' ;

$output = '';

$combo_output = '';
for($i=0;$i<count($cftextcombo);$i++)
{
        if($selectedvalue == $i && $fieldid != '')
                $sel_val = 'selected';
        else
                $sel_val = '';
	$combo_output.= '<a href="javascript:void(0);" onClick="makeFieldSelected(this,'.$i.','.$blockid.');" id="field'.$i.'" style="text-decoration:none;background-image:url('.$cfimagecombo[$i].');" class="customMnu" '.$disable_str.'>'.$cftextcombo[$i].'</a>';

}
$output .= '<div id="customfield" style="display:block;" class="layerPopup"><script language="JavaScript" type="text/javascript" src="include/js/customview.js"></script>
			<form action="index.php" method="post" name="addtodb" onSubmit="if(validate('.$blockid.')) {VtigerJS_DialogBox.block();}else{return false;}">
	  		<input type="hidden" name="module" value="Settings">
	  		<input type="hidden" name="fld_module" value="'.vtlib_purify($_REQUEST['fld_module']).'">
	  		<input type="hidden" name="activity_type" value="'.$activitytype.'">
	  		<input type="hidden" name="parenttab" value="Settings">
      		<input type="hidden" name="action" value="AddCustomFieldToDB">
		  	<input type="hidden" name="fieldid" value="'.$fieldid.'">
	  		<input type="hidden" name="column" value="'.$customfield_columnname.'">
	  		<input type="hidden" name="mode" id="cfedit_mode" value="'.$mode.'">
	  		<input type="hidden" name="cfcombo" id="selectedfieldtype" value="">
			<input type="hidden" name="blockid" value="'.$blockid.'">
		
	  
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
			<tr>';
			if($mode == 'edit')
				$output .= '<td width="60%" align="left" class="layerPopupHeading">'.$mod_strings['LBL_EDIT_FIELD_TYPE'].' - '.$customfield_typename.'</td>';
			else
				$output .= '<td width="60%" align="left" class="layerPopupHeading">'.$mod_strings['LBL_ADD_FIELD'].'</td>';
				
			$output .= '<td width="40%" align="right"><a href="javascript:fninvsh(\'customfield\');"><img src="'. vtiger_imageurl('close.gif', $theme).'" border="0"  align="absmiddle" /></a></td>
			</tr>';
			$output .='</table><table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
							<tr>
								<td class=small >
									<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
										<tr>';
			if($mode != 'edit')
			{	
				$output .= '<td><table>
						<tr><td>'.$mod_strings['LBL_SELECT_FIELD_TYPE'].'</td></tr>
						<tr><td>
							<div name="cfcombo" id="cfcombo" class=small  style="width:205px;height:150px;overflow-y:auto;overflow-x:hidden;overflow:auto;border:1px solid #CCCCCC;">'.$combo_output.'</div>
						</td></tr>
						</table></td>';
			}
			$output .='<td width="50%">
					<table width="100%" border="0" cellpadding="5" cellspacing="0">
						<tr>
							<td class="dataLabel" nowrap="nowrap" align="right" width="30%"><b>'.$mod_strings['LBL_LABEL'].' </b></td>
							<td align="left" width="70%"><input name="fldLabel_'.$blockid.'" id ="fldLabel_'.$blockid.'" value="'.$customfield_fieldlabel.'" type="text" class="txtBox"></td>
						</tr>';
					if($mode != 'edit') {
						$output .= '<tr id="lengthdetails_'.$blockid.'">
							<td class="dataLabel" nowrap="nowrap" align="right"><b>'.$mod_strings['LBL_LENGTH'].'</b></td>
							<td align="left"><input type="text" name="fldLength_'.$blockid.'" id= ="fldLength_'.$blockid.'" value="'.$fieldlength.'" '.$readonly.' class="txtBox"></td>
						</tr>
						<tr id="decimaldetails_'.$blockid.'" style="visibility:hidden;">
							<td class="dataLabel" nowrap="nowrap" align="right"><b>'.$mod_strings['LBL_DECIMAL_PLACES'].'</b></td>
							<td align="left"><input type="text" name="fldDecimal_'.$blockid.'" id= "fldDecimal_'.$blockid.'" value="'.$decimalvalue.'" '.$readonly.' class="txtBox"></td>
						</tr>
						<tr id="picklistdetails_'.$blockid.'" style="visibility:hidden;">
							<td class="dataLabel" nowrap="nowrap" align="right" valign="top"><b>'.$mod_strings['LBL_PICK_LIST_VALUES'].'</b></td>
							<td align="left" valign="top"><textarea name="fldPickList_'.$blockid.'"  id = ="fldPickList_'.$blockid.'" rows="10" class="txtBox" '.$readonly.'>'.$fldVal.'</textarea></td>
						</tr>';
					}
				$output .= '	
					</table>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
			<tr>
				<td align="center">
					<input type="submit" name="save" value=" &nbsp; '.$app_strings['LBL_SAVE_BUTTON_LABEL'].' &nbsp; " class="crmButton small save" />&nbsp;
					<input type="button" name="cancel" value=" '.$app_strings['LBL_CANCEL_BUTTON_LABEL'].' " class="crmButton small cancel" onclick="fninvsh(\'customfield\');" />
				</td>
			</tr>
	</table>
		<input type="hidden"  name="fieldType_'.$blockid.'" id="fieldType_'.$blockid.'" value="'.$selectedvalue.'">
		<input type="hidden" name="selectedfieldtype_'.$blockid.'"id="selectedfieldtype_'.$blockid.'"value="">
	</form></div>';
echo $output;
?>