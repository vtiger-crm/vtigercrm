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
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/menu.js"></script>

<script language="JavaScript" type="text/javascript">
    var allOptions = null;

    function setAllOptions(inputOptions) 
    {ldelim}
        allOptions = inputOptions;
    {rdelim}

    function modifyMergeFieldSelect(cause, effect) 
    {ldelim}
        var selected = cause.options[cause.selectedIndex].value;  id="mergeFieldValue"
        var s = allOptions[cause.selectedIndex];
        effect.length = s;
        for (var i = 0; i < s; i++) 
	{ldelim}
            effect.options[i] = s[i];
        {rdelim}
        document.getElementById('mergeFieldValue').value = '';
    {rdelim}
{literal}
    function init() 
    {
        var blankOption = new Option('--None--', '--None--');
        var options = null;
{/literal}

		var allOpts = new Object({$ALL_VARIABLES|@count}+1);
		{assign var="alloptioncount" value="0"}
		{foreach key=index item=module from=$ALL_VARIABLES}
	    	options = new Object({$module|@count}+1);
	    	{assign var="optioncount" value="0"}
            options[{$optioncount}] = blankOption;
            {foreach key=header item=detail from=$module}
             {assign var="optioncount" value=$optioncount+1}
				options[{$optioncount}] = new Option('{$detail.0}', '{$detail.1}');
			{/foreach}      
			 {assign var="alloptioncount" value=$alloptioncount+1}     
             allOpts[{$alloptioncount}] = options;
	    {/foreach}
        setAllOptions(allOpts);	    
    }
	
	

	
{literal}
	function cancelForm(frm)
	{
		frm.action.value='detailviewemailtemplate';
		frm.parenttab.value='Settings';
		frm.submit();
	}
{/literal}
</script>

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
<br>
	<div align=center>
	
			{include file='SetMenu.tpl'}
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				{literal}
				<form action="index.php" method="post" name="templatecreate" onsubmit="if(check4null(templatecreate)) { VtigerJS_DialogBox.block(); } else { return false; }">
				{/literal}
				<input type="hidden" name="action">
				<input type="hidden" name="mode" value="{$EMODE}">
				<input type="hidden" name="module" value="Settings">
				<input type="hidden" name="templateid" value="{$TEMPLATEID}">
				<input type="hidden" name="parenttab" value="{$PARENTTAB}">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'ViewTemplate.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_MODULE_NAME}" width="45" height="60" border=0 title="{$MOD.LBL_MODULE_NAME}"></td>
				{if $EMODE eq 'edit'}
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > <a href="index.php?module=Settings&action=listemailtemplates&parenttab=Settings">{$UMOD.LBL_EMAIL_TEMPLATES}</a> &gt; {$MOD.LBL_EDIT} &quot;{$TEMPLATENAME}&quot; </b></td>
				{else}
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > <a href="index.php?module=Settings&action=listemailtemplates&parenttab=Settings">{$UMOD.LBL_EMAIL_TEMPLATES}</a> &gt; {$MOD.LBL_CREATE_EMAIL_TEMPLATES} </b></td>
				{/if}
					
				</tr>
				<tr>
					<td valign=top class="small">{$UMOD.LBL_EMAIL_TEMPLATE_DESC}</td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
				
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						{if $EMODE eq 'edit'}
						<td class="big"><strong>{$UMOD.LBL_PROPERTIES} &quot;{$TEMPLATENAME}&quot; </strong></td>
						{else}
						<td class="big"><strong>{$MOD.LBL_CREATE_EMAIL_TEMPLATES}</strong></td>
						{/if}
						<td class="small" align=right>
							<input type="submit" value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="crmButton small save" onclick="this.form.action.value='saveemailtemplate'; this.form.parenttab.value='Settings'" >&nbsp;&nbsp;
			{if $EMODE eq 'edit'}
				<input type="submit" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="cancelForm(this.form)" />
			{else}
				<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="window.history.back()" >
			{/if}
						</td>
					</tr>
					</table>
					
					<table border=0 cellspacing=0 cellpadding=5 width=100% >
					<tr>
						<td width=20% class="small cellLabel"><font color="red">*</font><strong>{$UMOD.LBL_NAME}</strong></td>
						<td width=80% class="small cellText"><input name="templatename" type="text" value="{$TEMPLATENAME}" class="detailedViewTextBox" tabindex="1">&nbsp;</td>
					  </tr>
					<tr>
						<td valign=top class="small cellLabel"><strong>{$UMOD.LBL_DESCRIPTION}</strong></td>
						<td class="cellText small" valign=top><span class="small cellText">
						  <input name="description" type="text" value="{$DESCRIPTION}" class="detailedViewTextBox" tabindex="2">
						</span></td>
					  </tr>
					<tr>
						<td valign=top class="small cellLabel"><strong>{$UMOD.LBL_FOLDER}</strong></td>
						<td class="cellText small" valign=top>
						{if $EMODE eq 'edit'}
						<select name="foldername" class="small" tabindex="" style="width:100%" tabindex="3">
                                                    {foreach item=arr from=$FOLDERNAME}
                                                     <option value="{$FOLDERNAME}" {$arr}>{$FOLDERNAME}</option>
                                                        {if $FOLDERNAME == 'Public'}
                                                          <option value="Personal">{$UMOD.LBL_PERSONAL}</option>
                                                        {else}
                                                          <option value="Public">{$UMOD.LBL_PUBLIC}</option>
                                                         {/if}
                                                   {/foreach}
                                                 </select>
						{else}
						<select name="foldername" class="small" tabindex="" value="{$FOLDERNAME}" style="width:100%" tabindex="3">
                                                    <option value="Personal">{$UMOD.LBL_PERSONAL}</option>
                                                    <option value="Public" selected>{$UMOD.LBL_PUBLIC}</option>
        	                                </select>
						{/if}
					
						</td>
					  </tr>
					
					
					<tr>
					  <td colspan="2" valign=top class="cellText small"><table width="100%"  border="0" cellspacing="0" cellpadding="0" class="thickBorder">
                        <tr>
                          <td valign=top><table width="100%"  border="0" cellspacing="0" cellpadding="5" >
                              <tr>
                                <td colspan="3" valign="top" class="small" style="background-color:#cccccc"><strong>{$UMOD.LBL_EMAIL_TEMPLATE}</strong></td>
                                </tr>
                              <tr>
                                <td width="15%" valign="top" class="cellLabel small"><font color='red'>*</font>{$UMOD.LBL_SUBJECT}</td>
                                <td width="85%" colspan="2" class="cellText small"><span class="small cellText">
                                  <input name="subject" type="text" value="{$SUBJECT}" class="detailedViewTextBox" tabindex="4">
                                </span></td>
                              </tr> 




                             <tr>
                              
                                <td width="15%"  class="cellLabel small" valign="center">{$UMOD.LBL_SELECT_FIELD_TYPE}</td>
                                <td width="85%" colspan="2" class="cellText small">

		<table>
			<tr>
				<td>{$UMOD.LBL_STEP}1
				<td>
			
				<td style="border-left:2px dotted #cccccc;">{$UMOD.LBL_STEP}2
				<td>

				<td style="border-left:2px dotted #cccccc;">{$UMOD.LBL_STEP}3
				<td>
			</tr>
			
			<tr>
				<td>

					<select style="font-family: Arial, Helvetica, sans-serif;font-size: 11px;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffff;" id="entityType" ONCHANGE="modifyMergeFieldSelect(this, document.getElementById('mergeFieldSelect'));" tabindex="6">
                                        <OPTION VALUE="0" selected>{$APP.LBL_NONE}
                                        <OPTION VALUE="1">{$UMOD.LBL_ACCOUNT_FIELDS}                           
                                        <OPTION VALUE="2">{$UMOD.LBL_CONTACT_FIELDS}
                                        <OPTION VALUE="3" >{$UMOD.LBL_LEAD_FIELDS}
                                        <OPTION VALUE="4" >{$UMOD.LBL_USER_FIELDS}
                                        <OPTION VALUE="5" >{$UMOD.LBL_GENERAL_FIELDS}
                                        </select>
				<td>
			
				<td style="border-left:2px dotted #cccccc;">
					<select style="font-family: Arial, Helvetica, sans-serif;font-size: 11p
x;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffff;" id="mergeFieldSelect" onchange="document.getElementById('mergeFieldValue').value=this.options[this.selectedIndex].value;" tabindex="7"><option value="0" selected>{$APP.LBL_NONE}</select>	
				<td>

				<td style="border-left:2px dotted #cccccc;">	

					<input type="text"  id="mergeFieldValue" name="variable" value="variable" style="font-family: Arial, Helvetica, sans-serif;font-size: 11px;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffdd;" tabindex="8"/>
				<td>
			</tr>

		</table>
			

				</td>
                              </tr>





                              <tr>
                                <td valign="top" width=10% class="cellLabel small">{$UMOD.LBL_MESSAGE}</td>
                                 <td valign="top" colspan="2" width=60% class="cellText small"><p><textarea name="body" style="width:90%;height:200px" class=small tabindex="5">{$BODY}</textarea></p>
                              </tr>
                          </table></td>
                          
                        </tr>
                      </table></td>
					  </tr>
					</table>
					<br>
					<table border=0 cellspacing=0 cellpadding=5 width=100% >
					<tr>
					  <td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
					</tr>
					</table>
				</td>
				</tr>
				</table>	
			
			
			
			</td>
			</tr>
			</table>
		</td>
	</tr>
	</form>
	</table>
		
	</div>

</td>
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</tbody>
</table>

<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
<script type="text/javascript" defer="1">var textAreaName = null;
	var textAreaName = 'body';
	CKEDITOR.replace( textAreaName,	{ldelim}
		extraPlugins : 'uicolor',
		uiColor: '#dfdff1'
	{rdelim} ) ;
	var oCKeditor = CKEDITOR.instances[textAreaName];
</script>

<script>

function check4null(form)
{ldelim}

        var isError = false;
        var errorMessage = "";
        // Here we decide whether to submit the form.
        if (trim(form.templatename.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n template name";
                form.templatename.focus();
        {rdelim}
        if (trim(form.foldername.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n folder name";
                form.foldername.focus();
        {rdelim}
        if (trim(form.subject.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n subject";
                form.subject.focus();
        {rdelim}

        // Here we decide whether to submit the form.
        if (isError == true) {ldelim}
                alert("{$APP.MISSING_FIELDS}" + errorMessage);
                return false;
        {rdelim}
 return true;

{rdelim}

init();

</script>
