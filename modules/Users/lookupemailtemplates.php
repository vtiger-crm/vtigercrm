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

require_once('include/utils/utils.php');

global $theme,$current_user;
$theme_path="themes/".$theme."/";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
<head>
  <title><?php echo $mod_strings['LBL_EMAIL_TEMPLATES_LIST']; ?></title>
  <link type="text/css" rel="stylesheet" href="<?php echo $theme_path ?>/style.css"/>
</head>
<body>
            <form action="index.php" onsubmit="VtigerJS_DialogBox.block();">
	     <div class="lvtHeaderText"><?php echo $mod_strings['LBL_EMAIL_TEMPLATES']; ?></div>
		<hr noshade="noshade" size="1">
		
             <input type="hidden" name="module" value="Users">
		<table style="background-color: rgb(204, 204, 204);" class="small" border="0" cellpadding="5" cellspacing="1" width="100%">
		<tr>
		<th width="35%" class="lvtCol"><b><?php echo $mod_strings['LBL_TEMPLATE_NAME']; ?></b></th>
                <th width="65%" class="lvtCol"><b><?php echo $mod_strings['LBL_DESCRIPTION']; ?></b></td>
                </tr>
<?php
   $sql = "select * from vtiger_emailtemplates order by templateid desc";
   $result = $adb->pquery($sql, array());
   $temprow = $adb->fetch_array($result);
   
$cnt=1;

require_once('include/utils/UserInfoUtil.php');
require('user_privileges/user_privileges_'.$current_user->id.'.php');
do
{
	$templatename = $temprow["templatename"];
	if($is_admin == false)
	{
		$folderName = $temprow['foldername'];
		if($folderName != 'Personal')
		{
			printf("<tr class='lvtColData' onmouseover=\"this.className='lvtColDataHover'\" onmouseout=\"this.className='lvtColData'\" bgcolor='white'> <td height='25'>");
			echo "<a href='javascript:submittemplate(".$temprow['templateid'].");'>".$temprow["templatename"]."</a></td>";
			printf("<td height='25'>%s</td>",$temprow["description"]);
		}
	}
	else
	{
		printf("<tr class='lvtColData' onmouseover=\"this.className='lvtColDataHover'\" onmouseout=\"this.className='lvtColData'\" bgcolor='white'> <td height='25'>");
		echo "<a href='javascript:submittemplate(".$temprow['templateid'].");'>".$temprow["templatename"]."</a></td>";
		printf("<td height='25'>%s</td>",$temprow["description"]);
	}	
        $cnt++;

}while($temprow = $adb->fetch_array($result));
?>
</table>
</body>
<script>
function submittemplate(templateid)
{
	window.document.location.href = 'index.php?module=Users&action=UsersAjax&file=TemplateMerge&templateid='+templateid;
}
</script>
</html>
