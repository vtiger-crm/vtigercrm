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

global $theme;
global $app_strings;
global $mod_strings;
$theme_path="themes/".$theme."/";
$image_path="themes/images/";


?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $mod_strings['TITLE_USER_DOCUMENT']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $app_strings['LBL_CHARSET']; ?>">
</head>
<body>
<form method="post" action="index.php?module=Users&action=listnotificationschedulers" name="" onsubmit="VtigerJS_DialogBox.block();">
	<TABLE WIDTH="100%" CELLPADDING=0 CELLSPACING=0 BORDER=0>
          <TR> 
	    <TD ALIGN=LEFT CLASS="moduleTitle hline" NOWRAP><?php echo $mod_strings['LBL_NOTIFICATION_EMAIL_INFO']; ?>: 
            </TD>
          </TR>
        </TABLE>
  <br>
  <table width="50%" border=0 cellspacing=1 cellpadding=2 class="formOuterBorder">
      <td class="formSecHeader" colspan=2 nowrap><?php echo $mod_strings['LBL_NOTIFICATION_EMAIL_INFO']; ?>:</td>
    <tr>
      <td nowrap class="dataLabel" width="50%"><?php echo $mod_strings['LBL_NOTIFICATION_ACTIVITY']; ?>:</td>
      <td></td>
    </tr>
    <tr >
      <td nowrap class="dataLabel"><?php echo $mod_strings['LBL_DESCRIPTION']; ?>:</td>
      <td></td>
    </tr>
    <tr >
      <td nowrap class="dataLabel"><?php echo $mod_strings['LBL_ACTIVE']; ?>: </td>
      <td> <img src="<?php echo vtiger_imageurl('yes.gif', $theme) ?>" alt="<?php echo $mod_strings['LBL_INACTIVE']; ?>" title="<?php echo $mod_strings['LBL_INACTIVE']; ?>" width="13" height="12" align="absmiddle"> 
        [<a href=#>Deactivate</a>]</td>
    </tr>
    <tr >
      <td nowrap class="dataLabel"><?php echo $mod_strings['LBL_SUBJECT']; ?>:</td>
      <td></td>
    </tr>
    <tr >
      <td nowrap valign="top" class="dataLabel"><?php echo $mod_strings['LBL_BODY']; ?>:</td>
      <td valign="top"></td>
    </tr>
  </table>
  <TABLE WIDTH="50%" CELLPADDING="0" CELLSPACING="5" BORDER="0">
    <TD NOWRAP>&nbsp;
      <input type="submit" name="cancel" value="<?php echo $mod_strings['LBL_GOTO_LISTVIEW_BUTTON']; ?>" class="button" onclick="this.form.action.value='listemailtemplates'">
      &nbsp;</TD>
    </TR>
  </TABLE>
</form>
</body>
</html>
