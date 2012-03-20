{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}

<div id="{$popupid}" style="float: right; border-style: solid; border-color: rgb(141, 141, 141); border-width: 1px 3px 3px 1px; overflow: hidden; padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 10px; margin-left: 2px; font-weight: normal; height: 75px;">

<table border='0' cellpadding='2' cellspacing='0'>
	<tr>
		<td align='left'><b>
		<img align="top" src="themes/images/{$activitytype}s.gif"/> {$activitytype} - {$cbstatus}</b> </td>
		<td align='right'><b><font color={$cbcolor}>{$cbdate} {$cbtime}</font></b></td>
		<td align='right'>
			<a style='padding-left: 10px;' href="javascript:;" onclick="ActivityReminderCallbackReset(0, '{$popupid}');ActivityReminderRemovePopupDOM('{$popupid}');"><img src='{'close.gif'|@vtiger_imageurl:$THEME}' align='absmiddle' border='0'></a></td>
	</tr>

	<tr>
		<td colspan='3'><hr></td>
	</tr>

	<tr>
		<td colspan='3' align='left'><b>{$cbsubject}</b><a style='padding: 2px;' href='index.php?action=DetailView&module={$cbmodule}&record={$cbrecord}'>[{$APP.LBL_MORE}...]</a></td>
	</tr>

	<tr>
		<td align='center'> 
			<a style='padding: 0 5px 0 5px;' href='javascript:;' onclick="ActivityReminderPostponeCallback('{$cbmodule}','{$cbrecord}','{$cbreminderid}');ActivityReminderRemovePopupDOM('{$popupid}');"><b>{$APP.LBL_POSTPONE}</b></a> 
		</td>
	</tr>
</table>

</div>
