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
<!-- Table to display the mails list -  Starts -->
				<div id="navTemp" style="display:none">
					<span style="float:left">{$ACCOUNT} &gt; {$MAILBOX}
					{if $NUM_EMAILS neq 0}
                                                 {if $NUM_EMAILS neq 1}
                                                        ({$NUM_EMAILS} {$MOD.LBL_MESSAGES})
                                                 {else}
                                                        ({$NUM_EMAILS} {$MOD.LBL_MESSAGE})
                                                 {/if}
                                         {/if}
					</span> <span style="float:right">{$NAVIGATION}</span>	
				</div>
				<span id="{$MAILBOX}_tempcount" style="display:none" >{$UNREAD_COUNT}</span>
				<div id="temp_boxlist" style="display:none">
					<ul style="list-style-type:none;">
					{foreach item=row from=$BOXLIST}
						{foreach item=row_values from=$row}                                                                                                 {$row_values}                                                                                                       {/foreach}                                                                                                          {/foreach}
					</ul>
				</div>
				<div id="temp_movepane" style="display:none">
					<input type="button" name="mass_del" value=" {$MOD.LBL_DELETE} "  class="crmbutton small delete" onclick="mass_delete();"/>
                                        {$FOLDER_SELECT}
				</div>
			<div id="show_msg" class="layerPopup" align="center" style="padding: 5px;font-weight:bold;width: 400px;display:none;z-index:10000"></div>	
                                <form name="massdelete" method="post" onsubmit="VtigerJS_DialogBox.block();">
                                <table cellspacing="1" cellpadding="3" border="0" width="100%" id="message_table">
                                   <tr>
                                <th class="tableHeadBg"><input type="checkbox" name="select_all" value="checkbox"  onclick="toggleSelect(this.checked,'selected_id');"/></th>
                                        {foreach item=element from=$LISTHEADER}
                                                {$element}
                                        {/foreach}
                                   </tr>
                                        {foreach item=row from=$LISTENTITY}
                                                {foreach item=row_values from=$row}
                                                        {$row_values}
                                                {/foreach}
                                        {/foreach}
				</table>
                                </form>
                                <!-- Table to display the mails list - Ends -->
