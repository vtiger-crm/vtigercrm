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
{literal}
<style>
.showTable{
	display:inline-table;
}
.hideTable{
	display:none;
}
</style>
{/literal}
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
        <br>
	<div align=center>
			{include file='SetMenu.tpl'}
				<!-- DISPLAY -->
				<form action="index.php" method="post" name="profileform" id="form" onsubmit="VtigerJS_DialogBox.block();">
				<input type="hidden" name="module" value="Users">		
				<input type="hidden" name="parenttab" value="Settings">
				<input type="hidden" name="action" value="{$ACTION}">		
				<input type="hidden" name="mode" value="{$MODE}">	
				<input type="hidden" name="profileid" value="{$PROFILEID}">
				<input type="hidden" name="profile_name" value="{$PROFILE_NAME}">
				<input type="hidden" name="profile_description" value="{$PROFILE_DESCRIPTION}">
				<input type="hidden" name="parent_profile" value="{$PARENTPROFILEID}">
				<input type="hidden" name="radio_button" value="{$RADIOBUTTON}">	
				<input type="hidden" name="return_action" value="{$RETURN_ACTION}">	

				<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tbody><tr>
					<td rowspan="2" valign="top" width="50"><img src="{'ico-profile.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_PROFILES}" title="{$MOD.LBL_PROFILES}" border="0" height="48" width="48"></td>
					<td class="heading2" valign="bottom"><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > <a href="index.php?module=Settings&action=ListProfiles&parenttab=Settings">{$CMOD.LBL_PROFILE_PRIVILEGES}</a> &gt; {$CMOD.LBL_VIEWING} &quot;{$PROFILE_NAME}&quot;</b></td>
				</tr>
				<tr>
					<td class="small" valign="top">{$CMOD.LBL_PROFILE_MESG} &quot;{$PROFILE_NAME}&quot; </td>
				</tr>
				</tbody></table>
				
				
				<table border="0" cellpadding="10" cellspacing="0" width="100%">
				<tbody><tr>
				<td valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
                      <tbody><tr>
                        <td><table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tbody><tr class="small">
                              <td><img src="{'prvPrfTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
                              <td class="prvPrfTopBg" width="100%"></td>
                              <td><img src="{'prvPrfTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
                            </tr>
                          </tbody></table>
                            <table class="prvPrfOutline" border="0" cellpadding="0" cellspacing="0" width="100%">
                              <tbody><tr>
                                <td><!-- tabs -->
                                    
                                    <!-- Headers -->
                                    <table border="0" cellpadding="5" cellspacing="0" width="100%">
                                      <tbody><tr>
                                        <td><table class="small" border="0" cellpadding="5" cellspacing="0" width="100%">
                                            <tbody><tr>
                                              <td><!-- Module name heading -->
                                                  <table class="small" border="0" cellpadding="2" cellspacing="0">
                                                    <tbody><tr>
                                                      <td valign="top"><img src="{'prvPrfHdrArrow.gif'|@vtiger_imageurl:$THEME}"> </td>
                                                      <td class="prvPrfBigText"><b> {if $MODE eq 'create'}{$CMOD.LBL_STEP_2_2} : {/if}{$CMOD.LBL_DEFINE_PRIV_FOR} &lt;{$PROFILE_NAME}&gt; </b><br>
                                                      <font class="small">{$CMOD.LBL_USE_OPTION_TO_SET_PRIV}</font> </td>
                                                      <td class="small" style="padding-left: 10px;" align="right"></td>
                                                    </tr>
                                                </tbody></table></td>
                                              <td align="right" valign="bottom">&nbsp;											 	{if $ACTION eq 'SaveProfile'}
                                                <input type="submit" value=" {$CMOD.LBL_FINISH_BUTTON} " name="save" class="crmButton create small" title="{$CMOD.LBL_FINISH_BUTTON}"/>&nbsp;&nbsp;
                                                {else}
                                                        <input type="submit" value=" {$APP.LBL_SAVE_BUTTON_LABEL} " name="save" class="crmButton small save" title="{$APP.LBL_SAVE_BUTTON_LABEL}"/>&nbsp;&nbsp;
                                                {/if}
                                                <input type="button" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " name="Cancel" class="crmButton cancel small" title="{$APP.LBL_CANCEL_BUTTON_LABEL}" onClick="window.history.back();" /> 
						</td>
                                            </tr>
                                          </tbody></table>
                                            <!-- privilege lists -->
                                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                              <tbody><tr>
                                                <td style="height: 10px;" align="center"></td>
                                              </tr>
                                            </tbody></table>
                                            <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                              <tbody><tr>
                                                <td>
						<table border="0" cellpadding="5" cellspacing="0" width="100%">
  						<tbody>
							<tr>
    							<td class="cellLabel big"> {$CMOD.LBL_SUPER_USER_PRIV} </td>
						       </tr>
						</tbody>
						</table>
						<table class="small" align="center" border="0" cellpadding="5" cellspacing="0" width="90%">
                                                <tbody><tr>
                                                    <td class="prvPrfTexture" style="width: 20px;">&nbsp;</td>
                                                    <td valign="top" width="97%"><table class="small" border="0" cellpadding="2" cellspacing="0" width="100%">
                                                      <tbody>
				                         <tr id="gva">
                                                          <td valign="top">{$GLOBAL_PRIV.0}</td>
                                                          <td ><b>{$CMOD.LBL_VIEW_ALL}</b> </td>
                                                        </tr>
                                                        <tr>
                                                          <td valign="top"></td>
                                                          <td width="100%" >{$CMOD.LBL_ALLOW} "{$PROFILE_NAME}" {$CMOD.LBL_MESG_VIEW}</td>
                                                        </tr>
                                                        <tr>
                                                          <td>&nbsp;</td>
                                                        </tr>
							<tr>
							<td valign="top">{$GLOBAL_PRIV.1}</td>
							<td ><b>{$CMOD.LBL_EDIT_ALL}</b> </td>
							</tr>
                                                        <tr>
                                                          <td valign="top"></td>
                                                          <td > {$CMOD.LBL_ALLOW} "{$PROFILE_NAME}" {$CMOD.LBL_MESG_EDIT}</td>
                                                        </tr>

                                                      </tbody></table>
						</td>
                                                  </tr>
                                                </tbody></table>
<br>

			<table border="0" cellpadding="5" cellspacing="0" width="100%">
			  <tbody><tr>
			    <td class="cellLabel big"> {$CMOD.LBL_SET_PRIV_FOR_EACH_MODULE} </td>
			  </tr>
			</tbody></table>
			<table class="small" align="center" border="0" cellpadding="5" cellspacing="0" width="90%">
			  <tbody><tr>
			    <td class="prvPrfTexture" style="width: 20px;">&nbsp;</td>
			    <td valign="top" width="97%">
				<table class="small listTable" border="0" cellpadding="5" cellspacing="0" width="100%">
			        <tbody>
				<tr id="gva">
			          <td colspan="2" rowspan="2" class="small colHeader"><strong> {$CMOD.LBL_TAB_MESG_OPTION} </strong><strong></strong></td>
			          <td colspan="3" class="small colHeader"><div align="center"><strong>{$CMOD.LBL_EDIT_PERMISSIONS}</strong></div></td>
			          <td rowspan="2" class="small colHeader" nowrap="nowrap">{$CMOD.LBL_FIELDS_AND_TOOLS_SETTINGS}</td>
			        </tr>
			        <tr id="gva">
			          <td class="small colHeader"><div align="center"><strong>
		                {$CMOD.LBL_CREATE_EDIT}
			          </strong></div></td>
			          <td class="small colHeader"> <div align="center"><strong>{$CMOD.LBL_VIEW}</strong></div></td>
			          <td class="small colHeader"> <div align="center"><strong>{$CMOD.LBL_DELETE}</strong></div></td>
			        </tr>
					
				<!-- module loops-->
			        {foreach key=tabid item=elements from=$TAB_PRIV}	
			        <tr>
					{assign var=modulename value=$TAB_PRIV[$tabid][0]}
					{assign var="MODULELABEL" value=$modulename}
					{if $APP[$modulename] neq ''}
						{assign var="MODULELABEL" value=$APP[$modulename]}
					{/if}
			          <td class="small cellLabel" width="3%"><div align="right">
					{$TAB_PRIV[$tabid][1]}
			          </div></td>
			          <td class="small cellLabel" width="40%"><p>{$MODULELABEL}</p></td>
			          <td class="small cellText" width="15%">&nbsp;<div align="center">
					{$STANDARD_PRIV[$tabid][1]}
			          </div></td>
			          <td class="small cellText" width="15%">&nbsp;<div align="center">
					{$STANDARD_PRIV[$tabid][3]}
			          </div></td>
			          <td class="small cellText" width="15%">&nbsp;<div align="center">
					{$STANDARD_PRIV[$tabid][2]}
        			  </div></td>
			          <td class="small cellText" width="22%">&nbsp;<div align="center">
				{if $FIELD_PRIVILEGES[$tabid] neq NULL}
				<img src="{'showDown.gif'|@vtiger_imageurl:$THEME}" id="img_{$tabid}" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" onclick="fnToggleVIew('{$tabid}_view')" border="0" height="16" width="40" style="display:block;">
				{/if}
				</div></td>
				  </tr>
		                  <tr class="hideTable" id="{$tabid}_view" className="hideTable">
				          <td colspan="6" class="small settingsSelectedUI">
						<table class="small" border="0" cellpadding="2" cellspacing="0" width="100%">
			        	    	<tbody>
						{if $FIELD_PRIVILEGES[$tabid] neq ''}
						<tr>
							{if $modulename eq 'Calendar'}
				                	<td class="small colHeader" colspan="6" valign="top">{$CMOD.LBL_FIELDS_TO_BE_SHOWN} ({$APP.Tasks})</td>
							{else}
				                	<td class="small colHeader" colspan="6" valign="top">{$CMOD.LBL_FIELDS_TO_BE_SHOWN}</td>
							{/if}
					        </tr>
						{/if}
						{foreach item=row_values from=$FIELD_PRIVILEGES[$tabid]}
				            	<tr>
						      {foreach item=element from=$row_values}
					              <td valign="top">{$element.1}</td>
					              <td>{$element.0}</td>
						      {/foreach}
				                </tr>
						{/foreach}
						{if $modulename eq 'Calendar'}
						<tr>
				                	<td class="small colHeader" colspan="6" valign="top">{$CMOD.LBL_FIELDS_TO_BE_SHOWN} ({$APP.Events})</td>
					        </tr>
						{foreach item=row_values from=$FIELD_PRIVILEGES[16]}
				            	<tr>
						      {foreach item=element from=$row_values}
					              <td valign="top">{$element.1}</td>
					              <td>{$element.0}</td>
						      {/foreach}
				                </tr>
						{/foreach}
						{/if}
						{if $UTILITIES_PRIV[$tabid] neq ''}
					        <tr>
					              <td colspan="6" class="small colHeader" valign="top">{$CMOD.LBL_TOOLS_TO_BE_SHOWN}</td>
						</tr>
						{/if}
						{foreach item=util_value from=$UTILITIES_PRIV[$tabid]}
							<tr>
							{foreach item=util_elements from=$util_value}
					              		<td valign="top">{$util_elements.1}</td>
						                <td>{$APP[$util_elements.0]}</td>
							{/foreach}
				                	</tr>
						{/foreach}
					        </tbody>
						</table>
					</td>
			          </tr>
				  {/foreach}	
			    	  </tbody>
				  </table>
			  </td>
			  </tr>
                          </tbody>
			</table>
		</td>
                </tr>
		<table border="0" cellpadding="2" cellspacing="0">
			<tr>
				<td align="left"><font color="red" size=5>*</font>{$CMOD.LBL_MANDATORY_MSG}</td>
			</tr>
			<tr>
				<td align="left"><font color="blue" size=5>*</font>{$CMOD.LBL_DISABLE_FIELD_MSG}</td>
			</tr>
		</table>
		<tr>
		<td style="border-top: 2px dotted rgb(204, 204, 204);" align="right">
		<!-- wizard buttons -->
		<table border="0" cellpadding="2" cellspacing="0">
		<tbody>
			<tr><td>
				{if $ACTION eq 'SaveProfile'}
					<input type="submit" value=" {$CMOD.LBL_FINISH_BUTTON} " name="save" class="crmButton create small" title="{$CMOD.LBL_FINISH_BUTTON}"/>&nbsp;&nbsp;
				{else}
					<input type="submit" value=" {$APP.LBL_SAVE_BUTTON_LABEL} " name="save" class="crmButton small save" title="{$APP.LBL_SAVE_BUTTON_LABEL}" />&nbsp;&nbsp;
				{/if}
				</td><td>
					<input type="button" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " name="Cancel" class="crmButton cancel small"onClick="window.history.back();" title="{$APP.LBL_CANCEL_BUTTON_LABEL}" /></td>

				<td>&nbsp;</td>
			</tr>
		</tbody>
		</table>
		</td>
		</tr>
          </tbody>
	  </table>
	</td>
        </tr>
        </tbody>
	</table>
      </td>
      </tr>
      </tbody></table>
      <table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
           <tbody><tr>
                <td><img src="{'prvPrfBottomLeft.gif'|@vtiger_imageurl:$THEME}"></td>
                <td class="prvPrfBottomBg" width="100%"></td>
                <td><img src="{'prvPrfBottomRight.gif'|@vtiger_imageurl:$THEME}"></td>
                </tr>
            </tbody>
      </table></td>
      </tr>
      </tbody></table>
	<p>&nbsp;</p>
	<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tbody><tr><td class="small" align="right" nowrap="nowrap"><a href="#top">{$MOD.LBL_SCROLL}</a></td></tr>
	</tbody></table>
					
	</td>
	</tr>
	</tbody></table>
	</form>	
	<!-- End of Display -->
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	</div>

	</td>
	<td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
	</tr>
</tbody>
</table>
<script language="javascript" type="text/javascript">
{literal}
var Imagid_array = new Array('img_2','img_4','img_6','img_7','img_8','img_9','img_10','img_13','img_14','img_18','img_19','img_20','img_21','img_22','img_23','img_26')
function fnToggleVIew(obj){
	if($(obj).hasClassName('hideTable')) {
		$(obj).removeClassName('hideTable');
	} else {
		$(obj).addClassName('hideTable');
	}
}
function invokeview_all()
{
	if($('view_all_chk').checked == true)
	{
		for(var i = 0;i < document.profileform.elements.length;i++)
		{
			if(document.profileform.elements[i].type == 'checkbox')
			{
				if(document.profileform.elements[i].id.indexOf('tab_chk_com_') != -1 || document.profileform.elements[i].id.indexOf('tab_chk_4') != -1 || document.profileform.elements[i].id.indexOf('_field_') != -1)
					document.profileform.elements[i].checked = true; 
			}
		}	
		showAllImages();
	}
}
function showAllImages()
{
	for(var j=0;j < Imagid_array.length;j++)
	{

		if(typeof($(Imagid_array[j])) != 'undefined')
			$(Imagid_array[j]).style.display = 'block';	
	}
}
function invokeedit_all()
{
	if($('edit_all_chk').checked == true)
	{
		$('view_all_chk').checked = true;
		for(var i = 0;i < document.profileform.elements.length;i++)
		{
			if(document.profileform.elements[i].type == 'checkbox')
			{
				if(document.profileform.elements[i].id.indexOf('tab_chk_com_') != -1 || document.profileform.elements[i].id.indexOf('tab_chk_4') != -1 || document.profileform.elements[i].id.indexOf('tab_chk_1') != -1 || document.profileform.elements[i].id.indexOf('_field_') != -1)
					document.profileform.elements[i].checked = true; 
			}
		}	
		showAllImages();
	}

}
function unselect_edit_all()
{
	$('edit_all_chk').checked = false;
}
function unselect_view_all()
{
	$('view_all_chk').checked = false;
}
function unSelectView(id)
{
	var createid = 'tab_chk_1_'+id;	
	var deleteid = 'tab_chk_2_'+id;
	var tab_id = 'tab_chk_com_'+id;
	if($('tab_chk_4_'+id).checked == false)
	{
		unselect_view_all();
		unselect_edit_all();
		$(createid).checked = false;
		$(deleteid).checked = false;
		$(tab_id).checked = false;
	}else
	{
		var imageid = 'img_'+id;
		var viewid = 'tab_chk_4_'+id;	
		if(typeof($(imageid)) != 'undefined')
			$(imageid).style.display = 'block';
		$('tab_chk_com_'+id).checked = true; 
	}
}
function unSelectCreate(id)
{
	var viewid = 'tab_chk_4_'+id;	
	if($('tab_chk_1_'+id).checked == false)
	{
		unselect_edit_all();
	}else
	{
		var imageid = 'img_'+id;
		var viewid = 'tab_chk_4_'+id;	
		if(typeof($(imageid)) != 'undefined')
			$(imageid).style.display = 'block';
		$('tab_chk_com_'+id).checked = true;
		$(viewid).checked = true;
	}
}
function unSelectDelete(id)
{	
	var contid = id+'_view';
	if($('tab_chk_2_'+id).checked == false)
	{
	}else
	{
		var imageid = 'img_'+id;
		var viewid = 'tab_chk_4_'+id;	
		if(typeof($(imageid)) != 'undefined')
			$(imageid).style.display = 'block';
		$('tab_chk_com_'+id).checked = true;
		$(viewid).checked = true;
	}

}
function hideTab(id)
{
	var createid = 'tab_chk_1_'+id;	
	var viewid = 'tab_chk_4_'+id;	
	var deleteid = 'tab_chk_2_'+id;
	var imageid = 'img_'+id;
	var contid = id+'_view';
	if($('tab_chk_com_'+id).checked == false)
	{
		unselect_view_all();
		unselect_edit_all();
		if(typeof($(imageid)) != 'undefined')
			$(imageid).style.display = 'none';
		$(contid).className = 'hideTable';
		if(typeof($(createid)) != 'undefined')
			$(createid).checked = false;
		if(typeof($(deleteid)) != 'undefined')
			$(deleteid).checked = false;
		if(typeof($(viewid)) != 'undefined')
			$(viewid).checked = false;
	}else
	{
		if(typeof($(imageid)) != 'undefined')
			$(imageid).style.display = 'block';
		if(typeof($(createid)) != 'undefined')
			$(createid).checked = true;
		if(typeof($(deleteid)) != 'undefined')
			$(deleteid).checked = true;
		if(typeof($(viewid)) != 'undefined')
			$(viewid).checked = true;
		var fieldid = id +'_field_';
		for(var i = 0;i < document.profileform.elements.length;i++)
                {
                        if(document.profileform.elements[i].type == 'checkbox' && document.profileform.elements[i].id.indexOf(fieldid) != -1)
                        {
                                        document.profileform.elements[i].checked = true;
                        }
                }
	}
}
function selectUnselect(oCheckbox)
{
	if(oCheckbox.checked == false)
	{
		unselect_view_all();
		unselect_edit_all();
	}
}
function initialiseprofile()
{
	var module_array = Array(1,2,4,6,7,8,9,10,13,14,15,17,18,19,20,21,22,23,24,25,26,27);
	for (var i=0;i < module_array.length;i++)
	{
		hideTab(module_array[i]);
	}	
}
//initialiseprofile();
{/literal}
</script>
