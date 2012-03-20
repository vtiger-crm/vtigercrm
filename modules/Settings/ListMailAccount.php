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
require_once('modules/Settings/Forms.php');

global $app_strings;
global $mod_strings;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

echo '<br>';
echo get_module_title($mod_strings['LBL_MODULE_NAME'], $mod_strings['LBL_MODULE_NAME'].' : '.$mod_strings['LBL_ADD_MAIL_ACCOUNT'], true);
echo '<br><br>';


if($_REQUEST['problem'])
{
  echo '<font color=red><b>Error in incoming mail server configuration! </b></font>';
}

?>

            <form action="index.php" name="massdelete" onsubmit="VtigerJS_DialogBox.block();">
             <input type="hidden" name="module" value="Settings">
             <input type="hidden" name="action" value="">
             <input type="hidden" name="idlist">
		
		<input title="<?php echo $mod_strings['LBL_NEW_MAIL_ACCOUNT_TITLE'];?>" accessKey="<?php echo $mod_strings['LBL_NEW_MAIL_ACCOUNT_KEY'];?>" class="button" onclick="this.form.action.value='AddMailAccount'" type="submit" name="button" value="  <?php echo $mod_strings['LBL_NEW_MAIL_ACCOUNT_LABEL'];?>  " >
		
		<input title="<?php echo $app_strings['LBL_DELETE_BUTTON_TITLE'];?>" accessKey="<?php echo $app_strings['LBL_DELETE_BUTTON_KEY'];?>" class="button" onclick="this.form.action.value='DeleteMailAccount'; return massDelete()" type="submit" name="button" value="  <?php echo $app_strings['LBL_DELETE_BUTTON_LABEL'];?>  " >
<br><br>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="FormBorder">
		<tbody>
		<tr><td COLSPAN="12"></td></tr>
		<tr>
		<td WIDTH="1" class="moduleListTitle" style="padding:0px 3px 0px 3px;"><input type="checkbox" name="selectall" onClick=toggleSelect(this.checked,"selected_id")></td>
		<td WIDTH="1" class="blackLine" NOWRAP><IMG SRC="<?php echo vtiger_imageurl('blank.gif', $theme) ?>"></td>
		<td width="25%"class="moduleListTitle" height="25">&nbsp;<b><?php echo $mod_strings['LBL_DISPLAY_NAME']; ?></b></td>
		<td WIDTH="1" class="blackLine" NOWRAP><IMG SRC="<?php echo vtiger_imageurl('blank.gif', $theme) ?>"></td>
                <td width="30%" class="moduleListTitle">&nbsp;<b><?php echo $mod_strings['LBL_MAIL_SERVER_NAME']; ?></b></td>
		<td WIDTH="1" class="blackLine" NOWRAP><IMG SRC="<?php echo vtiger_imageurl('blank.gif', $theme) ?>"></td>
		<td width="25%" class="moduleListTitle">&nbsp;<b><?php echo $mod_strings['LBL_EMAIL_ADDRESS']; ?></b></td>

		<td WIDTH="1" class="blackLine" NOWRAP><IMG SRC="<?php echo vtiger_imageurl('blank.gif', $theme) ?>"></td>
		<td width="25%" class="moduleListTitle">&nbsp;<b><?php echo $mod_strings['LBL_MAIL_PROTOCOL']; ?></b></td>

		<td WIDTH="1" class="blackLine" NOWRAP><IMG SRC="<?php echo vtiger_imageurl('blank.gif', $theme) ?>"></td>
		<td width="25%" class="moduleListTitle">&nbsp;<b><?php echo $mod_strings['LBL_MAIL_UNAME']; ?></b></td>
			
		<td WIDTH="1" class="blackLine" NOWRAP><IMG SRC="<?php echo vtiger_imageurl('blank.gif', $theme) ?>"></td>
		<td width="10%" class="moduleListTitle">&nbsp;<b><?php echo $mod_strings['LBL_DEFAULT']; ?></b></td>
		<td WIDTH="1" class="blackLine" NOWRAP><IMG SRC="<?php echo vtiger_imageurl('blank.gif', $theme) ?>"></td>
		<td width="10%" class="moduleListTitle">&nbsp;<b><?php echo $mod_strings['Edit']; ?></b></td>
		<td WIDTH="1" class="blackLine" NOWRAP><IMG SRC="<?php echo vtiger_imageurl('blank.gif', $theme) ?>"></td>
		</tr>
		<tr><td COLSPAN="12" class="blackLine"><IMG SRC="themes/images/blank.gif"></td></tr>
<?php
   global $current_user;
   require_once('include/utils/UserInfoUtil.php');

   $result = getMailServerInfo($current_user);
   $temprow = $adb->fetch_array($result);
   $rowcount = $adb->num_rows($result);
$edit="Edit  ";
$del="Del  ";
$bar="  | ";
$cnt=1;

if($rowcount!=0)
{
do
{

  if ($cnt%2==0)
	  printf('<tr class="evenListRow"> <td height="25">&nbsp;<input type="checkbox" name="selected_id" value='.$temprow['account_id'].'></td>');
  else
	  printf('<tr class="oddListRow"> <td height="25">&nbsp;<input type="checkbox" name="selected_id" value='.$temprow['account_id'].'></td>');
  printf('<td WIDTH="1" class="blackLine" NOWRAP><IMG SRC="themes/images/%s"></td>','blank.gif');
  printf("<td height='25'>&nbsp;%s</td>",$temprow["display_name"]);
  printf('<td WIDTH="1" class="blackLine" NOWRAP><IMG SRC="themes/images/%s"></td>','blank.gif');
  printf("<td height='25'>&nbsp;%s</td>",$temprow["mail_servername"]);
  printf('<td WIDTH="1" class="blackLine" NOWRAP><IMG SRC="themes/images/%s"></td>','blank.gif');
  printf("<td height='25'>&nbsp;%s</td>",$temprow["mail_id"]);

  printf('<td WIDTH="1" class="blackLine" NOWRAP><IMG SRC="themes/images/%s"></td>','blank.gif');
  printf("<td height='25'>&nbsp;%s</td>",$temprow["mail_protocol"]);


  printf('<td WIDTH="1" class="blackLine" NOWRAP><IMG SRC="themes/images/%s"></td>','blank.gif');
  printf("<td height='25'>&nbsp;%s</td>",$temprow["mail_username"]);
  
  printf('<td WIDTH="1" class="blackLine" NOWRAP><IMG SRC="themes/images/%s"></td>','blank.gif');
  if($temprow["set_default"]==1);
  $DEFAULT="Selected";
  printf('<td align=center><input type="radio" name="set_default" value="%s" '.$DEFAULT.'></td>',$temprow["account_id"]);
  printf('<td WIDTH="1" class="blackLine" NOWRAP><IMG SRC="themes/images/%s"></td>','blank.gif');
  printf('<td>&nbsp;<a href="index.php?module=Settings&action=AddMailAccount&record=%s">'.$mod_strings["Edit"].'</a></td>',$temprow["account_id"]);
  $cnt++;
  printf("</tr>");	
  $DEFAULT='';
}
while($temprow = $adb->fetch_array($result));
}
?>
</tbody>
</table>
<script>
function massDelete()
{
        x = document.massdelete.selected_id.length;
        idstring = "";

        if ( x == undefined)
        {

                if (document.massdelete.selected_id.checked)
                {
                        document.massdelete.idlist.value=document.massdelete.selected_id.value;
                        //alert(document.massdelete.idlist.value);
                }
                else
                {
                        alert(alert_arr.SELECT);
                        return false;
                }
        }
        else
        {
                xx = 0;
                for(i = 0; i < x ; i++)
                {
                        if(document.massdelete.selected_id[i].checked)
                        {
                                idstring = document.massdelete.selected_id[i].value +";"+idstring
                        xx++
                        }
                }
                if (xx != 0)
                {
                        document.massdelete.idlist.value=idstring;
                        //alert(document.massdelete.idlist.value);
                }
                else
                {
                        alert(alert_arr.SELECT);
                        return false;
                }
        }
        document.massdelete.action="index.php?module=Settings&action=DeleteMailAccount&return_module=Settings&return_action=ListMailAccount"
}
</script>
