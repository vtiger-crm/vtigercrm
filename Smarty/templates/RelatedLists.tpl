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
<script language="JavaScript" type="text/javascript" src="modules/PriceBooks/PriceBooks.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/ListView.js"></script>
{literal}
<script>

function editProductListPrice(id,pbid,price)
{
        $("status").style.display="inline";
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'action=ProductsAjax&file=EditListPrice&return_action=CallRelatedList&return_module=PriceBooks&module=Products&record='+id+'&pricebook_id='+pbid+'&listprice='+price,
                        onComplete: function(response) {
                                        $("status").style.display="none";
                                        $("editlistprice").innerHTML= response.responseText;
                        }
                }
        );
}

function gotoUpdateListPrice(id,pbid,proid)
{
        $("status").style.display="inline";
        $("roleLay").style.display = "none";
        var listprice=$("list_price").value;
                new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: 'module=Products&action=ProductsAjax&file=UpdateListPrice&ajax=true&return_action=CallRelatedList&return_module=PriceBooks&record='+id+'&pricebook_id='+pbid+'&product_id='+proid+'&list_price='+listprice,
                                onComplete: function(response) {
                                        $("status").style.display="none";
										$("RLContents").update(response.responseText);
                                }
                        }
                );
}
{/literal}
</script>
	{include file='Buttons_List1.tpl'}
<!-- Contents -->
<div id="editlistprice" style="position:absolute;width:300px;"></div>
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
<tr>
	<td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td class="showPanelBg" valign=top width=100%>
		<!-- PUBLIC CONTENTS STARTS-->
		<div class="small" style="padding:20px">
 	        {* Module Record numbering, used MOD_SEQ_ID instead of ID *}
			 <span class="lvtHeaderText"><font color="purple">[ {$MOD_SEQ_ID} ] </font>{$NAME} -  {$SINGLE_MOD} {$APP.LBL_MORE} {$APP.LBL_INFORMATION}</span> <br>
			 {$UPDATEINFO}
			 </span>&nbsp;&nbsp;<span id="vtbusy_info" style="display:none;" valign="bottom"><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span><span id="vtbusy_info" style="visibility:hidden;" valign="bottom"><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>

			 <hr noshade size=1>
			 <br>

			<!-- Account details tabs -->
			<table border=0 cellspacing=0 cellpadding=0 width=95% align=center>
			<tr>
				<td>
					<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
						<tr>
							{if $OP_MODE eq 'edit_view'}
		                                                {assign var="action" value="EditView"}
                		                        {else}
                                		                {assign var="action" value="DetailView"}
		                                        {/if}
							<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
							{if $MODULE eq 'Calendar'}
                                                	<td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action={$action}&module={$MODULE}&record={$ID}&activity_mode={$ACTIVITY_MODE}&parenttab={$CATEGORY}">{$SINGLE_MOD} {$APP.LBL_INFORMATION}</a></td>
		                                        {else}
                		                        <td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action={$action}&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$SINGLE_MOD} {$APP.LBL_INFORMATION}</a></td>
                                		        {/if}
							<td class="dvtTabCache" style="width:10px">&nbsp;</td>
							<td class="dvtSelectedCell" align=center nowrap>{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</td>
							<td class="dvtTabCache" style="width:100%">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td valign=top align=left >
		                	<table border=0 cellspacing=0 cellpadding=3 width=100% class="dvtContentSpace" style="border-bottom:0;">
						<tr>
							<td align=left>
							<!-- content cache -->
								<table border=0 cellspacing=0 cellpadding=0 width=100%>
									<tr>
										<td style="padding:10px">
										   <!-- General details -->
												{include file='RelatedListsHidden.tpl'}
												<div id="RLContents">
					                                                        {include file='RelatedListContents.tpl'}
                                        						        </div>
												</form>
										  {*-- End of Blocks--*}
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
						<tr>
							{if $OP_MODE eq 'edit_view'}
		                                                {assign var="action" value="EditView"}
                		                        {else}
                                		                {assign var="action" value="DetailView"}
		                                        {/if}
							<td class="dvtTabCacheBottom" style="width:10px" nowrap>&nbsp;</td>
							{if $MODULE eq 'Calendar'}
                                                	<td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action={$action}&module={$MODULE}&record={$ID}&activity_mode={$ACTIVITY_MODE}&parenttab={$CATEGORY}">{$SINGLE_MOD} {$APP.LBL_INFORMATION}</a></td>
		                                        {else}
                		                        <td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action={$action}&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$SINGLE_MOD} {$APP.LBL_INFORMATION}</a></td>
                                		        {/if}
							<td class="dvtTabCacheBottom" style="width:10px">&nbsp;</td>
							<td class="dvtSelectedCellBottom" align=center nowrap>{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</td>
							<td class="dvtTabCacheBottom" style="width:100%">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			</table>
		</div>
	<!-- PUBLIC CONTENTS STOPS-->
	</td>
	<td align=right valign=top><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
</tr>
</table>

{if $MODULE eq 'Leads' or $MODULE eq 'Contacts' or $MODULE eq 'Accounts' or $MODULE eq 'Campaigns' or $MODULE eq 'Vendors'}
<form name="SendMail" onsubmit="VtigerJS_DialogBox.block();"><div id="sendmail_cont" style="z-index:100001;position:absolute;width:300px;"></div></form>
{/if}

<script>
function OpenWindow(url)
{ldelim}
	openPopUp('xAttachFile',this,url,'attachfileWin',380,375,'menubar=no,toolbar=no,location=no,status=no,resizable=no');
{rdelim}
</script>
