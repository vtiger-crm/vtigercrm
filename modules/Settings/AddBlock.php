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

require_once('modules/Vtiger/layout_utils.php');

$tabid=vtlib_purify($_REQUEST['tabid']);
$blockid=vtlib_purify($_REQUEST['blockid']);
$mode=vtlib_purify($_REQUEST['mode']);
$readonly = '';
$smarty = new vtigerCRM_Smarty;
if($_REQUEST['mode']=='edit')
{

$sql='SELECT blocklabel FROM vtiger_blocks WHERE blockid = ?';
$res= $adb->pquery($sql, array($_REQUEST['blockid']));
$row= $adb->fetch_array($res);

checkFileAccessForInclusion('modules/'.$_REQUEST['fld_module'].'/language/'.$_SESSION['authenticated_user_language'].'.lang.php');
include('modules/'.$_REQUEST['fld_module'].'/language/'.$_SESSION['authenticated_user_language'].'.lang.php');

$blockLabel=$mod_strings[$row["blocklabel"]];
}

$blockQuery = 'SELECT blocklabel,blockid FROM vtiger_blocks WHERE tabid = ?';
$block = $adb->pquery($blockQuery, array($_REQUEST['tabid']));
$blocknum = $adb->num_rows($block);

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("FLD_MODULE", vtlib_purify($_REQUEST['fld_module']));

$output = '';

$output .= '<div id="orgLay" style="display:block;" class="layerPopup"><script language="JavaScript" type="text/javascript" src="include/js/customview.js"></script>
			<form action="index.php" method="post" name="addtodb" onsubmit="VtigerJS_DialogBox.block();"> 
			<input type="hidden" name="module" value="Settings">
	  		<input type="hidden" name="fld_module" value="'.vtlib_purify($_REQUEST['fld_module']).'">
	  		<input type="hidden" name="parenttab" value="Settings">
          	<input type="hidden" name="action" value="AddBlockToDB">
	  		<input type="hidden" name="blockid" value="'.vtlib_purify($_REQUEST['blockid']).'">
	   		<input type="hidden" name="tabid" value="'.vtlib_purify($_REQUEST['tabid']).'">
	   		<input type="hidden" name="blockselect" value="'.vtlib_purify($_REQUEST['blockselect']).'">
	  		<input type="hidden" name="mode" id="cfedit_mode" value="'.$mode.'">
	  		<input type="hidden" name="cfcombo" id="selectedfieldtype" value="">

	  
			<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
				<tr>';
				if($mode == 'edit')
					$output .= '<td width="60%" align="left" class="layerPopupHeading">Edit Block</td>';
				else
					$output .= '<td width="95%" align="left" class="layerPopupHeading">Add Block</td>';
				
					$output .= '<td width="5%" align="right"><a href="javascript:fninvsh(\'orgLay\');"><img src="'. vtiger_imageurl('close.gif', $theme) .'" border="0"  align="absmiddle" /></a></td>
				</tr>';
					$output .='' .
			'</table>' .
			'<table border=0 cellspacing=0 cellpadding=0 width=95% align=center> 
				<tr>
					<td class=small >
						<table border=0 celspacing=0 cellpadding=0 width=100% align=center bgcolor=white>
							<tr>';

			$output .= '<td width="5%" align="right"><a href="javascript:fninvsh(\'orgLay\');"><img src="'. vtiger_imageurl('close.gif', $theme) .'" border="0"  align="absmiddle" /></a></td>
			</tr>';
			$output .='</table><table border=0 cellspacing=0 cellpadding=0 width=95% align=center> 
							<tr>
								<td class=small >
									<table border=0 celspacing=0 cellpadding=0 width=100% align=center bgcolor=white>
										<tr>';

				
		
				$output .=		'<td width="50%">
									<table width="100%" border="0" cellpadding="5" cellspacing="0">
										<tr>
											<td class="dataLabel" nowrap="nowrap" align="right" width="30%"><b>Block name</b></td>
											<td align="left" width="70%"><input name="blocklabel" value="'.$blockLabel.'" type="text" class="txtBox"></td>
										</tr>
										<tr>' .
											'<td class="dataLabel" align="right" width="30%"<b>After</b></td>' .
											'<td align="left" width="70%"><select id="blockname" name="after_blockid">' ;
													for($i=0;$i < $blocknum;$i++){
													$blockname = $adb->query_result($block,$i,'blocklabel');
													$blockname = getTranslatedString($blockname, $_REQUEST['fld_module']);
													$blockid = $adb->query_result($block,$i,'blockid');
													$output .="<option value = '".$blockid."'>".$blockname."</option>";
													}
					
				
				$output .= '					</select>
									 		</td>' .
									 	'</tr>' .
									'</table>
								</td>
							</tr>
						</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% >
			<tr>
				<td align="center">
					<input type="button" name="save"  value=" &nbsp; '.$app_strings['LBL_SAVE_BUTTON_LABEL'].'&nbsp; " class="crmButton small save" onclick="return check();"/>&nbsp;
					<input type="button" name="cancel" value=" '.$app_strings['LBL_CANCEL_BUTTON_LABEL'].' " class="crmButton small cancel" onclick="fninvsh(\'orgLay\');" />
				</td>
			</tr>
	</table>
		<input type="hidden" name="fieldType" id="fieldType" value="'.$selectedvalue.'">
			</td>' .
		'</tr>' .
	'</table>' .
'</form>' .
'</div>';
echo $output;
?>