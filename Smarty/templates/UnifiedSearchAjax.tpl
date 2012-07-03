{*<!--
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

-->*}
{if $MODULE eq $SEARCH_MODULE && $SEARCH_MODULE neq ''}
	<div id="global_list_{$SEARCH_MODULE}" style="display:block">
{elseif $MODULE eq 'Contacts' && $SEARCH_MODULE eq ''}
	<div id="global_list_{$MODULE}" style="display:block">
{elseif $SEARCH_MODULE neq ''}
	<div id="global_list_{$MODULE}" style="display:none">
{else}
	<div id="global_list_{$MODULE}" style="display:block">
{/if}
<form name="massdelete" method="POST">
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
     
     <input name="idlist" type="hidden">
     <input name="change_owner" type="hidden">
     <input name="change_status" type="hidden">
	 <input name="search_tag" type="hidden" value="{$TAG_SEARCH}" >
     <input name="search_criteria" type="hidden" value="{$SEARCH_STRING}">
     <input name="module" type="hidden" value="{$MODULE}" />
     <input name="{$MODULE}RecordCount" id="{$MODULE}RecordCount" type="hidden" value="{$ModuleRecordCount.$MODULE.count}" />
     <tr>
		<td>
	   <!-- PUBLIC CONTENTS STARTS-->
	   <br>
		<div class="small" style="padding:2px">
			<table border=0 cellspacing=1 cellpadding=0 width=100% class="lvtBg">
	           <tr >
					<td>
						<table border=0 cellspacing=0 cellpadding=2 width=100% class="small">
				   			<tr>
							{assign var="MODULELABEL" value=$MODULE}
							{if $APP.$MODULE neq ''}
								{assign var="MODULELABEL" value=$APP.$MODULE}
							{/if}
								<td style="padding-right:20px" nowrap ><b class=big>{$MODULELABEL}</b>{$SEARCH_CRITERIA}</td>					
								<td style="padding-right:20px" class="small" align="right" nowrap>{$ModuleRecordCount.$MODULE.recordListRangeMessage}</td>
								<td nowrap width="50%">
									<table border=0 cellspacing=0 cellpadding=0 class="small">
									   <tr>{$NAVIGATION}</tr>
									</table>
								</td>					
				  			 </tr>
						</table>
                 		<div  class="searchResults">
			 				<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
				   				<tr>
								{if $DISPLAYHEADER eq 1}
									{foreach item=header from=$LISTHEADER}
									<td class="mailSubHeader">{$header}</td>
									{/foreach}
								{else}
									<td class="searchResultsRow" colspan=$HEADERCOUNT> {$APP.LBL_NO_DATA} </td>
								{/if}
				   				</tr>
							   {foreach item=entity key=entity_id from=$LISTENTITY}
							   <tr bgcolor=white onMouseOver="this.className='lvtColDataHover'" onMouseOut="this.className='lvtColData'"  >
								{foreach item=data from=$entity}	
									<td>{$data}</td>
								{/foreach}
							   </tr>
							   {/foreach}
							</table>
			 			</div>
					</td>
		   		</tr>
			</table>
	   </div>	   
	</td>
   </tr>
</table>
</form>	
</div>
{if $SEARCH_MODULE eq 'All'}
<script>
displayModuleList(document.getElementById('global_search_module'));
</script>
{/if}
