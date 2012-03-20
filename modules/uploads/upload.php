<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $theme;
$theme_path="themes/".$theme."/";
?>
<HTML>
<head>
	<link type="text/css" href="<?php echo $theme_path;?>style.css" rel="stylesheet">
	<script>
                function titleValidation()
                {
                        var val = document.getElementById('uploadsubject').value;
                        if(trim(val) == '')
                        {
                                alert("Title cannot be empty");
                                return false;
                        }
                }

		function getFileNameOnly(filename) {
			var onlyfilename = filename;
			// Normalize the path (to make sure we use the same path separator)
			var filename_normalized = filename.replace(/\\/g, '/');
			if(filename_normalized.lastIndexOf("/") != -1) {
				onlyfilename = filename_normalized.substring(filename_normalized.lastIndexOf("/") + 1);
			}
			return onlyfilename;
		}

		/* Function to validate the filename */
		function validateFilename(form_ele) {
			if (form_ele.value == '') return true;
			var value = getFileNameOnly(form_ele.value);

			// Color highlighting logic
			var err_bg_color = "#FFAA22";

			if (typeof(form_ele.bgcolor) == "undefined") {
				form_ele.bgcolor = form_ele.style.backgroundColor;
			}

			// Validation starts here
			var valid = true;

			/* Filename length is constrained to 255 at database level */
			if (value.length > 255) {
				alert(alert_arr.LBL_FILENAME_LENGTH_EXCEED_ERR);
				valid = false;
			}

			if (!valid) {
				form_ele.style.backgroundColor = err_bg_color;
				return false;
			}
			form_ele.style.backgroundColor = form_ele.bgcolor;
			form_ele.form[form_ele.name + '_hidden'].value = value;
			return true;
		}
		function trim(str)
		{
			// str. remove whitespaces from left. remove whitespaces from right
			return str.replace(/^\s+/g, "").replace(/\s+$/g, "");
		}
        </script>
</head>
<BODY marginheight="0" marginwidth="0" leftmargin="0" rightmargin="0" bottommargin="0" topmargin="0">
<FORM METHOD="post" onsubmit="VtigerJS_DialogBox.block();" action="index.php?module=uploads&action=add2db&return_module=<?php echo vtlib_purify($_REQUEST['return_module'])?>" enctype="multipart/form-data" style="margin:0px;">
<?php
	$ret_module = ($_REQUEST['return_module'] != "")?vtlib_purify($_REQUEST['return_module']):$_SESSION['return_mod'];
	$ret_action = ($_REQUEST['return_action'] != "")?vtlib_purify($_REQUEST['return_action']):$_SESSION['return_act'];
	$ret_id = ($_REQUEST['return_id'] != "")?vtlib_purify($_REQUEST['return_id']):$_SESSION['returnid'];

	$_SESSION['return_act'] = $ret_action;	
	$_SESSION['return_mod'] = $ret_module;	
	$_SESSION['returnid'] = $ret_id;	
?>

<INPUT TYPE="hidden" NAME="MAX_FILE_SIZE" VALUE="<?php echo $upload_maxsize; ?>">
<INPUT TYPE="hidden" NAME="return_module" VALUE="<?php echo $ret_module ?>">
<INPUT TYPE="hidden" NAME="return_action" VALUE="<?php echo $ret_action ?>">
<INPUT TYPE="hidden" NAME="return_id" VALUE="<?php echo $ret_id ?>">
<table border=0 cellspacing=0 cellpadding=0 width=100% class="layerPopup">
<tr>
<td>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
		<tr>
			<td class="layerPopupHeading" align="left"><?php echo $mod_strings["LBL_ATTACH_FILE"];?></td>
			<td width="70%" align="right">&nbsp;</td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
		<tr>
			<td class=small >
				<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white class="small">		
					<tr>
						<td width="30%" colspan="2" align="left">
							<b><?php echo $mod_strings["LBL_STEP_SELECT_FILE"];?></b><br>
							<?php echo $mod_strings["LBL_BROWSE_FILES"]; ?>
						</td>
					</tr>
					<tr><td><B><font color=red>*</font>&nbsp;<?php echo $app_strings["LBL_TITLE"];?> </B> <input type ="text" name = "uploadsubject" id="uploadsubject"></td></tr>
					<tr>
						<td width="30%" colspan="2" align="left">
							&nbsp;<input type="file" name="filename" onchange="validateFilename(this)" /><input type="hidden" name="filename_hidden"/>
						</td>
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr>
						<td width="30%" colspan="2" align="left">
							<b> <?php echo $mod_strings["LBL_DESCRIPTION"];?> </b><?php echo $mod_strings["LBL_OPTIONAL"];?>
						</td>
					</tr>
					<tr><td colspan="2" align="left"><textarea cols="50" rows="5"  name="txtDescription" class="txtBox"></textarea></td></tr>
				</table>
			</td>
		</tr>
	</table>
	
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
		<tr>
			<td colspan="2" align="center">
					<input type="submit" name="save" value="&nbsp;<?php echo $mod_strings["LBL_ATTACH"]; ?>&nbsp;" class="crmbutton small save" onclick = "return titleValidation();" />&nbsp;&nbsp;
					<input type="button" name="cancel" value=" <?php echo $mod_strings["LBL_CANCEL"];?> " class="crmbutton small cancel" onclick="self.close();" />
			</td>	
		</tr>
	</table>
</td>
</tr>
</table>
</FORM>
</BODY>
</HTML>
