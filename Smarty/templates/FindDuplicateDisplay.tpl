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
<script language="JavaScript" type="text/javascript" src="include/js/ListView.js"></script>
{* If we duplicate merge is within same module show the headers ... *}
{if $MODULE eq $smarty.request.module}

{include file='Buttons_List.tpl'}
				<div id="searchingUI" style="display:none;">
    				<table border=0 cellspacing=0 cellpadding=0 width=100%>
    					<tr>
    						<td align=center>
        						<img src="{'searching.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SEARCHING}"  title="{$APP.LBL_SEARCHING}">
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

{/if}

{*<!-- Contents -->*}

{if $MODULE eq $smarty.request.module}
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
     <tr>
        <td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>

		<td class="showPanelBg" valign="top" width=100% style="padding:10px;">
{/if}
			
			{* Common Output: Within module Duplicate Search or Post Import Duplicate Search *}		
			<div id="duplicate_ajax" style='margin: 0 10px;'>
				{include file='FindDuplicateAjax.tpl'}
			</div>
			<div id="current_action" style="display:none">{$smarty.request.action|@vtlib_purify}</div>
			{* END *}
			
{if $MODULE eq $smarty.request.module}
     	</td>
        <td valign=top><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</table>
{/if}