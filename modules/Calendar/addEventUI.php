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

require_once('include/utils/CommonUtils.php');
require_once('include/CustomFieldUtil.php');
require_once('modules/Calendar/Activity.php');
require_once('modules/Calendar/Calendar.php');
require_once('modules/Calendar/CalendarCommon.php');
require_once("modules/Emails/mail.php");

 global $theme,$mod_strings,$app_strings,$current_user;
 $theme_path="themes/".$theme."/";
 $image_path=$theme_path."images/";
 $category = getParentTab();
 $userDetails=getOtherUserName($current_user->id,true);
 $to_email = getUserEmailId('id',$current_user->id);
 $date_format = parse_calendardate($app_strings['NTC_DATE_FORMAT']);
 $taskassignedto = getAssignedTo(9);
 $eventassignedto = getAssignedTo(16);
$mysel= vtlib_purify($_REQUEST['view']);
$calendar_arr = Array();
$calendar_arr['IMAGE_PATH'] = $image_path;
if(empty($mysel)){
	if($current_user->activity_view == "This Year"){
		$mysel = 'year';
	}else if($current_user->activity_view == "This Month"){
		$mysel = 'month';
	}else if($current_user->activity_view == "This Week"){
		$mysel = 'week';
	}else{
		$mysel = 'day';
	}
}
$date_data = array();
if ( isset($_REQUEST['day']))
{

        $date_data['day'] = $_REQUEST['day'];
}

if ( isset($_REQUEST['month']))
{
        $date_data['month'] = $_REQUEST['month'];
}

if ( isset($_REQUEST['week']))
{
        $date_data['week'] = $_REQUEST['week'];
}

if ( isset($_REQUEST['year']))
{
        if ($_REQUEST['year'] > 2037 || $_REQUEST['year'] < 1970)
        {
		print("<font color='red'>".$app_strings['LBL_CAL_LIMIT_MSG']."</font>");
                exit;
        }
        $date_data['year'] = $_REQUEST['year'];
}


if(empty($date_data))
{
	$data_value=date('Y-m-d H:i:s');
        preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/',$data_value,$value);
        $date_data = Array(
                'day'=>$value[3],
                'month'=>$value[2],
                'year'=>$value[1],
                'hour'=>$value[4],
                'min'=>$value[5],
        );

}
$calendar_arr['calendar'] = new Calendar($mysel,$date_data);
$calendar_arr['view'] = $mysel;
if($current_user->hour_format == '')
	$calendar_arr['calendar']->hour_format = 'am/pm';
else
	$calendar_arr['calendar']->hour_format = $current_user->hour_format;
 
/** Function to construct HTML code for Assigned To field
 *  @param $assignedto  -- Assigned To values :: Type array
 *  @param $toggletype  -- String to different event and task  :: Type string
 *  return $htmlStr     -- HTML code in string forat  :: Type string
 */
function getAssignedToHTML($assignedto,$toggletype)
{
	global $app_strings;
	$userlist = $assignedto[0];
	if(isset($assignedto[1]) && $assignedto[1] != null)
		$grouplist = $assignedto[1];
	$htmlStr = '';
	$check = 1;
	foreach($userlist as $key_one=>$arr)
	{
		foreach($arr as $sel_value=>$value)
		{
			if($value != '')
				$check=$check*0;
			else
				$check=$check*1;
		}
	}
	if($check == 0)
	{
		$select_user='checked';
		$style_user='display:block';
		$style_group='display:none';
	}
	else
	{
		$select_group='checked';
		$style_user='display:none';
		$style_group='display:block';
	}
	if($toggletype == 'task')
		$htmlStr .= '<input type="radio" name="task_assigntype" '.$select_user.' value="U" onclick="toggleTaskAssignType(this.value)">&nbsp;'.$app_strings['LBL_USER'];
	else
		$htmlStr .= '<input type="radio" name="assigntype" '.$select_user.' value="U" onclick="toggleAssignType(this.value)">&nbsp;'.$app_strings['LBL_USER'];
	if($grouplist != '')
	{
		if($toggletype == 'task')
			$htmlStr .= '<input type="radio" name="task_assigntype" '.$select_group.' value="T" onclick="toggleTaskAssignType(this.value)">&nbsp;'.$app_strings['LBL_GROUP'];
		else
			$htmlStr .= '<input type="radio" name="assigntype" '.$select_group.' value="T" onclick="toggleAssignType(this.value)">&nbsp;'.$app_strings['LBL_GROUP'];
	}
	if($toggletype == 'task')
	{
		$htmlStr .= '<span id="task_assign_user" style="'.$style_user.'">
				<select name="task_assigned_user_id" class=small>';
	}
	else
	{
		$htmlStr .= '<span id="assign_user" style="'.$style_user.'">
				<select name="assigned_user_id" class=small>';
	}
	$htmlStr .= getUserslist();
	$htmlStr .= '</select>
			</span>';
	if($grouplist != '')
	{
		if($toggletype == 'task')
		{
			$htmlStr .= '<span id="task_assign_team" style="'.$style_group.'">
					<select name="task_assigned_group_id" class=small>';
		}
		else
		{
			$htmlStr .= '<span id="assign_team" style="'.$style_group.'">
					<select name="assigned_group_id" class=small>';
		}
		$htmlStr .= getGroupslist();
		$htmlStr .= '</select>
				</span>';
	}
	return $htmlStr;
}

?>
       
	<!-- Add Event DIV starts-->
	<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
	<script type="text/javascript" src="jscalendar/calendar.js"></script>
	<script type="text/javascript" src="jscalendar/lang/calendar-<?php echo $app_strings['LBL_JSCALENDAR_LANG'] ?>.js"></script>
	<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
	<script type="text/javascript" src="include/js/ListView.js"></script>
	<div class="calAddEvent layerPopup" style="display:none;width:550px;left:200px;" id="addEvent" align=center>
	<form name="EditView" onSubmit="if(check_form()) { VtigerJS_DialogBox.block(); } else { return false; }" method="POST" action="index.php">
	<input type="hidden" name="return_action" value="index">
	<input type="hidden" name="return_module" value="Calendar">
	<input type="hidden" name="module" value="Calendar">
	<input type="hidden" name="activity_mode" value="Events">
	<input type="hidden" name="action" value="Save">
	<input type="hidden" name="view" value="<?php echo $calendar_arr['view'] ?>">
	<input type="hidden" name="hour" value="<?php echo $calendar_arr['calendar']->date_time->hour ?>">
	<input type="hidden" name="day" value="<?php echo $calendar_arr['calendar']->date_time->day ?>">
	<input type="hidden" name="month" value="<?php echo $calendar_arr['calendar']->date_time->month ?>">
	<input type="hidden" name="year" value="<?php echo $calendar_arr['calendar']->date_time->year ?>">
	<input type="hidden" name="record" value="">
	<input type="hidden" name="mode" value="">
	<input type="hidden" name="time_start" id="time_start">
	<input type="hidden" name="time_end" id="time_end">
	<input type="hidden" name="followup_due_date" id="followup_due_date">
	<input type="hidden" name="followup_time_start" id="followup_time_start">
	<input type="hidden" name="followup_time_end" id="followup_time_end">
	<input type="hidden" name="duration_hours" value="0">                                                                      <input type="hidden" name="duration_minutes" value="0">
	<input type=hidden name="inviteesid" id="inviteesid" value="">
	<input type="hidden" name="parenttab" value="<?php echo $category ?>">
	<input type="hidden" name="viewOption" value="">
	<input type="hidden" name="subtab" value="">
	<input type="hidden" name="maintab" value="Calendar">
	<input type="hidden" name="dateformat" value="<?php echo $date_format ?>">
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerHeadingULine">
		<tr style="cursor:move;">
			<td class="layerPopupHeading" align = "left" id="moveEvent"><?php echo $mod_strings['LBL_ADD_EVENT']?></b></td>
				<td align=right><a href="javascript:ghide('addEvent');"><img src="<?php echo  vtiger_imageurl('close.gif', $theme)  ?>" border="0"  align="absmiddle" /></a></td>
		</tr>
		</table>
		
		<table border=0 cellspacing=0 cellpadding=5 width=95% align=center bgcolor="#FFFFFF"> 
			<tr>
		<td class=small >
			<table border=0 cellspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
			<tr>
			<td nowrap  width=20% align="right"><b><?php echo $mod_strings['LBL_EVENTTYPE']?></b></td>
			<td width=80% align="left">
				<table>
					<tr><td>
							<?php echo getActFieldCombo('activitytype','vtiger_activitytype'); ?>
					</td></tr>
				</table>
			</td>
			</tr>
			<tr>
				<td nowrap align="right"><b><font color="red">*</font><?php echo $mod_strings['LBL_EVENTNAME']?></b></td>
				<td align="left"><input name="subject" type="text" class="textbox" value="" style="width:50%">&nbsp;&nbsp;&nbsp; 
			<?php if(getFieldVisibilityPermission('Events',$current_user->id,'visibility') == '0') { ?>	
			<input name="visibility" value="Public" type="checkbox"><?php echo $mod_strings['LBL_PUBLIC']; ?>
			<?php } ?>	
			</td>
			</tr>
			<?php if(getFieldVisibilityPermission('Events',$current_user->id,'description') == '0') { ?>
			<tr>
				<td valign="top" align="right"><b><?php echo $mod_strings['Description']?></b></td>
				<td align="left"><textarea style = "width:100%; height : 60px;" name="description"></textarea></td>
			</tr>
			<?php } ?>
			<?php if(getFieldVisibilityPermission('Events',$current_user->id,'location') == '0') { ?>
			<tr>
				<td nowrap align="right"><b><?php echo $mod_strings['Location']?></b></td>
				<td align="left"><input name="location" type="text" class="textbox" value="" style="width:50%"></td>
			</tr>
		        <?php } ?>
			<tr>
				<td colspan=2 width=80% align="center">
					<table border=0 cellspacing=0 cellpadding=3 width=80%>
					<tr>
						<?php if(getFieldVisibilityPermission('Events',$current_user->id,'eventstatus') == '0')	{ ?>
						<td ><b><font color="red">*</font><?php echo $mod_strings['Status'] ; ?></b></td>
						<?php } ?>
						<?php if(getFieldVisibilityPermission('Events',$current_user->id,'assigned_user_id') == '0') { ?>
						<td ><b><?php echo $mod_strings['Assigned To']; ?></b></td>
						<?php } ?>
					</tr>
					<tr>
						<?php if(getFieldVisibilityPermission('Events',$current_user->id,'eventstatus') == '0') { ?>
						<td valign=top><?php echo getActFieldCombo('eventstatus','vtiger_eventstatus'); ?></td>
						<?php } ?>	
						<td valign=top rowspan=2>
							<?php if(getFieldVisibilityPermission('Events',$current_user->id,'assigned_user_id') == '0') { ?>
							<?php echo getAssignedToHTML($eventassignedto,'event'); ?>
							<br><?php }else{
								?><input name="assigned_user_id" value="<?php echo $current_user->id ?>" type="hidden">
							<?php } ?>

								<?php if(getFieldVisibilityPermission('Events',$current_user->id,'sendnotification') == '0') { ?>
							<input type="checkbox" name="sendnotification" >&nbsp;<?php echo $mod_strings['LBL_SENDNOTIFICATION'] ?>
							<?php } ?>
						</td>
					</tr>
					<?php if(getFieldVisibilityPermission('Events',$current_user->id,'taskpriority') == '0') { ?>
					<tr>
						<td valign=top><b><?php echo $mod_strings['Priority'] ; ?></b>
							<br><?php echo getActFieldCombo('taskpriority','vtiger_taskpriority'); ?>
						</td>
					</tr>
				        <?php } ?>
					</table>
				</td>
			</tr>		
			</table>
			<hr noshade size=1>
			<table id="date_table" border=0 cellspacing=0 cellpadding=5 width=100% align=center bgcolor="#FFFFFF" align=center>
			<tr>
			<td >
				<table border=0 cellspacing=0 cellpadding=2 width=100% align=center>
				<tr>
				<td width=50% id="date_table_firsttd" valign=top style="border-right:1px solid #dddddd">
					<table border=0 cellspacing=0 cellpadding=2 width=100% align=center>
					<tr><td colspan=3 align="left"><b><?php echo $mod_strings['LBL_EVENTSTAT']?></b></td></tr>
				        <tr><td colspan=3 align="left">
						<?php echo  getTimeCombo($calendar_arr['calendar']->hour_format,'start');?>
					</td></tr>
                                        <tr><td align="left">
					<input type="text" name="date_start" id="jscal_field_date_start" class="textbox" style="width:90px" onChange="dochange('jscal_field_date_start','jscal_field_due_date');" value="<?php echo getDisplayDate($calendar_arr['calendar']->date_time->get_formatted_date()) ?>"></td><td width=100% align="left"><img border=0 src="<?php echo $image_path?>btnL3Calendar.gif" alt="<?php echo $mod_strings['LBL_SET_DATE']?>" title="<?php echo $mod_strings['LBL_SET_DATE']?>" id="jscal_trigger_date_start">
						<script type="text/javascript">
                					Calendar.setup ({
								inputField : "jscal_field_date_start", ifFormat : "<?php  echo $date_format; ?>", showsTime : false, button : "jscal_trigger_date_start", singleClick : true, step : 1
									})
     						        </script>
					</td></tr>
					</table>
				</td>
				<td width=50% valign=top id="date_table_secondtd">
					<table border=0 cellspacing=0 cellpadding=2 width=100% align=center>
					<tr><td colspan=3 align="left"><b><?php echo $mod_strings['LBL_EVENTEDAT']?></b></td></tr>
				        <tr><td colspan=3 align="left">
                                                <?php echo getTimeCombo($calendar_arr['calendar']->hour_format,'end');?>
					</td></tr>
				        <tr><td align="left">
					<input type="text" name="due_date" id="jscal_field_due_date" class="textbox" style="width:90px" value="<?php echo getDisplayDate($calendar_arr['calendar']->date_time->get_formatted_date()) ?>"></td><td width=100% align="left"><img border=0 src="<?php echo $image_path?>btnL3Calendar.gif" alt="<?php echo $mod_strings['LBL_SET_DATE']?>" title="<?php echo $mod_strings['LBL_SET_DATE']?>" id="jscal_trigger_due_date">
					<script type="text/javascript">
                                                        Calendar.setup ({
                                                                inputField : "jscal_field_due_date", ifFormat : "<?php echo $date_format; ?>", showsTime : false, button : "jscal_trigger_due_date", singleClick : true, step : 1
                                                                        })
                                                        </script>
					</td></tr>
					</table>
				</td>
				<td width=34% valign=top style="display:none;border-left:1px solid #dddddd" id="date_table_thirdtd">
					<table border=0 cellspacing=0 cellpadding=2 width=100% align=center>
					<tr><td colspan=3 align="left"><b><input type="checkbox" name="followup"><?php echo $mod_strings['LBL_HOLDFOLLOWUP']?></b></td></tr>
					<tr><td colspan=3 align="left">
					<?php echo getTimeCombo($calendar_arr['calendar']->hour_format,'followup_start');?>
					</td></tr>
					<tr><td align="left">
						<input type="text" name="followup_date" id="jscal_field_followup_date" class="textbox" style="width:90px" value="<?php echo getDisplayDate($calendar_arr['calendar']->date_time->get_formatted_date()) ?>"></td><td width=100% align="left"><img border=0 src="<?php echo $image_path?>btnL3Calendar.gif" alt="<?php echo $mod_strings['LBL_SET_DATE']?>" title="<?php echo $mod_strings['LBL_SET_DATE']?>" id="jscal_trigger_followup_date">
						<script type="text/javascript">
						Calendar.setup ({
							inputField : "jscal_field_followup_date", ifFormat : "<?php echo $date_format; ?>", showsTime : false, button : "jscal_trigger_followup_date", singleClick : true, step : 1
						})
						</script>
					</td></tr>
					</table>
				</td>
				</tr>
				</table></td>
			</tr>
			</table>
			<?php  
				$custom_fields_data = getCalendarCustomFields(getTabid('Events'),'edit');
				$smarty=new vtigerCRM_Smarty;
				$smarty->assign("MODULE",'Calendar');
				$smarty->assign("MOD",$mod_strings);
				$smarty->assign("APP",$app_strings);
				$theme_path="themes/".$theme."/";
				$image_path=$theme_path."images/";
				$smarty->assign("IMAGE_PATH", $image_path);
				if (count($custom_fields_data) > 0){ ?>
					<hr noshade size=1>
					<table>
					<tr>
						<td colspan="2">
							<b><?php echo $app_strings['LBL_CUSTOM_INFORMATION']?></b>
						</td>
					</tr>
					<tr>
						<?php 
							echo "<tr>";
							for($i=0; $i<count($custom_fields_data); $i++) {
								$maindata = $custom_fields_data[$i];
								$smarty->assign("maindata",$maindata);
								$smarty->assign("THEME", $theme);
								$smarty->display('EditViewUI.tpl');
								if (($i+1)%2 == 0) {
									echo "</tr><tr>";
								}
							}							
							if ($i% 2 != 0) {
								echo '<td width="20%"></td><td width="30%"></td>';
							}
							echo "</tr>";
						?>
					</tr>
					</table>
				<?php } ?>

			<!-- Alarm, Repeat, Invite starts-->
			<br>
			<table border=0 cellspacing=0 cellpadding=0 width=100% align=center bgcolor="#FFFFFF">
			<tr>
			<td>
				<table border=0 cellspacing=0 cellpadding=3 width=100%>
				<tr>
					<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
					<td id="cellTabInvite" class="dvtSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','on');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','off');ghide('addEventAlarmUI');gshow('addEventInviteUI','',document.EditView.date_start.value,document.EditView.due_date.value,document.EditView.starthr.value,document.EditView.startmin.value,document.EditView.startfmt.value,document.EditView.endhr.value,document.EditView.endmin.value,document.EditView.endfmt.value);ghide('addEventRepeatUI');ghide('addEventRelatedtoUI');"><?php echo $mod_strings['LBL_INVITE']?></a></td>
					<td class="dvtTabCache" style="width:10px">&nbsp;</td>
					<?php if(getFieldVisibilityPermission('Events',$current_user->id,'reminder_time') == '0') { ?>
					<td id="cellTabAlarm" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','on');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','off');gshow('addEventAlarmUI','',document.EditView.date_start.value,document.EditView.due_date.value,document.EditView.starthr.value,document.EditView.startmin.value,document.EditView.startfmt.value,document.EditView.endhr.value,document.EditView.endmin.value,document.EditView.endfmt.value);ghide('addEventInviteUI');ghide('addEventRepeatUI');ghide('addEventRelatedtoUI');"><?php echo $mod_strings['LBL_REMINDER']?></a></td>
					<td class="dvtTabCache" style="width:10px">&nbsp;</td>
					<?php } if(getFieldVisibilityPermission('Events',$current_user->id,'recurringtype') == '0') {  ?>
					<td id="cellTabRepeat" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','on');switchClass('cellTabRelatedto','off');ghide('addEventAlarmUI');ghide('addEventInviteUI');gshow('addEventRepeatUI','',document.EditView.date_start.value,document.EditView.due_date.value,document.EditView.starthr.value,document.EditView.startmin.value,document.EditView.startfmt.value,document.EditView.endhr.value,document.EditView.endmin.value,document.EditView.endfmt.value);ghide('addEventRelatedtoUI');"><?php echo $mod_strings['LBL_REPEAT']?></a></td>
					<?php } ?>
					<td class="dvtTabCache" style="width:10px">&nbsp;</td>
					<td id="cellTabRelatedto" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','on');ghide('addEventAlarmUI');ghide('addEventInviteUI');gshow('addEventRelatedtoUI','',document.EditView.date_start.value,document.EditView.due_date.value,document.EditView.starthr.value,document.EditView.startmin.value,document.EditView.startfmt.value,document.EditView.endhr.value,document.EditView.endmin.value,document.EditView.endfmt.value);ghide('addEventRepeatUI');"><?php echo $mod_strings['LBL_RELATEDTO']?></a></td>
					<td class="dvtTabCache" style="width:100%">&nbsp;</td>
				</tr>
				</table>
			</td>
			</tr>
			<tr>
			<td width=100% valign=top align=left class="dvtContentSpace" style="padding:10px;height:120px">
			<!-- Invite UI -->
				
				<DIV id="addEventInviteUI" style="display:block;width:100%">
				<table border=0 cellspacing=0 cellpadding=2 width=100% bgcolor="#FFFFFF">
				<tr>
					<td valign=top> 
						<table border=0 cellspacing=0 cellpadding=2 width=100%>
						<tr>
							<td colspan=3>
								<ul style="padding-left:20px">
								<li><?php echo $mod_strings['LBL_INVITE_INST1']?> 
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
							<select name="availableusers" id="availableusers" class=small size=5 multiple style="height:70px;width:100%">
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
								<input type=button value="<?php echo $mod_strings['LBL_ADD_BUTTON'] ?> >>" class="crm button small save" style="width:100%" onClick="incUser('availableusers','selectedusers')"><br>
								<input type=button value="<< <?php echo $mod_strings['LBL_RMV_BUTTON'] ?> " class="crm button small cancel" style="width:100%" onClick="rmvUser('selectedusers')">
							</td>
							<td width=40% align=center valign=top>
								<select name="selectedusers" id="selectedusers" class=small size=5 multiple style="height:70px;width:100%">
								</select>
								<div align=left><?php echo $mod_strings['LBL_SELUSR_INFO']?>
								</div>
							
							</td>
						</tr>
						</table>
							
					
					</td>
				</tr>
				</table>
				</DIV>
			
			<!-- Reminder UI -->
				<DIV id="addEventAlarmUI" style="display:none;width:100%">
				<?php if(getFieldVisibilityPermission('Events',$current_user->id,'reminder_time') == '0') { ?>
				<table bgcolor="#FFFFFF">
					<tr><td><?php echo $mod_strings['LBL_SENDREMINDER']?></td>
						<td>
					<input type="radio" name="set_reminder"value="Yes" onClick="showBlock('reminderOptions')">&nbsp;<?php echo $mod_strings['LBL_YES'] ?>&nbsp;
					<input type="radio" name="set_reminder" value="No" onClick="fnhide('reminderOptions')">&nbsp;<?php echo $mod_strings['LBL_NO'] ?>&nbsp;
							
					</td></tr>
				</table>
				<DIV id="reminderOptions" style="display:none;width:100%">
				<table border=0 cellspacing=0 cellpadding=2  width=100% bgcolor="#FFFFFF">
				<tr>
					<td nowrap align=right width=20% valign=top>
						<b><?php echo $mod_strings['LBL_RMD_ON']?> : </b>
					</td>
					<td width=80%>
						<table border=0>
						<tr>
						<td colspan=2>
							<select class=small name="remdays">
							<?php
								for($m=0;$m<=31;$m++)
								{
							?>
									<option value="<?php echo $m ?>"><?php echo $m ?></option>
							<?php
								}
							?>
							</select><?php echo $mod_strings['LBL_REMAINDER_DAY']; ?> 
							<select class=small name="remhrs">
                                                        <?php
                                                                for($h=0;$h<=23;$h++)
                                                                {
                                                        ?>
                                                                        <option value="<?php echo $h ?>"><?php echo $h ?></option>
                                                        <?php
                                                                }
                                                        ?>
							</select><?php echo $mod_strings['LBL_REMAINDER_HRS']; ?>
							<select class=small name="remmin">
                                                        <?php
                                                                for($min=1;$min<=59;$min++)
                                                                {
                                                        ?>
                                                                        <option value="<?php echo $min ?>"><?php echo $min ?></option>
                                                        <?php
                                                                }
                                                        ?>
							</select><?php echo $mod_strings['LBL_MINUTES']; ?>&nbsp;<?php echo $mod_strings['LBL_BEFOREEVENT'] ?>
						</td>
						</tr>
						</table>
					</td>
				</tr>
				<!-- This is now required as of now, as we aree not allowing to change the email id
                                        and it is showing logged in User's email id, instead of Assigned to user's email id -->
				<!--<tr>
					<td nowrap align=right>
					<?php echo $mod_strings['LBL_SDRMD'] ?> :
					</td>
					<td >
					<input type=text name="toemail" readonly="readonly" class=textbox style="width:90%" value="<?php echo $to_email ?>">
					</td>
				</tr>-->
				</table>
				<?php } ?>
				</DIV>
				</DIV>
			<!-- Repeat UI -->
				<div id="addEventRepeatUI" style="display:none;width:100%">
			<?php if(getFieldVisibilityPermission('Events',$current_user->id,'recurringtype') == '0') {  ?>
				<table border=0 cellspacing=0 cellpadding=2  width=100% bgcolor="#FFFFFF">
				<tr>
					<td nowrap align=right width=20% valign=top>
					<strong><?php echo $mod_strings['LBL_REPEAT']?> :</strong>
					</td>
					<td nowrap width=80% valign=top>
						<table border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td width=20><input type="checkbox" name="recurringcheck" onClick="showhide('repeatOptions')"></td>
							<td><?php echo $mod_strings['LBL_ENABLE_REPEAT']?></td>
						</tr>
						<tr>
							<td colspan=2>
							<div id="repeatOptions" style="display:none">
								<table border=0 cellspacing=0 cellpadding=2 bgcolor="#FFFFFF">
								<tr>
								<td>
									<?php echo $mod_strings['LBL_REPEATEVENT']; ?>
								</td>
								<td><select name="repeat_frequency" class="small">
								<?php for($i=1;$i<=14;$i++) { ?>
									<option value="<?php echo $i ?>"><?php echo $i ?></option>	
								<?php } ?>	
								</select></td>
								<td>
									<select name="recurringtype" onChange="rptoptDisp(this)" class="small">
										<option value="Daily"><?php echo $mod_strings['LBL_DAYS']; ?></option>
										<option value="Weekly"><?php echo $mod_strings['LBL_WEEKS']; ?></option>
										<option value="Monthly"><?php echo $mod_strings['LBL_MONTHS']; ?></option>
										<option value="Yearly"><?php echo $mod_strings['LBL_YEAR']; ?></option>
									</select>
									<!-- Limit for Repeating Event -->
									<b><?php echo $mod_strings['LBL_UNTIL']; ?>:</b> <input type="text" name="calendar_repeat_limit_date" id="calendar_repeat_limit_date" class="textbox" style="width:90px" value="<?php echo getDisplayDate($calendar_arr['calendar']->date_time->get_formatted_date()) ?>" ></td><td align="left"><img border=0 src="<?php echo $image_path ?>btnL3Calendar.gif" alt="<?php echo $mod_strings['LBL_SET_DATE']?>" title="<?php echo $mod_strings['LBL_SET_DATE']?>" id="jscal_trigger_calendar_repeat_limit_date">
									<script type="text/javascript">
									Calendar.setup ({
										inputField : "calendar_repeat_limit_date", ifFormat : "<?php  echo $date_format; ?>", showsTime : false, button : "jscal_trigger_calendar_repeat_limit_date", singleClick : true, step : 1
									})
									</script>
									<!-- END -->	
								</td>
								</tr>
								</table>

								<div id="repeatWeekUI" style="display:none;">
								<table border=0 cellspacing=0 cellpadding=2>
									<tr>
								<td><input name="sun_flag" value="sunday" type="checkbox"></td><td><?php echo $mod_strings['LBL_SM_SUN']; ?></td>
								<td><input name="mon_flag" value="monday" type="checkbox"></td><td><?php echo $mod_strings['LBL_SM_MON']; ?></td>
								<td><input name="tue_flag" value="tuesday" type="checkbox"></td><td><?php echo $mod_strings['LBL_SM_TUE']; ?></td>
								<td><input name="wed_flag" value="wednesday" type="checkbox"></td><td><?php echo $mod_strings['LBL_SM_WED']; ?></td>
								<td><input name="thu_flag" value="thursday" type="checkbox"></td><td><?php echo $mod_strings['LBL_SM_THU']; ?></td>
								<td><input name="fri_flag" value="friday" type="checkbox"></td><td><?php echo $mod_strings['LBL_SM_FRI']; ?></td>
								<td><input name="sat_flag" value="saturday" type="checkbox"></td><td><?php echo $mod_strings['LBL_SM_SAT']; ?></td>
									</tr>
								</table>
								</div>

								<div id="repeatMonthUI" style="display:none;">
								<table border=0 cellspacing=0 cellpadding=2 bgcolor="#FFFFFF">
									<tr>
										<td>
											<table border=0 cellspacing=0 cellpadding=2>
												<tr>
													<td><input type="radio" checked name="repeatMonth" value="date"></td><td><?php echo $mod_strings['on'];?></td><td><input type="text" class=textbox style="width:20px" value="2" name="repeatMonth_date" ></td><td><?php echo $mod_strings['day of the month'];?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td>
											<table border=0 cellspacing=0 cellpadding=2>
												<tr>
													<td>
														<input type="radio" name="repeatMonth" value="day"></td>
													<td><?php echo $mod_strings['on'];?></td>
													<td>
														<select name="repeatMonth_daytype">
															<option value="first"><?php echo $mod_strings['First'];?></option>
															<option value="last"><?php echo $mod_strings['Last'];?></option>
														</select>
													</td>
													<td>
														<select name="repeatMonth_day">
															<option value=1><?php echo $mod_strings['LBL_DAY1']; ?></option>
															<option value=2><?php echo $mod_strings['LBL_DAY2']; ?></option>
															<option value=3><?php echo $mod_strings['LBL_DAY3']; ?></option>
															<option value=4><?php echo $mod_strings['LBL_DAY4']; ?></option>
															<option value=5><?php echo $mod_strings['LBL_DAY5']; ?></option>
															<option value=6><?php echo $mod_strings['LBL_DAY6']; ?></option>
														</select>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
								</div>
								
							</div>
								
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
				<?php } ?>
				</div>
				<div id="addEventRelatedtoUI" style="display:none;width:100%">
					<table width="100%" cellpadding="5" cellspacing="0" border="0" bgcolor="#FFFFFF">
				<?php if(getFieldVisibilityPermission('Events',$current_user->id,'parent_id') == '0') {  ?>
						<tr>
							<td width="15%"><b><?php echo $mod_strings['LBL_RELATEDTO']?></b></td>
							<td>
								<input name="parent_id" value="" type="hidden">
								<input name="del_actparent_rel" type="hidden" >
								<select name="parent_type" class="small" id="parent_type" onChange="document.EditView.parent_name.value='';document.EditView.parent_id.value=''">
									<option value="Leads"><?php echo $app_strings['Leads']?></option>
									<option value="Accounts"><?php echo $app_strings['Accounts']?></option>
									<option value="Potentials"><?php echo $app_strings['Potentials']?></option>
									<option value="HelpDesk"><?php echo $app_strings['HelpDesk']?></option>
								</select>
							</td>
							<td>
								<div id="eventrelatedto" align="left">
								<input type="text" readonly="readonly" class="calTxt small" value="" name="parent_name">&nbsp;
							<input type="button" name="selectparent" class="crmButton small edit" value="<?php echo $mod_strings['LBL_SELECT']; ?>" onclick="return window.open('index.php?module='+document.EditView.parent_type.value+'&action=Popup','test','width=640,height=602,resizable=0,scrollbars=0,top=150,left=200');">
							<input type='button' value='<?php echo $app_strings['LNK_DELETE']; ?>' class="crmButton small edit" onclick="document.EditView.del_actparent_rel.value=document.EditView.parent_id.value;document.EditView.parent_id.value='';document.EditView.parent_name.value='';">
								</div>
							</td>
						</tr>
					<?php } ?>
						<tr>
						<td><b><?php echo $app_strings['Contacts'] ?></b></td>
							<td colspan="2">
								<input name="contactidlist" id="contactidlist" value="" type="hidden">
								<input name="deletecntlist" id="deletecntlist" type="hidden">
								<select name="contactlist" size="5" style="height: 85px;width:150px;"  id="parentid" class="small" multiple>
								</select>
								<input type="button" onclick="selectContact('true','general',document.EditView);" class="crmButton small edit" name="selectcnt" value="<?php echo $mod_strings['LBL_SELECT_CONTACT'] ; ?>">
								<input type='button' value='<?php echo $app_strings['LNK_DELETE']; ?>' class="crmButton small edit" onclick='removeActContacts();'>
								
							</td>
						</tr>
					</table>
				</div>
			</td>
			</tr>
			</table>
			<!-- popup specific content fill in ends -->
		</td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
		<br>
		
		
		<tr>
			<td valign=top></td>
			<td  align=center>
				<input alt="<?php echo $app_strings['LBL_SAVE_BUTTON_TITLE']; ?>" title="<?php echo $app_strings['LBL_SAVE_BUTTON_TITLE']; ?>" accessKey='S' type="submit" name="eventsave" class="crm button small save" style="width:90px" value="<?php echo $mod_strings['LBL_SAVE']?>">
	<input alt="<?php echo $app_strings['LBL_CANCEL_BUTTON_TITLE']; ?>" title="<?php echo $app_strings['LBL_CANCEL_BUTTON_TITLE']; ?>" type="button" class="crm button small cancel" style="width:90px" name="eventcancel" value="<?php echo $mod_strings['LBL_RESET']?>" onClick="ghide('addEvent')">
	  </td>
	  </tr>
	</table>
  </form>
  </div>
							 
	<!-- Add Activity DIV stops-->

<div id="eventcalAction" class="calAction" style="width:125px;" onMouseout="fninvsh('eventcalAction')" onMouseover="fnvshNrm('eventcalAction')">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFFFFF">
		<tr>
			<td>
				<?php
				if(isPermitted("Calendar","EditView") == "yes")
				{
				?>
					<?php if(getFieldVisibilityPermission('Events',$current_user->id,'eventstatus') == '0') { ?>
						<a href="javascript:;" id="complete" onClick="fninvsh('eventcalAction')" class="calMnu">- <?php echo $mod_strings['LBL_HELD']?></a>
						<a href="javascript:;" id="pending" onClick="fninvsh('eventcalAction')" class="calMnu">- <?php echo $mod_strings['LBL_NOTHELD']?></a>
					<?php }?>		
				<span style="border-top:1px dashed #CCCCCC;width:99%;display:block;"></span>
				<a href="javascript:;" id="postpone" onClick="fninvsh('eventcalAction')" class="calMnu">- <?php echo $mod_strings['LBL_POSTPONE']?></a>
				<a href="javascript:;" id="changeowner" onClick="cal_fnvshobj(this,'act_changeowner');fninvsh('eventcalAction')" class="calMnu">- <?php echo $mod_strings['LBL_CHANGEOWNER']?></a>
				<?php
				}
				if(isPermitted("Calendar","Delete") == "yes")	
				{
				?>
				<a href="" id="actdelete" onclick ="fninvsh('eventcalAction');return confirm('Are you sure?');" class="calMnu">- <?php echo $mod_strings['LBL_DEL']?></a>
				<?php
				}
				?>
			</td>
		</tr>
	</table>
</div>

<!-- Dropdown for Add Event -->
<div id='addEventDropDown' style='width:160px' onmouseover='fnShowEvent()' onmouseout='fnRemoveEvent()'>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<?php
	global $adb;
	if($current_user->column_fields['is_admin']=='on')
		$Res = $adb->pquery("select * from vtiger_activitytype",array());
	else
	{
		$role_id=$current_user->roleid;
		$subrole = getRoleSubordinates($role_id);
		if(count($subrole)> 0)
		{
			$roleids = $subrole;
			array_push($roleids, $role_id);
		}
		else
		{	
			$roleids = $role_id;
		}

		if (count($roleids) > 1) {
			$Res=$adb->pquery("select distinct activitytype from  vtiger_activitytype inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_activitytype.picklist_valueid where roleid in (". generateQuestionMarks($roleids) .") and picklistid in (select picklistid from vtiger_activitytype) order by sortid asc",array($roleids));
		} else {
			$Res=$adb->pquery("select distinct activitytype from vtiger_activitytype inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_activitytype.picklist_valueid where roleid = ? and picklistid in (select picklistid from vtiger_activitytype) order by sortid asc",array($role_id));
		}
	}
	$eventlist='';
	for($i=0; $i<$adb->num_rows($Res);$i++)
	{
		$eventlist = $adb->query_result($Res,$i,'activitytype');
?>		
	<tr><td><a href='' id="add<?php echo strtolower($eventlist);?>" class='drop_down'><?php echo getTranslatedString($eventlist,'Calendar')?></a></td></tr>
<?php
	}
?>
	<tr><td><a href='' id="addtodo" class='drop_down'><?php echo $mod_strings['LBL_ADDTODO']?></a></td></tr>
</table>
</div>
<div class="calAddEvent layerPopup" style="display:none;width:700px;left:200px;" id="createTodo" align=center>
<form name="createTodo" onSubmit="task_check_form();if(formValidate()) { VtigerJS_DialogBox.block(); } else { return false; }" method="POST" action="index.php">
<input type="hidden" name="return_action" value="index">
<input type="hidden" name="return_module" value="Calendar">
  <input type="hidden" name="module" value="Calendar">
  <input type="hidden" name="activity_mode" value="Task">
  <input type="hidden" name="action" value="TodoSave">
  <input type="hidden" name="view" value="<?php echo $calendar_arr['view'] ?>">
  <input type="hidden" name="hour" value="<?php echo $calendar_arr['calendar']->date_time->hour ?>">
  <input type="hidden" name="day" value="<?php echo $calendar_arr['calendar']->date_time->day ?>">
  <input type="hidden" name="month" value="<?php echo $calendar_arr['calendar']->date_time->month ?>">
  <input type="hidden" name="year" value="<?php echo $calendar_arr['calendar']->date_time->year ?>">
  <input type="hidden" name="record" value="">
  <input type="hidden" name="parenttab" value="<?php echo $category ?>">
  <input type="hidden" name="mode" value="">
  <input type="hidden" name="task_time_start" id="task_time_start">
  <input type="hidden" name="viewOption" value="">
  <input type="hidden" name="subtab" value="">
  <input type="hidden" name="maintab" value="Calendar">
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerHeadingULine">
		<tr style="cursor:move;">
                	<td class="lvtHeaderText" id="moveTodo" align="left"><?php echo $mod_strings['LBL_ADD_TODO'] ?></b></td>
			<td align=right><a href="javascript:ghide('createTodo');"><img src="<?php echo  vtiger_imageurl('close.gif', $theme)?>" border="0"  align="absmiddle" /></a></td>
		</tr>
        </table>
	<table border=0 cellspacing=0 cellpadding=5 width=95% bgcolor="#FFFFFF" >
		<tr>
			<td width="20%" align="right"><b><font color="red">*</font><?php echo $mod_strings['LBL_TODONAME'] ?></b></td>
                        <td width="80%" align="left"><input name="task_subject" type="text" value="" class="textbox" style="width:70%"></td>
                </tr>
		<?php if(getFieldVisibilityPermission('Calendar',$current_user->id,'description') == '0') { ?>
		<tr>
			<td align="right"><b><?php echo $mod_strings['Description'] ?></b></td>
			<td align="left"><textarea style="width: 100%; height: 60px;" name="task_description"></textarea></td>
		</tr>
		<?php } ?>
		<tr>
			<td colspan="2" align="center" width="80%">
				<table border="0" cellpadding="3" cellspacing="0" width="80%">
					<tr>
						<td align="left">
							<?php if(getFieldVisibilityPermission('Calendar',$current_user->id,'taskstatus') == '0') { ?>
							<b><?php echo $mod_strings['Status']; ?></b>
							<?php } ?>
						</td>
						<td align="left">
							<?php if(getFieldVisibilityPermission('Calendar',$current_user->id,'taskpriority') == '0') { ?>
							<b><?php echo $mod_strings['Priority']; ?></b>
							<?php } ?>
						</td>
						<td align="left">
							<?php if(getFieldVisibilityPermission('Calendar',$current_user->id,'assigned_user_id') == '0') { ?>
							<b><?php echo $mod_strings['Assigned To']; ?></b>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top">
							<?php if(getFieldVisibilityPermission('Calendar',$current_user->id,'taskstatus') == '0') { ?>
							<?php echo getActFieldCombo('taskstatus','vtiger_taskstatus'); ?>
							<?php } ?>	
						</td>
						<td align="left" valign="top">
							<?php if(getFieldVisibilityPermission('Calendar',$current_user->id,'taskpriority') == '0') { ?>
							<?php echo getActFieldCombo('taskpriority','vtiger_taskpriority'); ?>
							<?php } ?>
						</td>
						<td align="left" valign="top">
							<?php if(getFieldVisibilityPermission('Calendar',$current_user->id,'assigned_user_id') == '0') { ?>
							<?php echo getAssignedToHTML($taskassignedto,'task'); ?>
							<?php }else{
						       	?><input name="task_assigned_user_id" value="<?php echo $current_user->id ?>" type="hidden">
							<?php } ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td colspan="2">    <hr noshade="noshade" size="1"></td></tr>
	</table>
	<table bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" width="95%" align=center>
		<tr><td>
			<table border="0" cellpadding="2" cellspacing="0" width="100%" align=center>
				<tr><td width=50% valign=top style="border-right:1px solid #dddddd">
					<table border=0 cellspacing=0 cellpadding=2 width=95% align=center>
						<tr><td colspan=3 align="left"><b><?php echo $mod_strings['LBL_TODODATETIME'] ?></b></td></tr>
						<tr><td colspan=3 align="left"><?php echo getTimeCombo($calendar_arr['calendar']->hour_format,'start','','','',true); ?></td></tr>
						<tr><td align="left">
							<input type="text" name="task_date_start" id="task_date_start" class="textbox" style="width:90px" onChange="dochange('task_date_start','task_due_date');" value="<?php echo getDisplayDate($calendar_arr['calendar']->date_time->get_formatted_date()) ?>" ></td><td width=100% align="left"><img border=0 src="<?php echo $image_path ?>btnL3Calendar.gif" alt="<?php echo $mod_strings['LBL_SET_DATE']?>" title="<?php echo $mod_strings['LBL_SET_DATE']?>" id="jscal_trigger_task_date_start">
						<script type="text/javascript">
						Calendar.setup ({
							inputField : "task_date_start", ifFormat : "<?php  echo $date_format; ?>", showsTime : false, button : "jscal_trigger_task_date_start", singleClick : true, step : 1
						})
						</script>
						</td></tr>
					</table></td>	
					<td width=50% valign="top">
						<table border="0" cellpadding="2" cellspacing="0" width="95%" align=center>
							<tr><td colspan=3 align="left"><b><?php echo $mod_strings['Due Date'] ?></b></td></tr>
							<tr><td align="left">
								<input type="text" name="task_due_date" id="task_due_date" class="textbox" style="width:90px" value="<?php echo getDisplayDate($calendar_arr['calendar']->date_time->get_formatted_date()) ?>" ></td><td width=100% align="left"><img border=0 src="<?php echo $image_path ?>btnL3Calendar.gif" alt="<?php echo $mod_strings['LBL_SET_DATE']?>" title="<?php echo $mod_strings['LBL_SET_DATE']?>" id="jscal_trigger_task_due_date">
						<script type="text/javascript">
						Calendar.setup ({
							inputField : "task_due_date", ifFormat : "<?php  echo $date_format; ?>", showsTime : false, button : "jscal_trigger_task_due_date", singleClick : true, step : 1
						})
						</script>
						</td></tr>
					</table></td>
				</tr>
			</table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
	<?php  
	$custom_fields_data = getCalendarCustomFields(getTabid('Calendar'),'edit');
	$smarty=new vtigerCRM_Smarty;
	$smarty->assign("MODULE",'Calendar');
	$smarty->assign("MOD",$mod_strings);
	$smarty->assign("APP",$app_strings);
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	$smarty->assign("IMAGE_PATH", $image_path);
	if (count($custom_fields_data) > 0){ ?>
		<hr noshade size=1>
		<table>
		<tr>
			<td colspan="2">
				<b><?php echo $app_strings['LBL_CUSTOM_INFORMATION']?></b>
			</td>
		</tr>
		<tr>
			<?php 
				echo "<tr>";
				for($i=0; $i<count($custom_fields_data); $i++) {
					$maindata = $custom_fields_data[$i];
					$smarty->assign("maindata",$maindata);
					$smarty->assign("THEME", $theme);
					$smarty->display('EditViewUI.tpl');
					if (($i+1)%2 == 0) {
						echo "</tr><tr>";
					}
				}
				if ($i% 2 != 0) {
					echo '<td width="20%"></td><td width="30%"></td>';
				}
				echo "</tr>";
			?>
		</tr>
		</table>
		<br />
	<?php } ?>
				
	<?php if((getFieldVisibilityPermission('Calendar',$current_user->id,'sendnotification') == '0') || (getFieldVisibilityPermission('Calendar',$current_user->id,'parent_id') == '0') || (getFieldVisibilityPermission('Calendar',$current_user->id,'contact_id') == '0')) { ?>
	<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%" bgcolor="#FFFFFF">
		<tr>
			<td>
				<table border=0 cellspacing=0 cellpadding=3 width=100%>
					<tr>
						<td class="dvtTabCache" style="width:10px" nowrap="nowrap">&nbsp;</td>
						<?php if(getFieldVisibilityPermission('Calendar',$current_user->id,'sendnotification') == '0') { $classval = "dvtUnSelectedCell";  ?>
						<td id="cellTabNotification" class="dvtSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabNotification','on');switchClass('cellTabtodoRelatedto','off');gshow('addTaskAlarmUI','todo',document.createTodo.task_date_start.value,document.createTodo.task_due_date.value,document.createTodo.starthr.value,document.createTodo.startmin.value,document.createTodo.startfmt.value,'','','',document.createTodo.viewOption.value,document.createTodo.subtab.value);ghide('addTaskRelatedtoUI');"><?php echo $mod_strings['LBL_NOTIFICATION']?></a></td>
						<?php } else { $classval = "dvtSelectedCell"; } ?>
						<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
						<?php if((getFieldVisibilityPermission('Calendar',$current_user->id,'parent_id') == '0') || (getFieldVisibilityPermission('Calendar',$current_user->id,'contact_id') == '0')) { ?>
						<td id="cellTabtodoRelatedto" class="<?php echo $classval ; ?>" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabtodoRelatedto','on'); switchClass('cellTabNotification','off');gshow('addTaskRelatedtoUI','todo',document.createTodo.task_date_start.value,document.createTodo.task_due_date.value,document.createTodo.starthr.value,document.createTodo.startmin.value,document.createTodo.startfmt.value,'','','',document.createTodo.viewOption.value,document.createTodo.subtab.value);ghide('addTaskAlarmUI');"><?php echo $mod_strings['LBL_RELATEDTO']?></a></td>					
						<?php } ?>	
						<td class="dvtTabCache" style="width: 100%;">&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td width=100% valign=top align=left class="dvtContentSpace" style="padding:10px;height:120px">
		<!-- Reminder UI -->
		<DIV id="addTaskAlarmUI" style="display:block;width:100%">
		<?php if(getFieldVisibilityPermission('Calendar',$current_user->id,'sendnotification') == '0') { ?>
                <table>
			<tr><td><?php echo $mod_strings['LBL_SENDNOTIFICATION'] ?></td><td>
				<input name="task_sendnotification" type="checkbox">
			</td></tr>
                </table>
		<?php $vision = "none" ; } else {$vision = "block" ;} ?>
		</DIV>
		<div id="addTaskRelatedtoUI" style="display:<?php echo $vision; ?>;width:100%">
			<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<?php if(getFieldVisibilityPermission('Calendar',$current_user->id,'parent_id') == '0') { ?>
			<tr>
				<td><b><?php echo $mod_strings['LBL_RELATEDTO']?></b></td>
				<td>
					<input name="task_parent_id" type="hidden" value="">
					<input name="del_actparent_rel" type="hidden" >
						<select name="task_parent_type" class="small" id="task_parent_type" onChange="document.createTodo.task_parent_name.value='';document.createTodo.task_parent_id.value=''">
						<option value="Leads"><?php echo $app_strings['Leads']?></option>
						<option value="Accounts"><?php echo $app_strings['Accounts']?></option>
						<option value="Potentials"><?php echo $app_strings['Potentials']?></option>
						<option value="Quotes"><?php echo $app_strings['Quotes']?></option>
						<option value="PurchaseOrder"><?php echo $app_strings['PurchaseOrder']?></option>
						<option value="SalesOrder"><?php echo $app_strings['SalesOrder']?></option>
						<option value="Invoice"><?php echo $app_strings['Invoice']?></option>
						<option value="Campaigns"><?php echo $app_strings['Campaigns']?></option>
						<option value="HelpDesk"><?php echo $app_strings['HelpDesk']?></option></select>
						</select>
				</td>
				<td>
					<div id="taskrelatedto" align="left">
					<input name="task_parent_name" readonly type="text" class="calTxt small" value="">
					<input type="button" name="selectparent" class="crmButton small edit" value="<?php echo $mod_strings['LBL_SELECT']; ?>" onclick="return window.open('index.php?module='+document.createTodo.task_parent_type.value+'&action=Popup&maintab=Calendar','test','width=640,height=602,resizable=0,scrollbars=0,top=150,left=200');">
					<input type='button' value='<?php echo $app_strings['LNK_DELETE']; ?>' class="crmButton small edit" onclick="document.createTodo.del_actparent_rel.value=document.createTodo.task_parent_id.value;document.createTodo.task_parent_id.value='';document.createTodo.task_parent_name.value='';">
					</div>
				</td>
			</tr>
			<?php } ?>
			<?php if(getFieldVisibilityPermission('Calendar',$current_user->id,'contact_id') == '0') { ?>	
			<tr>
			<td><b><?php echo $mod_strings['LBL_CONTACT_NAME'] ?></b></td>
			<td colspan="2">
				<input name="task_contact_name" id="contact_name" readonly type="text" class="calTxt" value=""><input name="task_contact_id" id="contact_id" type="hidden" value="">&nbsp;
				<input name="deletecntlist"  id="deletecntlist" type="hidden">
				<input type="button" onclick="selectContact('false','task',document.createTodo);" class="crmButton small edit" name="selectcnt" value="<?php echo $mod_strings['LBL_SELECT']." ". $mod_strings['LBL_LIST_CONTACT'] ; ?>">
				<input type='button' value='<?php echo $app_strings['LNK_DELETE']; ?>' class="crmButton small edit" onclick='document.createTodo.deletecntlist.value=document.createTodo.task_contact_name.value;document.createTodo.task_contact_name.value="";document.createTodo.task_contact_id.value="";'>
			</td>
			  </tr>
			<?php } ?>
			                  </table>
					                  </div>
		</td></tr>
                <!-- Repeat UI -->
	</table>
	<?php } ?>
	<br>

                <table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
                <tr>
                        <td valign=top></td>
                        <td  align=center>
				<input alt="<?php echo $app_strings['LBL_SAVE_BUTTON_TITLE']; ?>" title="<?php echo $app_strings['LBL_SAVE_BUTTON_TITLE']; ?>" accessKey='S' type="submit" name="todosave" class="crm button small save" style="width:90px" value="<?php echo $mod_strings['LBL_SAVE'] ?>">
		<input alt="<?php echo $app_strings['LBL_CANCEL_BUTTON_TITLE']; ?>" title="<?php echo $app_strings['LBL_CANCEL_BUTTON_TITLE']; ?>" type="button" class="crm button small cancel" style="width:90px" name="todocancel" value="<?php echo $mod_strings['LBL_RESET']?>" onClick="ghide('createTodo')">
	</td></tr></table>
  </form>
  <script>
  	var fieldname = new Array('task_subject','task_date_start','task_time_start','task_due_date','taskstatus');
	var fieldlabel = new Array('<?php echo $mod_strings['LBL_LIST_SUBJECT']?>','<?php echo $mod_strings['LBL_START_DATE']?>','<?php echo $mod_strings['LBL_TIME']?>','<?php echo $mod_strings['LBL_DUE_DATE']?>','<?php echo $mod_strings['LBL_STATUS']?>');
	var fielddatatype = new Array('V~M','D~M~time_start','T~O','D~M~OTH~GE~task_date_start~Start Date & Time','V~O');
  </script>
  </div>

<div id="act_changeowner" class="statechange" style="left:250px;top:200px;z-index:5000">
	<form name="change_owner">
	<input type="hidden" value="" name="idlist" id="idlist">
	<input type="hidden" value="" name="action">
	<input type="hidden" value="" name="hour">
	<input type="hidden" value="" name="day">
	<input type="hidden" value="" name="month">
	<input type="hidden" value="" name="year">
	<input type="hidden" value="" name="view">
	<input type="hidden" value="" name="module">
	<input type="hidden" value="" name="subtab">
	<table width="100%" border="0" cellpadding="3" cellspacing="0" >
		<tr>
		<td class="genHeaderSmall" align="left" style="border-bottom:1px solid #CCCCCC;" width="60%"><?php echo $app_strings['LBL_CHANGE_OWNER']; ?></td>
			<td style="border-bottom: 1px solid rgb(204, 204, 204);">&nbsp;</td>
			<td align="right" style="border-bottom:1px solid #CCCCCC;" width="40%"><a href="javascript:fninvsh('act_changeowner')"><img src="<?php echo vtiger_imageurl('close.gif', $theme) ?>" align="absmiddle" border="0"></a></td>
		</tr>
		<tr>
		        <td colspan="3">&nbsp;</td>
	</tr>
	<tr>
	<td width="50%"><b><?php echo $app_strings['LBL_TRANSFER_OWNERSHIP']; ?></b></td>
	        <td width="2%"><b>:</b></td>
        	<td width="48%">
		<?php
		$usersList = getUserslist();
		$groupList = getGroupslist();
		?>

            <input type = "radio" id= "user_checkbox" name = "user_lead_owner"  <?php if($groupList != '') { ?> onclick=checkgroup();  <?php } ?> checked><?php echo $app_strings['LBL_USER'];?>&nbsp;
			<?php if( $groupList != '') {?>
				<input type = "radio" id = "group_checkbox" name = "user_lead_owner" onclick=checkgroup(); ><?php echo $app_strings['LBL_GROUP'];?><br>
				<select name="lead_group_owner" id="lead_group_owner" class="detailedViewTextBox" style="display:none;">    
					<?php echo getGroupslist();?>  
				</select>
			<?php } ?>
            <select name="lead_owner" id="lead_owner" class="detailedViewTextBox" style="display:block">
				<?php echo getUserslist(); ?>
            </select>
        	</td>
	</tr>
	<tr><td colspan="3" style="border-bottom:1px dashed #CCCCCC;">&nbsp;</td></tr>
	<tr>
        	<td colspan="3" align="center">
	        &nbsp;&nbsp;
<input type="button" name="button" class="crm button small save" value="<?php echo $app_strings['LBL_UPDATE_OWNER']; ?>" onClick="calendarChangeOwner();fninvsh('act_changeowner');">
		        <input type="button" name="button" class="crm button small cancel" value="<?php echo $app_strings['LBL_CANCEL_BUTTON_LABEL']; ?>" onClick="fninvsh('act_changeowner')">	
		</td>
	</tr>
	</table>
	</form>
</div>


<div id="taskcalAction" class="calAction" style="width:125px;" onMouseout="fninvsh('taskcalAction')" onMouseover="fnvshNrm('taskcalAction')">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFFFFF">
                <tr>
                        <td>
				<?php
                                if(isPermitted("Calendar","EditView") == "yes")
                                {
                                ?>
					<?php if(getFieldVisibilityPermission('Calendar',$current_user->id,'taskstatus') == '0') { ?>
	                                	<a href="" id="taskcomplete" onClick="fninvsh('taskcalAction');" class="calMnu">- <?php echo $mod_strings['LBL_COMPLETED']?></a>
        	                        	<a href="" id="taskpending" onClick="fninvsh('taskcalAction');" class="calMnu">- <?php echo $mod_strings['LBL_DEFERRED']?></a>
					<?php } ?>		
						
                                <span style="border-top:1px dashed #CCCCCC;width:99%;display:block;"></span>
                                <a href="" id="taskpostpone" onClick="fninvsh('taskcalAction');" class="calMnu">- <?php echo $mod_strings['LBL_POSTPONE']?></a>
                                <a href="" id="taskchangeowner" onClick="cal_fnvshobj(this,'act_changeowner'); fninvsh('taskcalAction');" class="calMnu">- <?php echo $mod_strings['LBL_CHANGEOWNER']?></a>
                                <?php
                                }
                                if(isPermitted("Calendar","Delete") == "yes")
                                {
                                ?>
                                <a href="" id="taskactdelete" onClick ="fninvsh('taskcalAction');return confirm('Are you sure?');" class="calMnu">- <?php echo $mod_strings['LBL_DEL']?></a>
                                <?php
                                }
                                ?>

                        </td>
                </tr>
        </table>
</div>
<script>
	//for move addEventUI
	var theEventHandle = document.getElementById("moveEvent");
	var theEventRoot   = document.getElementById("addEvent");
	Drag.init(theEventHandle, theEventRoot);

	//for move addToDo
	var theTodoHandle = document.getElementById("moveTodo");
	var theTodoRoot   = document.getElementById("createTodo");
	Drag.init(theTodoHandle, theTodoRoot);
</script>
