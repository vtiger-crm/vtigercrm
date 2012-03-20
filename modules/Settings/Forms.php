<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header$
 * Description:  Contains a variety of utility functions used to display UI
 * components such as form vtiger_headers and footers.  Intended to be modified on a per
 * theme basis.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

/**
 * Create javascript to validate the data entered into a record.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */

 require_once('include/utils/utils.php'); //new

function get_validate_record_js () {
global $mod_strings;
global $app_strings;

$lbl_last_name = $mod_strings['LBL_LIST_LAST_NAME'];
$lbl_email_id = $mod_strings['LBL_EMAIL_ADDRESS'];
$lbl_user_name = $app_strings['LBL_LIST_USER_NAME'];
$lbl_password = $mod_strings['LBL_LIST_PASSWORD'];
$lbl_servername = $mod_strings['LBL_MAIL_SERVER_NAME'];
$err_missing_required_fields = $app_strings['ERR_MISSING_REQUIRED_FIELDS'];
$err_invalid_email_address = $app_strings['ERR_INVALID_EMAIL_ADDRESS'];
$err_invalid_page_count = $app_strings['ERR_INVALID_PAGE_COUNT'];
$the_script  = <<<EOQ

<script type="text/javascript" language="Javascript">
<!--  to hide script contents from old browsers

function verify_data(form) {
	var isError = false;
	var errorMessage = "";
	if (trim(form.email.value) == "") {
		isError = true;
		errorMessage += "\\n$lbl_email_id";
	}
	if (trim(form.server_username.value) == "") {
		isError = true;
		errorMessage += "\\n$lbl_user_name";
	}
	if (trim(form.server_password.value) == "") {
		isError = true;
		errorMessage += "\\n$lbl_password";
	}
	if (trim(form.mail_servername.value) == "") {
		isError = true;
		errorMessage += "\\n$lbl_servername";
	}

	if (isError == true) {
		alert("$err_missing_required_fields" + errorMessage);
		return false;
	}
	form.email.value = trim(form.email.value);
	if (form.email.value != "" && !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(form.email.value)) {
		alert('"' + form.email.value + '" $err_invalid_email_address');
		return false;
	}
	if(form.mails_per_page.value != "" && isNaN(form.mails_per_page.value)){
		alert('"' + form.mails_per_page.value + '" $err_invalid_page_count');
		return false;
	}
		
	return true;
}

// end hiding contents from old browsers  -->
</script>

EOQ;

return $the_script;
}

/**
 * Create HTML form to enter a new record with the minimum necessary vtiger_fields.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */

function get_new_record_form () {
global $mod_strings;
global $app_strings;
global $current_user;
global $adb;//for dynamic quickcreateform construction


$lbl_required_symbol = $app_strings['LBL_REQUIRED_SYMBOL'];
$lbl_first_name = $mod_strings['LBL_FIRST_NAME'];
$lbl_last_name = $mod_strings['LBL_LAST_NAME'];
$lbl_account_name = $mod_strings['LBL_ACCOUNT_NAME'];
$lbl_phone = $mod_strings['LBL_PHONE'];
$lbl_email_address = $mod_strings['LBL_EMAIL_ADDRESS'];
$lbl_save_button_title = $app_strings['LBL_SAVE_BUTTON_TITLE'];
$lbl_save_button_key = $app_strings['LBL_SAVE_BUTTON_KEY'];
$lbl_save_button_label = $app_strings['LBL_SAVE_BUTTON_LABEL'];
$user_id = $current_user->id;

$qcreate_form = get_left_form_header($mod_strings['LBL_NEW_FORM_TITLE']);


$qcreate_get_field="select * from vtiger_field where tabid=4 and quickcreate=0 and vtiger_field.presence in (0,2) order by quickcreatesequence";
$qcreate_get_result=$adb->pquery($qcreate_get_field, array());
$qcreate_get_noofrows=$adb->num_rows($qcreate_get_result);

$fieldName_array = Array();//for validation

$qcreate_form.='<form name="EditView" onSubmit="if(formValidate()){VtigerJS_DialogBox.block();} else {return false;}" method="POST" action="index.php">';
$qcreate_form.='<input type="hidden" name="module" value="Contacts">';
$qcreate_form.='<input type="hidden" name="record" value="">';
$qcreate_form.='<input type="hidden" name="assigned_user_id" value="'.$user_id.'">';
$qcreate_form.='<input type="hidden" name="email2" value="">';
$qcreate_form.='<input type="hidden" name="action" value="Save">';
//$qcreate_form.='<input type="hidden" name="return_action" value="index">';
//$qcreate_form.='<input type="hidden" name="return_module" value="Contacts">';

$qcreate_form.='<table>';

for($j=0;$j<$qcreate_get_noofrows;$j++)
{
        $qcreate_form.='<tr>';
        $fieldlabel=$adb->query_result($qcreate_get_result,$j,'fieldlabel');
        $uitype=$adb->query_result($qcreate_get_result,$j,'uitype');
        $tabid=$adb->query_result($qcreate_get_result,$j,'tabid');

        $fieldname=$adb->query_result($qcreate_get_result,$j,'fieldname');//for validation
        $typeofdata=$adb->query_result($qcreate_get_result,$j,'typeofdata');//for validation
        $qcreate_form .= get_quickcreate_form($fieldlabel,$uitype,$fieldname,$tabid);


        //to get validationdata
        //start
        $fldLabel_array = Array();
        $fldLabel_array[$fieldlabel] = $typeofdata;
        $fieldName_array['QCK_'.$fieldname] = $fldLabel_array;

        //end

        $qcreate_form.='</tr>';

}


//for validation
$validationData = $fieldName_array;
$fieldName = '';
$fieldLabel = '';
$fldDataType = '';

$rows = count($validationData);
foreach($validationData as $fldName => $fldLabel_array)
{
   if($fieldName == '')
   {
     $fieldName="'".$fldName."'";
   }
   else
   {
     $fieldName .= ",'".$fldName ."'";
   }
   foreach($fldLabel_array as $fldLabel => $datatype)
   {
        if($fieldLabel == '')
        {

                $fieldLabel = "'".$fldLabel ."'";
        }
        else
        {
                $fieldLabel .= ",'".$fldLabel ."'";
        }
        if($fldDataType == '')
        {
                $fldDataType = "'".$datatype ."'";
        }
        else
        {
                $fldDataType .= ",'".$datatype ."'";
        }
   }
}

$qcreate_form.='</table>';


$qcreate_form.='<input title="'.$lbl_save_button_title.'" accessKey="'.$lbl_save_button_key.'" class="button" type="submit" name="button" value="'.$lbl_save_button_label.'" >';
$qcreate_form.='</form>';
$qcreate_form.='<script type="text/javascript">

        var fieldname = new Array('.$fieldName.')
        var fieldlabel = new Array('.$fieldLabel.')
        var fielddatatype = new Array('.$fldDataType.')

                </script>';

$qcreate_form .= get_left_form_footer();
return $qcreate_form;




}

/*function get_new_record_form () {
global $mod_strings;
global $app_strings;
global $current_user;

$lbl_required_symbol = $app_strings['LBL_REQUIRED_SYMBOL'];
$lbl_first_name = $mod_strings['LBL_FIRST_NAME'];
$lbl_last_name = $mod_strings['LBL_LAST_NAME'];
$lbl_phone = $mod_strings['LBL_PHONE'];
$lbl_email_address = $mod_strings['LBL_EMAIL_ADDRESS'];
$lbl_save_button_title = $app_strings['LBL_SAVE_BUTTON_TITLE'];
$lbl_save_button_key = $app_strings['LBL_SAVE_BUTTON_KEY'];
$lbl_save_button_label = $app_strings['LBL_SAVE_BUTTON_LABEL'];
$user_id = $current_user->id;

$the_form = get_left_form_header($mod_strings['LBL_NEW_FORM_TITLE']);
$the_form .= <<<EOQ

		<form name="ContactSave" onSubmit="return verify_data(ContactSave)" method="POST" action="index.php">
			<input type="hidden" name="module" value="Contacts">
			<input type="hidden" name="record" value="">
			<input type="hidden" name="assigned_user_id" value='${user_id}'>
			<input type="hidden" name="email2" value="">
			<input type="hidden" name="action" value="Save">
		$lbl_first_name<br>
		<input name='first_name' type="text" value=""><br>
		<FONT class="required">$lbl_required_symbol</FONT>$lbl_last_name<br>
		<input name='last_name' type="text" value=""><br>
		$lbl_phone<br>
		<input name='phone_work' type="text" value=""><br>
		$lbl_email_address<br>
		<input name='email1' type="text" value=""><br><br>
		<input title="$lbl_save_button_title" accessKey="$lbl_save_button_key" class="button" type="submit" name="button" value="  $lbl_save_button_label  " >
		</form>

EOQ;
$the_form .= get_left_form_footer();
$the_form .= get_validate_record_js();

return $the_form;
}*/

?>
