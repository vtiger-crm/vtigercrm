<!--*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
{$DATE_JS}
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-{$APP.LBL_JSCALENDAR_LANG}.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<script type="text/javascript" src="modules/CustomView/CustomView.js"></script>
<script language="JavaScript" type="text/javascript" src="include/calculator/calc.js"></script>
{literal}
<form enctype="multipart/form-data" name="CustomView" method="POST" action="index.php" onsubmit="if(mandatoryCheck()){VtigerJS_DialogBox.block();} else{ return false; }">
{/literal}
<input type="hidden" name="module" value="CustomView">
<input type="hidden" name="action" value="Save">
<input type="hidden" name="parenttab" value="{$CATEGORY}">
<input type="hidden" name="cvmodule" value="{$CVMODULE}">
<input type="hidden" name="return_module" value="{$RETURN_MODULE}">
<input type="hidden" name="record" value="{$CUSTOMVIEWID}">
<input type="hidden" name="return_action" value="{$RETURN_ACTION}">
<input type="hidden" id="user_dateformat" name="user_dateformat" value="{$DATEFORMAT}">
<script language="javascript" type="text/javascript">
var typeofdata = new Array();
typeofdata['V'] = ['e','n','s','ew','c','k'];
typeofdata['N'] = ['e','n','l','g','m','h'];
typeofdata['T'] = ['e','n','l','g','m','h'];
typeofdata['I'] = ['e','n','l','g','m','h'];
typeofdata['C'] = ['e','n'];
typeofdata['DT'] = ['e','n','l','g','m','h'];
typeofdata['D'] = ['e','n','l','g','m','h'];
typeofdata['NN'] = ['e','n','l','g','m','h'];
typeofdata['E'] = ['e','n','s','ew','c','k'];
var fLabels = new Array();
fLabels['e'] = alert_arr.EQUALS;
fLabels['n'] = alert_arr.NOT_EQUALS_TO;
fLabels['s'] = alert_arr.STARTS_WITH;
fLabels['ew'] = alert_arr.ENDS_WITH;
fLabels['c'] = alert_arr.CONTAINS;
fLabels['k'] = alert_arr.DOES_NOT_CONTAINS;
fLabels['l'] = alert_arr.LESS_THAN;
fLabels['g'] = alert_arr.GREATER_THAN;
fLabels['m'] = alert_arr.LESS_OR_EQUALS;
fLabels['h'] = alert_arr.GREATER_OR_EQUALS;
var noneLabel;
function goto_CustomAction(module)
{ldelim}
        document.location.href = "index.php?module="+module+"&action=CustomAction&record={$CUSTOMVIEWID}";
{rdelim}

function mandatoryCheck()
{ldelim}

        var mandatorycheck = false;
        var i,j;
        var manCheck = new Array({$MANDATORYCHECK});
        var showvalues = "{$SHOWVALUES}";
        if(manCheck)
        {ldelim}
                var isError = false;
                var errorMessage = "";
                if (trim(document.CustomView.viewName.value) == "") {ldelim}
                        isError = true;
                        errorMessage += "\n{$MOD.LBL_VIEW_NAME}";
                {rdelim}
                // Here we decide whether to submit the form.
                if (isError == true) {ldelim}
                        alert("{$MOD.Missing_required_fields}:" + errorMessage);
                        return false;
                {rdelim}
		
		for(i=1;i<=9;i++)
                {ldelim}
                        var columnvalue = document.getElementById("column"+i).value;
                        if(columnvalue != null)
                        {ldelim}
                                for(j=0;j<manCheck.length;j++)
                                {ldelim}
                                        if(columnvalue == manCheck[j])
                                        {ldelim}
                                                mandatorycheck = true;
                                        {rdelim}
                                {rdelim}
                                if(mandatorycheck == true)
                                {ldelim}
					if(($("jscal_field_date_start").value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0) || ($("jscal_field_date_end").value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0))
						return stdfilterdateValidate();
					else
						return true;
                                {rdelim}else
                                {ldelim}
                                        mandatorycheck = false;
                                {rdelim}
                        {rdelim}
                {rdelim}
        {rdelim}
        if(mandatorycheck == false)
        {ldelim}
                alert("{$APP.MUSTHAVE_ONE_REQUIREDFIELD}"+showvalues);
        {rdelim}
        
        return false;
{rdelim}
</script>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
 <tbody><tr>
  <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
  <td class="showPanelBg" valign="top" width="100%">
   <div class="small" style="padding: 20px;">
	<span class="lvtHeaderText"><a class="hdrLink" href="index.php?action=ListView&module={$MODULE}&parenttab={$CATEGORY}">{$MODULELABEL}</a> &gt;
	{if $EXIST eq "true" && $EXIST neq ''}
		{$MOD.Edit_Custom_View}
	{else}
	 	{$MOD.New_Custom_View}
	{/if}
	</span> <br>
      <hr noshade="noshade" size="1">
      <form name="EditView" method="post" enctype="multipart/form-data" action="index.php">
      <table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
      <tbody><tr>
      <td align="left" valign="top">
      <table width="100%"  border="0" cellspacing="0" cellpadding="5">
		<tr>
		 	<td colspan="4" class="detailedViewHeader"><strong>{$MOD.Details}</strong></td>
		</tr>
		<tr>
			<td colspan=4 width="100%" style="padding:0px">
			<table cellpadding=4 cellspacing=0 width=100% border=0>
				<tr>
					<td class="dvtCellInfo" width="10%" align="right"><span class="style1">*</span>{$MOD.LBL_VIEW_NAME}
					</td>
					<td class="dvtCellInfo" width="30%">
						<input class="detailedViewTextBox" type="text" name='viewName' value="{$VIEWNAME}" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" size="40"/>
		 			</td>
		 			<td class="dvtCellInfo" width="20%">
		  			{if $CHECKED eq 'checked'}
		      			<input type="checkbox" name="setDefault" value="1" checked/>{$MOD.LBL_SETDEFAULT}
		  			{else}
		      			<input type="checkbox" name="setDefault" value="0" />{$MOD.LBL_SETDEFAULT}
		  			{/if}
		 			</td>
		 			<td class="dvtCellInfo" width="20%">
		  			{if $MCHECKED eq 'checked'}
		      			<input type="checkbox" name="setMetrics" value="1" checked/>{$MOD.LBL_LIST_IN_METRICS}
		  			{else}
		      			<input type="checkbox" name="setMetrics" value="0" />{$MOD.LBL_LIST_IN_METRICS}
		  			{/if}
		 			</td>
					<td class="dvtCellInfo" width="20%">
					{if $STATUS eq '' || $STATUS eq 1}
						<input type="checkbox" name="setStatus" value="1"/>
					{elseif $STATUS eq 2}
						<input type="checkbox" name="setStatus" value="2" checked/>
					{elseif $STATUS eq 3 || $STATUS eq 0}
						<input type="checkbox" name="setStatus" value="3" checked/>
					{/if}
						{$MOD.LBL_SET_AS_PUBLIC}
					</td>
				</tr>
			</table>
			</td>
		</tr>
		<tr><td colspan="4">&nbsp;</td></tr>
		<tr>
		 <td colspan="4" class="detailedViewHeader">
		  <b>{$MOD.LBL_STEP_2_TITLE} </b>
		 </td>
		</tr>
		<tr class="dvtCellLabel">
		  <td><select name="column1" id="column1" onChange="checkDuplicate();" class="small">
	                <option value="">{$MOD.LBL_NONE}</option>
			{foreach item=filteroption key=label from=$CHOOSECOLUMN1}
				<optgroup label="{$label}" class=\"select\" style=\"border:none\">
					{foreach item=text from=$filteroption}
					 {assign var=option_values value=$text.text}
		   		         <option {$text.selected} value={$text.value}>
			{if $MOD.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$MOD.$option_values}
                                {/if}
                        {elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$APP.$option_values}
                                {/if}
                        {else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$option_values}
                                {/if}
                        {/if}
					</option>
				{/foreach}
			{/foreach}
          	        {$CHOOSECOLUMN1}
	              </select></td>
		   <td><select name="column2" id="column2" onChange="checkDuplicate();" class="small">
                        <option value="">{$MOD.LBL_NONE}</option>
                        {foreach item=filteroption key=label from=$CHOOSECOLUMN2}
                                <optgroup label="{$label}" class=\"select\" style=\"border:none\">
                                {foreach item=text from=$filteroption}
                              	 {assign var=option_values value=$text.text}
		   		         <option {$text.selected} value={$text.value}>
			{if $MOD.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$MOD.$option_values}
                                {/if}
                        {elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$APP.$option_values}
                                {/if}
                        {else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$option_values}
                                {/if}
                        {/if}
					</option>
                                {/foreach}
                        {/foreach}
                        {$CHOOSECOLUMN2}
                      </select></td>
		   <td><select name="column3" id="column3" onChange="checkDuplicate();" class="small">
                        <option value="">{$MOD.LBL_NONE}</option>
                        {foreach item=filteroption key=label from=$CHOOSECOLUMN3}
                                <optgroup label="{$label}" class=\"select\" style=\"border:none\">
                                {foreach item=text from=$filteroption}
                                    	 {assign var=option_values value=$text.text}
		   		         <option {$text.selected} value={$text.value}>
			{if $MOD.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$MOD.$option_values}
                                {/if}
                        {elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$APP.$option_values}
                                {/if}
                        {else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$option_values}
                                {/if}
                        {/if}
					</option>
                                {/foreach}
                        {/foreach}
                        {$CHOOSECOLUMN3}
                      </select></td>
		   <td><select name="column4" id="column4" onChange="checkDuplicate();" class="small">
                        <option value="">{$MOD.LBL_NONE}</option>
                        {foreach item=filteroption key=label from=$CHOOSECOLUMN4}
                                <optgroup label="{$label}" class=\"select\" style=\"border:none\">
                                {foreach item=text from=$filteroption}
                                    	 {assign var=option_values value=$text.text}
		   		         <option {$text.selected} value={$text.value}>
			{if $MOD.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$MOD.$option_values}
                                {/if}
                        {elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$APP.$option_values}
                                {/if}
                        {else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$option_values}
                                {/if}
                        {/if}
					</option>
                                {/foreach}
                        {/foreach}
                        {$CHOOSECOLUMN4}
                      </select></td>
			
		</tr>
		<tr class="dvtCellInfo">
		   <td><select name="column5" id="column5" onChange="checkDuplicate();" class="small">
                        <option value="">{$MOD.LBL_NONE}</option>
                        {foreach item=filteroption key=label from=$CHOOSECOLUMN5}
                                <optgroup label="{$label}" class=\"select\" style=\"border:none\">
                                {foreach item=text from=$filteroption}
                                    	 {assign var=option_values value=$text.text}
		   		         <option {$text.selected} value={$text.value}>
			{if $MOD.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$MOD.$option_values}
                                {/if}
                        {elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$APP.$option_values}
                                {/if}
                        {else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$option_values}
                                {/if}
                        {/if}
					</option>
                                {/foreach}
                        {/foreach}
                        {$CHOOSECOLUMN5}
                      </select></td>
                   <td><select name="column6" id="column6" onChange="checkDuplicate();" class="small">
                        <option value="">{$MOD.LBL_NONE}</option>
                        {foreach item=filteroption key=label from=$CHOOSECOLUMN6}
                                <optgroup label="{$label}" class=\"select\" style=\"border:none\">
                                {foreach item=text from=$filteroption}
                                   	 {assign var=option_values value=$text.text}
		   		         <option {$text.selected} value={$text.value}>
			{if $MOD.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$MOD.$option_values}
                                {/if}
                        {elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$APP.$option_values}
                                {/if}
                        {else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$option_values}
                                {/if}
                        {/if}
					</option>
                                {/foreach}
                        {/foreach}
                        {$CHOOSECOLUMN6}
                      </select></td>
                   <td><select name="column7" id="column7" onChange="checkDuplicate();" class="small">
                        <option value="">{$MOD.LBL_NONE}</option>
                        {foreach item=filteroption key=label from=$CHOOSECOLUMN7}
                                <optgroup label="{$label}" class=\"select\" style=\"border:none\">
                                {foreach item=text from=$filteroption}
                                    	 {assign var=option_values value=$text.text}
		   		         <option {$text.selected} value={$text.value}>
			{if $MOD.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$MOD.$option_values}
                                {/if}
                        {elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$APP.$option_values}
                                {/if}
                        {else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$option_values}
                                {/if}
                        {/if}
					</option>
                                {/foreach}
                        {/foreach}
                        {$CHOOSECOLUMN7}
                      </select></td>
                   <td><select name="column8" id="column8" onChange="checkDuplicate();" class="small">
                        <option value="">{$MOD.LBL_NONE}</option>
                        {foreach item=filteroption key=label from=$CHOOSECOLUMN8}
                                <optgroup label="{$label}" class=\"select\" style=\"border:none\">
                                {foreach item=text from=$filteroption}
                                    	 {assign var=option_values value=$text.text}
		   		         <option {$text.selected} value={$text.value}>
			{if $MOD.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$MOD.$option_values}
                                {/if}
                        {elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$APP.$option_values}
                                {/if}
                        {else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$option_values}
                                {/if}
                        {/if}
					</option>
                                {/foreach}
                        {/foreach}
                        {$CHOOSECOLUMN8}
			</select></td>
		</tr>
		<tr class="dvtCellLabel">
		   <td><select name="column9" id="column9" onChange="checkDuplicate();" class="small">
                        <option value="">{$MOD.LBL_NONE}</option>
                        {foreach item=filteroption key=label from=$CHOOSECOLUMN9}
                                <optgroup label="{$label}" class=\"select\" style=\"border:none\">
                                {foreach item=text from=$filteroption}
                                    	 {assign var=option_values value=$text.text}
		   		         <option {$text.selected} value={$text.value}>
			{if $MOD.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$MOD.$option_values}
                                {/if}
                        {elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$APP.$option_values}
                                {/if}
                        {else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                        {$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
                                        {$option_values}
                                {/if}
                        {/if}
					 </option>
                                {/foreach}
                        {/foreach}
                        {$CHOOSECOLUMN9}
                        </select></td>
		     <td>&nbsp;</td>
		     <td>&nbsp;</td>
		     <td>&nbsp;</td>
		</tr>	
		{*section name=SelectColumn start=1 loop=4 step=1}
		<tr class="{cycle values="dvtCellLabel,dvtCellInfo"}">
		 {section name=Column start=1 loop=5 step=1}
		<td align="center">
		{math equation="(x-1)*4+ y" x=$smarty.section.SelectColumn.index y=$smarty.section.Column.index}.&nbsp;	
		  <select id="column{math equation="(x-1)*4+ y" x=$smarty.section.SelectColumn.index y=$smarty.section.Column.index}" name ="column{math equation="(x-1)*4+ y" x=$smarty.section.SelectColumn.index y=$smarty.section.Column.index}" class="detailedViewTextBox">
		   <option value="">{$MOD.LBL_NONE}</option>
		   {foreach item=filteroption key=label from={$CHOOSECOLUMN|cat: {math equation="(x-1)*4+ y" x=$smarty.section.SelectColumn.index y=$smarty.section.Column.index}}}
		    <optgroup label="{$label}" class=\"select\" style=\"border:none\">
		    {foreach item=text from=$filteroption}
		     <option {$text.selected} value={$text.value}>{$text.text}</option>
		    {/foreach}
		   {/foreach}
		  </select>
		 </td>
		 {/section}
	        </tr>
		{/section*}
		<tr><td colspan="4">&nbsp;</td></tr>
		<tr><td colspan="4"><table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
		<tbody><tr>
		 <td>
		  <table class="small" border="0" cellpadding="3" cellspacing="0" width="100%">
		   <tbody><tr>
		    <td class="dvtTabCache" style="width: 10px;" nowrap>&nbsp;</td>
		     {if $STDCOLUMNSCOUNT neq 0}	
		    <td style="width: 100px;" nowrap class="dvtSelectedCell" id="pi" onclick="fnLoadCvValues('pi','mi','mnuTab','mnuTab2')">
		     <b>{$MOD.LBL_STEP_3_TITLE}</b>
		    </td>
		    <td class="dvtUnSelectedCell" style="width: 100px;" align="center" nowrap id="mi" onclick="fnLoadCvValues('mi','pi','mnuTab2','mnuTab')">
		     <b>{$MOD.LBL_STEP_4_TITLE}</b>
		    </td>
		    {else}
                    <td class="dvtSelectedCell" style="width: 100px;" align="center" nowrap id="mi">
                     <b>{$MOD.LBL_STEP_4_TITLE}</b>
                    </td>

                    {/if}	
		    <td class="dvtTabCache" nowrap style="width:55%;">&nbsp;</td>
		   </tr>
		   </tbody>
	          </table>
		 </td>
	        </tr>
		<tr>
		 <td align="left" valign="top">
		{if $STDCOLUMNSCOUNT eq 0}
                        {assign var=stddiv value="style=display:none"}
                        {assign var=advdiv value="style=display:block"}
                {else}
                        {assign var=stddiv value="style=display:block"}
                        {assign var=advdiv value="style=display:none"}
                {/if}
		  <div id="mnuTab" {$stddiv}>
		     <table width="100%" cellspacing="0" cellpadding="5" class="dvtContentSpace">
                      <tr><td><br>
			<table width="75%" border="0" cellpadding="5" cellspacing="0" align="center">
			  <tr><td colspan="2" class="detailedViewHeader"><b>{$MOD.Simple_Time_Filter}</b></td></tr>
			  <tr>
			     <td width="75%" align="right" class="dvtCellLabel">{$MOD.LBL_Select_a_Column} :</td>
			     <td width="25%" class="dvtCellInfo">
				<select name="stdDateFilterField" class="select small" onchange="standardFilterDisplay();">
				{foreach item=stdfilter from=$STDFILTERCOLUMNS}
					<option {$stdfilter.selected} value={$stdfilter.value}>{$stdfilter.text}</option>	
				{/foreach}
                                </select>
			  </tr>
			  <tr>
			     <td align="right" class="dvtCellLabel">{$MOD.Select_Duration} :</td>
			     <td class="dvtCellInfo">
			        <select name="stdDateFilter" id="stdDateFilter" class="select small" onchange='showDateRange(this.options[this.selectedIndex].value )'>
				{foreach item=duration from=$STDFILTERCRITERIA}
					<option {$duration.selected} value={$duration.value}>{$duration.text}</option>
				{/foreach}
				</select>
			     </td>
			  </tr>
			  <tr>
			     <td align="right" class="dvtCellLabel">{$MOD.Start_Date} :</td>
			     <td width="25%" align=left class="dvtCellInfo">
			     {if $STDFILTERCRITERIA.0.selected eq "selected" || $CUSTOMVIEWID eq ""}
				{assign var=img_style value="visibility:visible"}
				{assign var=msg_style value=""}
			     {else}
				{assign var=img_style value="visibility:hidden"}
				{assign var=msg_style value="readonly"}
			     {/if}	
			     <input name="startdate" id="jscal_field_date_start" type="text" size="10" class="textField small" value="{$STARTDATE}" {$msg_style}>
			     <img src="{$IMAGE_PATH}btnL3Calendar.gif" id="jscal_trigger_date_start" style={$img_style}>
			     <font size=1><em old="(yyyy-mm-dd)">({$DATEFORMAT})</em></font>
			     <script type="text/javascript">
			  		Calendar.setup ({ldelim}
			 		inputField : "jscal_field_date_start", ifFormat : "{$JS_DATEFORMAT}", showsTime : false, button : "jscal_trigger_date_start", singleClick : true, step : 1
					{rdelim})
			     </script></td>
	            	  </tr>
			  <tr>
			     <td align="right" class="dvtCellLabel">{$MOD.End_Date} :</td> 
  			     <td width="25%" align=left class="dvtCellInfo">
			     <input name="enddate" {$msg_style} id="jscal_field_date_end" type="text" size="10" class="textField small" value="{$ENDDATE}">
			     <img src="{$IMAGE_PATH}btnL3Calendar.gif" id="jscal_trigger_date_end" style={$img_style}>
			     <font size=1><em old="(yyyy-mm-dd)">({$DATEFORMAT})</em></font>
			     <script type="text/javascript">
					Calendar.setup ({ldelim}
					inputField : "jscal_field_date_end", ifFormat : "{$JS_DATEFORMAT}", showsTime : false, button : "jscal_trigger_date_end", singleClick : true, step : 1
					{rdelim})
			     </script></td>
			  </tr>
			</table>
		      </td></tr>
		      <tr><td>&nbsp;</td></tr>
            </table>
   </div>
   <div id="mnuTab2" {$advdiv} >
      <table width="100%" cellspacing="0" cellpadding="5" class="dvtContentSpace">
       <tr><td>&nbsp;</td></tr>
       <tr><td class="dvtCellInfo">{$MOD.LBL_AF_HDR1}<br /><br />
	<li style="margin-left:30px;">{$MOD.LBL_AF_HDR2}</li>
	<li style="margin-left:30px;">{$MOD.LBL_AF_HDR3}</li>
	<br /><br />
       </td></tr>
       <tr><td>
	<table width="75%" border="0" cellpadding="5" cellspacing="0" align="center">
	  <tr><td colspan="3" class="detailedViewHeader"><b>{$MOD.LBL_RULE}</b></td></tr>
	  
	  <tr class="dvtCellLabel">
          <td><nobr><select name="fcol1" id="fcol1" onchange="updatefOptions(this, 'fop1');" class="small">
              <option value="">{$MOD.LBL_NONE}</option>
              {foreach item=filteroption key=label from=$BLOCK1}
                <optgroup label="{$label}" class=\"select\" style=\"border:none\">
                {foreach item=text from=$filteroption}
                  <option {$text.selected} value={$text.value}>{$text.text}</option>
                {/foreach}
              {/foreach}
              </select> &nbsp; <select name="fop1" id="fop1" class="small">
              <option value="">{$MOD.LBL_NONE}</option>
              {foreach item=criteria from=$FOPTION1}
                <option {$criteria.selected} value={$criteria.value}>{$criteria.text}</option>
              {/foreach}
              </select>&nbsp; <input name="fval1" id="fval1" type="text" size=30 maxlength=80 value="{$VALUE1}" class="small">
	      <span id="andfcol1">{$AND_TEXT1}</span></nobr>
            </td>
        </tr>
	<tr class="dvtCellInfo">
          <td><nobr><select name="fcol2" id="fcol2" onchange="updatefOptions(this, 'fop2');" class="small">
              <option value="">{$MOD.LBL_NONE}</option>
              {foreach item=filteroption key=label from=$BLOCK2}
                <optgroup label="{$label}" class=\"select\" style=\"border:none\">
                {foreach item=text from=$filteroption}
                  <option {$text.selected} value={$text.value}>{$text.text}</option>
                {/foreach}
              {/foreach}
              </select> &nbsp; <select name="fop2" id="fop2" class="small">
              <option value="">{$MOD.LBL_NONE}</option>
              {foreach item=criteria from=$FOPTION2}
                <option {$criteria.selected} value={$criteria.value}>{$criteria.text}</option>
              {/foreach}
              </select>&nbsp; <input name="fval2" id="fval2" type="text" size=30 maxlength=80 value="{$VALUE2}" class="small">
	      <span id="andfcol2">{$AND_TEXT2}</span></nobr>
            </td>
        </tr>
	<tr class="dvtCellLabel">
          <td><nobr><select name="fcol3" id="fcol3" onchange="updatefOptions(this, 'fop3');" class="small">
              <option value="">{$MOD.LBL_NONE}</option>
              {foreach item=filteroption key=label from=$BLOCK3}
                <optgroup label="{$label}" class=\"select\" style=\"border:none\">
                {foreach item=text from=$filteroption}
                  <option {$text.selected} value={$text.value}>{$text.text}</option>
                {/foreach}
              {/foreach}
              </select> &nbsp; <select name="fop3" id="fop3" class="small">
              <option value="">{$MOD.LBL_NONE}</option>
              {foreach item=criteria from=$FOPTION3}
                <option {$criteria.selected} value={$criteria.value}>{$criteria.text}</option>
              {/foreach}
              </select>&nbsp; <input name="fval3" id="fval3" type="text" size=30 maxlength=80 value="{$VALUE3}" class="small">
	      <span id="andfcol3">{$AND_TEXT3}</span></nobr>
            </td>
        </tr>
	<tr class="dvtCellInfo">
          <td><nobr><select name="fcol4" id="fcol4" onchange="updatefOptions(this, 'fop4');" class="small">
              <option value="">{$MOD.LBL_NONE}</option>
              {foreach item=filteroption key=label from=$BLOCK4}
                <optgroup label="{$label}" class=\"select\" style=\"border:none\">
                {foreach item=text from=$filteroption}
                  <option {$text.selected} value={$text.value}>{$text.text}</option>
                {/foreach}
              {/foreach}
              </select> &nbsp; <select name="fop4" id="fop4" class="small">
              <option value="">{$MOD.LBL_NONE}</option>
              {foreach item=criteria from=$FOPTION4}
                <option {$criteria.selected} value={$criteria.value}>{$criteria.text}</option>
              {/foreach}
              </select>&nbsp; <input name="fval4" id="fval4" type="text" size=30 maxlength=80 value="{$VALUE4}" class="small">
	      <span id="andfcol4">{$AND_TEXT4}</span></nobr>
            </td>
        </tr>
	<tr class="dvtCellLabel">
          <td><nobr><select name="fcol5" id="fcol5" onchange="updatefOptions(this, 'fop5');" class="small">
              <option value="">{$MOD.LBL_NONE}</option>
              {foreach item=filteroption key=label from=$BLOCK5}
                <optgroup label="{$label}" class=\"select\" style=\"border:none\">
                {foreach item=text from=$filteroption}
                  <option {$text.selected} value={$text.value}>{$text.text}</option>
                {/foreach}
              {/foreach}
              </select> &nbsp; <select name="fop5" id="fop5" class="small">
              <option value="">{$MOD.LBL_NONE}</option>
              {foreach item=criteria from=$FOPTION5}
                <option {$criteria.selected} value={$criteria.value}>{$criteria.text}</option>
              {/foreach}
              </select>&nbsp; <input name="fval5" id="fval5" type="text" size=30 maxlength=80 value="{$VALUE5}" class="small">
	      <span id="andfcol5">{$AND_TEXT5}</span></nobr>
            </td>
        </tr>

	  {*section name=advancedFilter start=1 loop=6 step=1}
	  <tr class="{cycle values="dvtCellInfo,dvtCellLabel"}">
	    <td align="left" width="33%">
	      <select name="fcol{$smarty.section.advancedFilter.index}" id="fcol{$smarty.section.advancedFilter.index}" onchange="updatefOptions(this, 'fop{$smarty.section.advancedFilter.index}'); class="detailedViewTextBox">
	      <option value="">{$MOD.LBL_NONE}</option>
	      {foreach item=filteroption key=label from=$BLOCK}
		<optgroup label="{$label}" class=\"select\" style=\"border:none\">
		{foreach item=text from=$filteroption}
		  <option {$text.selected} value={$text.value}>{$text.text}</option>
		{/foreach}
	      {/foreach}
	      </select>
	    </td>
	    <td align="left" width="33%">
	      <select name="fcol{$smarty.section.advancedFilter.index}" id="fcol{$smarty.section.advancedFilter.index}" class="detailedViewTextBox">
	      <option value="">{$MOD.LBL_NONE}</option>
	      {foreach item=criteria from=$FOPTION}
		<option {$criteria.selected} value={$criteria.value}>{$criteria.text}</option>
	      {/foreach}
	      </select>
	    </td>
	    <td width="34%" nowrap><input name="txt" value="" class="detailedViewTextBox" type="text"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'"/>&nbsp;And</td>
	  </tr>
	  {/section*}
	</table>
       </td></tr>
       <tr><td>&nbsp;</td></tr>
     </table>
   </div>
  </td></tr>
  </table>
  </td></tr>
  <tr><td colspan="4">&nbsp;</td></tr>
  <tr><td colspan="4" style="padding: 5px;">
	<div align="center">
	  <input title="{$APP.LBL_SAVE_BUTTON_LABEL} [Alt+S]" accesskey="S" class="crmbutton small save"  name="button2" value="{$APP.LBL_SAVE_BUTTON_LABEL}" style="width: 70px;" type="submit" onClick="return checkDuplicate();"/>
	  <input title="{$APP.LBL_CANCEL_BUTTON_LABEL} [Alt+X]" accesskey="X" class="crmbutton small cancel" name="button2" onclick='window.history.back()' value="{$APP.LBL_CANCEL_BUTTON_LABEL}" style="width: 70px;" type="button" />
	</div>
  </td></tr>
  <tr><td colspan="4">&nbsp;</td></tr>
</table>
</table>
</table>
{$STDFILTER_JAVASCRIPT}
{$JAVASCRIPT}
<!-- to show the mandatory fields while creating new customview -->
<script language="javascript" type="text/javascript">
var k;
var colOpts;
var manCheck = new Array({$MANDATORYCHECK});
{literal}
if(document.CustomView.record.value == '')
{
  for(k=0;k<manCheck.length;k++)
  {
      selname = "column"+(k+1);
      colOpts = document.getElementById(selname).options;
      for (l=0;l<colOpts.length;l++)
      {
        if(colOpts[l].value == manCheck[k])
        {
          colOpts[l].selected = true;
        }
      }
  }
}
function checkDuplicate()
{
	if(getObj('viewName').value.toLowerCase() == 'all')
	{
		alert(alert_arr.ALL_FILTER_CREATION_DENIED);
		return false;
	}
	var cvselect_array = new Array('column1','column2','column3','column4','column5','column6','column7','column8','column9')
		for(var loop=0;loop < cvselect_array.length-1;loop++)
		{
			selected_cv_columnvalue = $(cvselect_array[loop]).options[$(cvselect_array[loop]).selectedIndex].value;
			if(selected_cv_columnvalue != '')
			{	
				for(var iloop=0;iloop < cvselect_array.length;iloop++)
				{
					if(iloop == loop)
						iloop++;
					selected_cv_icolumnvalue = $(cvselect_array[iloop]).options[$(cvselect_array[iloop]).selectedIndex].value;	
					if(selected_cv_columnvalue == selected_cv_icolumnvalue)
					{
						{/literal}
                                                alert('{$APP.COLUMNS_CANNOT_BE_DUPLICATED}');
                                                $(cvselect_array[iloop]).selectedIndex = 0;
                                                return false;
                                                {literal}
					}

				}
			}
		}

if(!checkval())
	return false;


		return true;
}
checkDuplicate();
function stdfilterdateValidate()
{
	if(!dateValidate("startdate",alert_arr.STDFILTER+" - "+alert_arr.STARTDATE,"OTH"))
	{
		getObj("startdate").focus()
		return false;
	}
	else if(!dateValidate("enddate",alert_arr.STDFILTER+" - "+alert_arr.ENDDATE,"OTH"))
	{
		getObj("enddate").focus()
		return false;
	}
	else
	{
		if (!dateComparison("enddate",alert_arr.STDFILTER+" - "+alert_arr.ENDDATE,"startdate",alert_arr.STDFILTER+" - "+alert_arr.STARTDATE,"GE")) {
                        getObj("enddate").focus()
                        return false
                } else return true;
	}
}
for(var i=1;i<=5;i++)
{
	var obj=document.getElementById("fcol"+i);
	if(obj.selectedIndex != 0)
		updatefOptions(obj, 'fop'+i);
}
standardFilterDisplay();
{/literal}
</script>
