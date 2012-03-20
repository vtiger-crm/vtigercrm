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
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Vtiger Bookmarklet</title>
<script type="text/javascript" src="modules/Emails/GmailBookmarklet.js"></script>
<style type="text/css">
{literal}
.small {
	color:#000000;
	font-family:Arial,Helvetica,sans-serif;
	font-size:11px;
}
.big {
	background-color: #E7E7E7;
	font-family: Arial, Helvetica, sans-serif;
	font-size:14px;
}
.tableHeading {
	background:#D7D7D7;
	border:1px solid #DEDEDE;
}
.dvtCellLabel, .cellLabel {
	background:#F7F7F7;
	border-bottom:1px solid #DEDEDE;
	border-left:1px solid #DEDEDE;
	border-right:1px solid #DEDEDE;
	color:#545454;
	padding-left:10px;
	padding-right:10px;
	white-space:nowrap;
}
.dvtCellInfo, .cellInfo {
	border-bottom:1px solid #DEDEDE;
	border-left:1px solid #DEDEDE;
	border-right:1px solid #DEDEDE;
	padding-left:10px;
	padding-right:10px;
}
.button {
	width: 70px;
	color: white;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-weight: bold;
}
.save {
	background-color: #9CC83E;
}
.cancel {
	background-color: #E0AD07;
}

/** Hack to hide the footer from UI */
.settingsSelectedUI { display: none; }

/* Styling for result */
ul.searchResult {
	list-style-type: none;
	margin: 0; padding: 0;
}
ul.searchResult li {
	display: inline;
	margin: 0; padding: 0;
	padding-right: 10px;
	line-height: 1.5em;
}
ul.searchResult li a {
	cursor: pointer;
	color: blue;
}
ul.searchResult li a:hover {
	background-color: #565656;
	color: white;
}
{/literal}
</style>
<script type="text/javascript">
		var moduleNameFields = '{$entityNameFields}';
		var moduleEmailFields = '{$emailFields}';
</script>
</head>

<body onload="init();" class="small">
	<div id="__vtigerBookMarkletDiv__">
		<div>
		<img src="themes/images/vtigerlogo.jpg" alt="vtiger CRM" title="vtiger CRM" border="0" style="width: 80px; height: 30px"
		</div>
								
		<table width="99%" cellspacing="0" cellpadding="5" border="0" class="tableHeading">
		<tr>
			<td class="big">Google Mail Information<br/></td>
			<td class="big" align="right"><input id="__saveVtigerEmail__" value="Save" type="button" class="button save"></td>
		</tr>
		</table>
	
		<table border="0" cellpadding="5" cellspacing="0" width="99%" class="small">
			<tbody>
				<tr>
					<td class="dvtCellLabel" align="right">Subject</td>
					<td class="dvtCellInfo" colspan=2><input id="subject" class="small" value="{$subject}" size="63"></td>
				</tr>
				<tr>
					<td class="dvtCellLabel" align="right">Body</td>
					<td class="dvtCellInfo" colspan=2>
						<textarea id="description" rows="5" cols="60" class="small">Original message can be viewed &lt;a href="{$description}"&gt;here&lt;/a&gt;
Update your custom description ...</textarea>
					</td>
				</tr>
				<tr>
					<td class="dvtCellLabel">
						<select id="parentType" class="small">
							{foreach key=index item=moduleName from=$types}
							<option value="{$moduleName}">{$moduleName}</option>
							{/foreach}
						</select>
					</td>
					<td class="dvtCellInfo" width="20%">
						<span id="parentName" class="small bold">&nbsp;</span><br>
						<input id="parent_id" value="" type="hidden" />
						<input id="parentEmail" value="" type="hidden" />
						<input id="userEmail" value="{$userEmail}" type="hidden" />
					</td>
					<td class="dvtCellInfo">
						<input id="__searchaccount__" value="" size="30" />&nbsp; 
						<input class="button save" id="__searchVtigerAccount__" value="Search" type="button" />
					</td>
				</tr>
				<tr id="__vtigerAccountSearchList___">
				</tr>
			</tbody>
		</table>
	</div>
</body>
</html>