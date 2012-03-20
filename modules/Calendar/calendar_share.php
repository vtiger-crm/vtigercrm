<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
global $current_user,$mod_strings,$app_strings;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('include/database/PearDatabase.php');
require_once('modules/Calendar/CalendarCommon.php');
 $t=Date("Ymd");
 $userDetails=getSharingUserName($current_user->id);
 $shareduser_ids = getSharedUserId($current_user->id);
?>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerHeadingULine">
	<tr>
		<td class="layerPopupHeading" align="left"><?php echo $mod_strings['LBL_CALSETTINGS']?></td>
		<td align=right>
			<a href="javascript:fninvsh('calSettings');"><img src="<?php echo vtiger_imageurl('close.gif', $theme) ?>" border="0"  align="absmiddle" /></a>
		</td>
	</tr>
	</table>
<form name="SharingForm" method="post" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="module" value="Calendar">
<input type="hidden" name="action" value="updateCalendarSharing">
<input type="hidden" name="view" value="<?php echo vtlib_purify($_REQUEST['view']) ?>">
<input type="hidden" name="hour" value="<?php echo vtlib_purify($_REQUEST['hour']) ?>">
<input type="hidden" name="day" value="<?php echo vtlib_purify($_REQUEST['day']) ?>">
<input type="hidden" name="month" value="<?php echo vtlib_purify($_REQUEST['month']) ?>">
<input type="hidden" name="year" value="<?php echo vtlib_purify($_REQUEST['year']) ?>">
<input type="hidden" name="viewOption" value="<?php echo vtlib_purify($_REQUEST['viewOption']) ?>">
<input type="hidden" name="subtab" value="<?php echo vtlib_purify($_REQUEST['subtab']) ?>">
<input type="hidden" name="parenttab" value="<?php echo vtlib_purify($_REQUEST['parenttab']) ?>">
<input type="hidden" name="current_userid" value="<?php echo $current_user->id ?>" >
<input type="hidden" name="shar_userid" id="shar_userid" >

<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
	<tr>
		<td class=small >
			<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
			<tr>
		<td align="right" width="10%" valign="top"><img src="<?php echo vtiger_imageurl('cal_clock.jpg', $theme)  ?>" align="absmiddle"></td>
		<td align="left" width="90%">
			<b><?php echo $mod_strings['LBL_TIMESETTINGS']?></b><br>
			<input type="checkbox" name="sttime_check" <?php if($current_user->start_hour != ''){?> checked <?php } ?> onClick="enableCalstarttime();">&nbsp;<?php echo $mod_strings['LBL_CALSTART']?> 
			<select name="start_hour" <?php if($current_user->start_hour == ''){?>disabled <?php } ?> >
				<?php
					for($i=0;$i<=23;$i++)
					{
						if($i == 0)
							$hour = "12:00 am";
						elseif($i >= 12)
						{
							if($i == 12)
								$hour = $i;
							else 
								$hour = $i - 12;
							$hour = $hour.":00 pm";
						}
						else
               					{
							$hour = $i.":00 am";
       						}
						if($i <= 9 && strlen(trim($i)) < 2)
						{
							$value = '0'.$i.':00';
                       				}
						else
							$value = $i.':00';
							if($value === $current_user->start_hour)
                                                       	        $selected = 'selected';
                                                         else
                                                                $selected = '';
				?>
				<option <?php echo $selected?> value="<?php echo $value?>"><?php echo $hour?></option>
				<?php
					}
				?>
			</select><br>
			<input type="checkbox" name="hour_format" <?php if($current_user->hour_format == '24'){?> checked <?php } ?> value="24">&nbsp;<?php echo $mod_strings['LBL_USE24']?>
		</td>
	</tr>
	<tr><td colspan="2" style="border-bottom:1px dotted #CCCCCC;"></td></tr>
	<tr>
		<td align="right" valign="top"><img src="<?php echo vtiger_imageurl('cal_sharing.jpg', $theme) ?>" width="45" height="38" align="absmiddle"></td>
		<td align="left">
		<b><?php echo $mod_strings['LBL_CALSHARE']?></b><br>
		<?php echo $mod_strings['LBL_CALSHAREMESSAGE']?><br><br>
		<!-- Calendar sharing UI-->
			<DIV id="cal_shar" style="display:block;width:100%;height:200px">
                                <table border=0 cellspacing=0 cellpadding=2 width=100% bgcolor="#FFFFFF">
                                <tr>
                                        <td valign=top>
                                                <table border=0 cellspacing=0 cellpadding=2 width=100%>
                                                <tr>
                                                        <td colspan=3>
                                                                <ul style="padding-left:20px">
                                                                <li><?php echo $mod_strings['LBL_INVITE_SHARE']?>
                                                                <li><?php echo $mod_strings['LBL_INVITE_INST2']?>
                                                                </ul>
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <td><b><?php echo $mod_strings['LBL_AVL_USERS']?></b></td>
                                                        <td>&nbsp;</td>
                                                        <td><b><?php echo $mod_strings['LBL_SEL_USERS']?></b></td>
                                                </tr>
                                                <tr>
                                                        <td width=40% align=center valign=top>
                                                        <select name="available_users" id="available_users" class=small size=5
 multiple style="height:70px;width:100%">
                                                        <?php
                                                                foreach($userDetails as $id=>$name)
                                                                {
                                                                        if($id != '')
                                                                        echo "<option value=".$id.">".$name."</option>";
                                                                 }
                                                        ?>
                                                                </select>

                                                        </td>
                                                        <td width=20% align=center valign=top>
                                                                <input type=button value="<?php echo $mod_strings['LBL_ADD_BUTTON'] ?> >>" class="crm button small save" style="width:100%" onClick="incUser('available_users','selected_users')"><br>
                                                                <input type=button value="<< <?php echo $mod_strings['LBL_RMV_BUTTON'] ?> " class="crm button small cancel" style="width:100%" onClick="rmvUser('selected_users')">
							</td>
							<td>
							<select name="selected_users" id="selected_users" class=small size=5 multiple style="height:70px;width:100%">
							<?php
                                                                foreach($shareduser_ids as $shar_id=>$share_user)
                                                                {
                                                                        if($shar_id != '')
                                                                        echo "<option value=".$shar_id.">".$share_user."</option>";
                                                                }
                                                        ?>
                                                                </select>
	

                                                        </select>
							<td>
                                                </tr>
                                                </table>


                                        </td>
                                </tr>
                                </table>

		</div>
		</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
	<tr>
		<td align="center">
			<input type="submit" name="save" value=" &nbsp;<?php echo $app_strings['LBL_SAVE_BUTTON_LABEL'] ?>&nbsp;" class="crmbutton small save" onClick = "userEventSharing('shar_userid','selected_users');"/>&nbsp;&nbsp;
			<input type="button" name="cancel" value=" <?php echo $app_strings['LBL_CANCEL_BUTTON_LABEL'] ?> " class="crmbutton small cancel" onclick="fninvsh('calSettings');" />
		</td>
	</tr>
	</table>
</form>

