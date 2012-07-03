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
<script language="JavaScript" type="text/javascript" src="include/js/dtlviewajax.js"></script>
<script src="include/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<div id="convertleaddiv" style="display:block;position:absolute;left:225px;top:150px;"></div>
<span id="crmspanid" style="display:none;position:absolute;"  onmouseover="show('crmspanid');">
   <a class="link"  align="right" href="javascript:;">{$APP.LBL_EDIT_BUTTON}</a>
</span>
<script>
function tagvalidate()
{ldelim}
	if(trim(document.getElementById('txtbox_tagfields').value) != '')
		SaveTag('txtbox_tagfields','{$ID}','{$MODULE}');	
	else
	{ldelim}
		alert("{$APP.PLEASE_ENTER_TAG}");
		return false;
	{rdelim}
{rdelim}
function DeleteTag(id,recordid)
{ldelim}
	$("vtbusy_info").style.display="inline";
	Effect.Fade('tag_'+id);
	new Ajax.Request(
		'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                        method: 'post',
                        postBody: "file=TagCloud&module={$MODULE}&action={$MODULE}Ajax&ajxaction=DELETETAG&recordid="+recordid+"&tagid=" +id,
                        onComplete: function(response) {ldelim}
						getTagCloud();
						$("vtbusy_info").style.display="none";
                        {rdelim}
                {rdelim}
        );
{rdelim}
{literal}
function showHideStatus(sId,anchorImgId,sImagePath)
{
	oObj = eval(document.getElementById(sId));
	if(oObj.style.display == 'block')
	{
		oObj.style.display = 'none';
		eval(document.getElementById(anchorImgId)).src =  'themes/images/inactivate.gif';
		eval(document.getElementById(anchorImgId)).alt = 'Display';
		eval(document.getElementById(anchorImgId)).title = 'Display';
	}
	else
	{
		oObj.style.display = 'block';
		eval(document.getElementById(anchorImgId)).src =  'themes/images/activate.gif';
		eval(document.getElementById(anchorImgId)).alt = 'Hide';
		eval(document.getElementById(anchorImgId)).title = 'Hide';
	}
}
function setCoOrdinate(elemId)
{
	oBtnObj = document.getElementById(elemId);
	var tagName = document.getElementById('lstRecordLayout');
	leftpos  = 0;
	toppos = 0;
	aTag = oBtnObj;
	do 
	{					  
	  leftpos  += aTag.offsetLeft;
	  toppos += aTag.offsetTop;
	} while(aTag = aTag.offsetParent);
	
	tagName.style.top= toppos + 20 + 'px';
	tagName.style.left= leftpos - 276 + 'px';
}

function getListOfRecords(obj, sModule, iId,sParentTab)
{
		new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Users&action=getListOfRecords&ajax=true&CurModule='+sModule+'&CurRecordId='+iId+'&CurParentTab='+sParentTab,
			onComplete: function(response) {
				sResponse = response.responseText;
				$("lstRecordLayout").innerHTML = sResponse;
				Lay = 'lstRecordLayout';	
				var tagName = document.getElementById(Lay);
				var leftSide = findPosX(obj);
				var topSide = findPosY(obj);
				var maxW = tagName.style.width;
				var widthM = maxW.substring(0,maxW.length-2);
				var getVal = eval(leftSide) + eval(widthM);
				if(getVal  > document.body.clientWidth ){
					leftSide = eval(leftSide) - eval(widthM);
					tagName.style.left = leftSide + 230 + 'px';
				}
				else
					tagName.style.left= leftSide + 388 + 'px';
				
				setCoOrdinate(obj.id);
				
				tagName.style.display = 'block';
				tagName.style.visibility = "visible";
			}
		}
	);
}
{/literal}

</script>

<div id="lstRecordLayout" class="layerPopup" style="display:none;width:325px;height:300px;"></div> <!-- Code added by SAKTI on 16th Jun, 2008 -->

<table width="100%" cellpadding="2" cellspacing="0" border="0">
   <tr>
	<td>
		{include file='Buttons_List1.tpl'}

		<!-- Contents -->
		<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
		   <tr>
			<td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
			<td class="showPanelBg" valign=top width=100%>
			<!-- PUBLIC CONTENTS STARTS-->
			   <div class="small" style="padding:20px" >
		
				<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
				   <tr>
					<td>
			         {* Module Record numbering, used MOD_SEQ_ID instead of ID *}
			         {assign var="USE_ID_VALUE" value=$MOD_SEQ_ID}
		  			 {if $USE_ID_VALUE eq ''} {assign var="USE_ID_VALUE" value=$ID} {/if}
		  			
						<span class="lvtHeaderText"><font color="purple">[ {$USE_ID_VALUE} ] </font>{$NAME} -  {$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</span>&nbsp;&nbsp;&nbsp;<span class="small">{$UPDATEINFO}</span>&nbsp;<span id="vtbusy_info" style="display:none;" valign="bottom"><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span><span id="vtbusy_info" style="visibility:hidden;" valign="bottom"><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
					</td>
				   </tr>
				</table>
				<br>
						
				<!-- Entity and More information tabs -->
				<table border=0 cellspacing=0 cellpadding=0 width=95% align=center>
				   <tr>
					<td>						
   						<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
						   <tr>
								<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
								
								<td class="dvtSelectedCell" align=center nowrap>{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</td>	
								<td class="dvtTabCache" style="width:10px">&nbsp;</td>
								{if $SinglePane_View eq 'false' && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0}
									<td class="dvtUnSelectedCell" onmouseout="fnHideDrop('More_Information_Modules_List');" onmouseover="fnDropDown(this,'More_Information_Modules_List');" align="center" nowrap>
										<a href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</a>
										<div onmouseover="fnShowDrop('More_Information_Modules_List')" onmouseout="fnHideDrop('More_Information_Modules_List')"
													 id="More_Information_Modules_List" class="drop_mnu" style="left: 502px; top: 76px; display: none;">
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
											{foreach key=_RELATION_ID item=_RELATED_MODULE from=$IS_REL_LIST}
												<tr><td><a class="drop_down" href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}&selected_header={$_RELATED_MODULE}&relation_id={$_RELATION_ID}">{$_RELATED_MODULE|@getTranslatedString:$MODULE}</a></td></tr>
											{/foreach}
											</table>
										</div>
									</td>
								{/if}
								<td class="dvtTabCache" align="right" style="width:100%">
									{if $EDIT_DUPLICATE eq 'permitted'}
									<input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="crmbutton small edit" onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='DetailView'; DetailView.return_id.value='{$ID}';DetailView.module.value='{$MODULE}'; submitFormForAction('DetailView','EditView');" type="button" name="Edit" value="&nbsp;{$APP.LBL_EDIT_BUTTON_LABEL}&nbsp;">&nbsp;
									{/if}
									{if $EDIT_DUPLICATE eq 'permitted' && $MODULE neq 'Documents'}
									<input title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}" accessKey="{$APP.LBL_DUPLICATE_BUTTON_KEY}" class="crmbutton small create" onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='DetailView'; DetailView.isDuplicate.value='true';DetailView.module.value='{$MODULE}'; submitFormForAction('DetailView','EditView');" type="button" name="Duplicate" value="{$APP.LBL_DUPLICATE_BUTTON_LABEL}">&nbsp;
									{/if}
									{if $DELETE eq 'permitted'}
									<input title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" class="crmbutton small delete" onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='index'; {if $MODULE eq 'Accounts'} var confirmMsg = '{$APP.NTC_ACCOUNT_DELETE_CONFIRMATION}' {else} var confirmMsg = '{$APP.NTC_DELETE_CONFIRMATION}' {/if}; submitFormForActionWithConfirmation('DetailView', 'Delete', confirmMsg);" type="button" name="Delete" value="{$APP.LBL_DELETE_BUTTON_LABEL}">&nbsp;
									{/if}
								
									{if $privrecord neq ''}
									<img align="absmiddle" title="{$APP.LNK_LIST_PREVIOUS}" accessKey="{$APP.LNK_LIST_PREVIOUS}" onclick="location.href='index.php?module={$MODULE}&viewtype={$VIEWTYPE}&action=DetailView&record={$privrecord}&parenttab={$CATEGORY}'" name="privrecord" value="{$APP.LNK_LIST_PREVIOUS}" src="{'rec_prev.gif'|@vtiger_imageurl:$THEME}">&nbsp;
									{else}
									<img align="absmiddle" title="{$APP.LNK_LIST_PREVIOUS}" src="{'rec_prev_disabled.gif'|@vtiger_imageurl:$THEME}">
									{/if}							
									{if $privrecord neq '' || $nextrecord neq ''}
									<img align="absmiddle" title="{$APP.LBL_JUMP_BTN}" accessKey="{$APP.LBL_JUMP_BTN}" onclick="var obj = this;var lhref = getListOfRecords(obj, '{$MODULE}',{$ID},'{$CATEGORY}');" name="jumpBtnIdTop" id="jumpBtnIdTop" src="{'rec_jump.gif'|@vtiger_imageurl:$THEME}">&nbsp;
									{/if}
									{if $nextrecord neq ''}
									<img align="absmiddle" title="{$APP.LNK_LIST_NEXT}" accessKey="{$APP.LNK_LIST_NEXT}" onclick="location.href='index.php?module={$MODULE}&viewtype={$VIEWTYPE}&action=DetailView&record={$nextrecord}&parenttab={$CATEGORY}'" name="nextrecord" src="{'rec_next.gif'|@vtiger_imageurl:$THEME}">&nbsp;
									{else}
									<img align="absmiddle" title="{$APP.LNK_LIST_NEXT}" src="{'rec_next_disabled.gif'|@vtiger_imageurl:$THEME}">&nbsp;
									{/if}
								</td>
							</tr>
						</table>
					</td>
				   </tr>
				   <tr>
					<td valign=top align=left >
						<table border=0 cellspacing=0 cellpadding=3 width=100% class="dvtContentSpace" style="border-bottom:0px;">
						   <tr valign=top>

							<td align=left style="padding:10px;">
							<!-- content cache -->
								<form action="index.php" method="post" name="DetailView" id="form" onsubmit="VtigerJS_DialogBox.block();">
								{include file='DetailViewHidden.tpl'}
						
								<!-- Entity informations display - starts -->	
								<table border=0 cellspacing=0 cellpadding=0 width=100%>
			                			   <tr>
									<td style="padding:10px;border-right:1px dashed #CCCCCC;" width="80%">



<!-- The following table is used to display the buttons -->
<!-- Button displayed - finished-->
							 {include_php file="include/DetailViewBlockStatus.php"}

<!-- Entity information(blocks) display - start -->
{foreach key=header item=detail from=$BLOCKS}
	<table border=0 cellspacing=0 cellpadding=0 width=100% class="small">
	   <tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align=right>
		</td>
	   </tr>
	   <tr>
		{strip}
		<td colspan=4 class="dvInnerHeader" >
							
							<div style="float:left;font-weight:bold;"><div style="float:left;"><a href="javascript:showHideStatus('tbl{$header|replace:' ':''}','aid{$header|replace:' ':''}','{$IMAGE_PATH}');">
							{if $BLOCKINITIALSTATUS[$header] eq 1}
								<img id="aid{$header|replace:' ':''}" src="{'activate.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid #000000;" alt="Hide" title="Hide"/>
							{else}
								<img id="aid{$header|replace:' ':''}" src="{'inactivate.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid #000000;" alt="Display" title="Display"/>
							{/if}
								</a></div><b>&nbsp;
						        	{$header}
	  			     			</b></div>
		</td>
		{/strip}
	   </tr>
							</table>
							{if $BLOCKINITIALSTATUS[$header] eq 1}
							<div style="width:auto;display:block;" id="tbl{$header|replace:' ':''}" >
							{else}
							<div style="width:auto;display:none;" id="tbl{$header|replace:' ':''}" >
							{/if}
							<table border=0 cellspacing=0 cellpadding=0 width="100%" class="small">

	   {foreach item=detail from=$detail}
	   <tr style="height:25px">
		{foreach key=label item=data from=$detail}
			{assign var=keyid value=$data.ui}
			{assign var=keyval value=$data.value}
			{assign var=keytblname value=$data.tablename}
			{assign var=keyfldname value=$data.fldname}
			{assign var=keyfldid value=$data.fldid}
			{assign var=keyoptions value=$data.options}
			{assign var=keysecid value=$data.secid}
			{assign var=keyseclink value=$data.link}
			{assign var=keycursymb value=$data.cursymb}
			{assign var=keysalut value=$data.salut}
			{assign var=keycntimage value=$data.cntimage}
			{assign var=keyadmin value=$data.isadmin}
			{assign var=display_type value=$data.displaytype}
			{assign var=_readonly value=$data.readonly}

				{if $label ne ''}
					{if $keycntimage ne ''}
						<td class="dvtCellLabel" align=right width=25%><input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input>{$keycntimage}</td>
					{elseif $label neq 'Tax Class'}<!-- Avoid to display the label Tax Class -->
						{if $keyid eq '71' || $keyid eq '72'}  <!--CurrencySymbol-->
							<td class="dvtCellLabel" align=right width=25%><input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input>{$label} ({$keycursymb})</td>
						{elseif $keyid eq '9'}
							<td class="dvtCellLabel" align=right width=25%><input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input>{$label} {$APP.COVERED_PERCENTAGE}</td>
						{else}
							<td class="dvtCellLabel" align=right width=25%><input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input>{$label}</td>
						{/if}
					{/if}  
					{if $EDIT_PERMISSION eq 'yes' && $display_type neq '2' && $_readonly eq '0'}
						{* Performance Optimization Control *}
						{if !empty($DETAILVIEW_AJAX_EDIT) }
							{include file="DetailViewUI.tpl"}
						{else}
							{include file="DetailViewFields.tpl"}
						{/if}
						{* END *}
					{else}
						{include file="DetailViewFields.tpl"}
					{/if}
				{/if}
		{/foreach}
	   </tr>	
	   {/foreach}	
	</table>
							 </div> <!-- Line added by SAKTI on 10th Apr, 2008 -->
{/foreach}
{*-- End of Blocks--*} 
<!-- Entity information(blocks) display - ends -->

{* vtlib Customization: Embed DetailViewWidget block:// type if any *}
{if $CUSTOM_LINKS && !empty($CUSTOM_LINKS.DETAILVIEWWIDGET)}
{foreach item=CUSTOM_LINK_DETAILVIEWWIDGET from=$CUSTOM_LINKS.DETAILVIEWWIDGET}
	{if preg_match("/^block:\/\/.*/", $CUSTOM_LINK_DETAILVIEWWIDGET->linkurl)}
		<br>
		{php}
			echo vtlib_process_widget($this->_tpl_vars['CUSTOM_LINK_DETAILVIEWWIDGET'], $this->_tpl_vars);
		{/php}
	{/if}
{/foreach}
{/if}
{* END *}
									<br>

										<!-- Product Details informations -->
										{$ASSOCIATED_PRODUCTS}
										</form>
									</td>
<!-- The following table is used to display the buttons -->
								<table border=0 cellspacing=0 cellpadding=0 width=100%>
			                			   <tr>
									<td style="padding:10px;border-right:1px dashed #CCCCCC;" width="80%">

		<table border=0 cellspacing=0 cellpadding=0 width=100%>
		  <tr>
			<td style="border-right:1px dashed #CCCCCC;" width="100%">
			{if $SinglePane_View eq 'true'}
				{include file= 'RelatedListNew.tpl'}
			{/if}
		</td></tr></table>
</td></tr></table>
									<!-- Inventory Actions - ends -->	
									<td width=22% valign=top style="padding:10px;">
										<!-- right side InventoryActions -->
										{include file="Inventory/InventoryActions.tpl"}

										<br>
										<!-- To display the Tag Clouds -->
										<div>
										      {include file="TagCloudDisplay.tpl"}
										</div>
									</td>
								   </tr>
								   
								</table>
							</td>
						   </tr>
						    <tr>
					<td>						
   						<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
						   <tr>
								<td class="dvtTabCacheBottom" style="width:10px" nowrap>&nbsp;</td>
								
								<td class="dvtSelectedCellBottom" align=center nowrap>{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</td>	
								<td class="dvtTabCacheBottom" style="width:10px">&nbsp;</td>
								{if $SinglePane_View eq 'false' && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0}
								<td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</a></td>
								{/if}
								<td class="dvtTabCacheBottom" align="right" style="width:100%">
									{if $EDIT_DUPLICATE eq 'permitted'}
									<input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="crmbutton small edit" onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='DetailView'; DetailView.return_id.value='{$ID}';DetailView.module.value='{$MODULE}'; submitFormForAction('DetailView','EditView');" type="button" name="Edit" value="&nbsp;{$APP.LBL_EDIT_BUTTON_LABEL}&nbsp;">&nbsp;
									{/if}
									{if $EDIT_DUPLICATE eq 'permitted' && $MODULE neq 'Documents'}
									<input title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}" accessKey="{$APP.LBL_DUPLICATE_BUTTON_KEY}" class="crmbutton small create" onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='DetailView'; DetailView.isDuplicate.value='true';DetailView.module.value='{$MODULE}'; submitFormForAction('DetailView','EditView');" type="button" name="Duplicate" value="{$APP.LBL_DUPLICATE_BUTTON_LABEL}">&nbsp;
									{/if}
									{if $DELETE eq 'permitted'}
									<input title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" class="crmbutton small delete" onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='index'; {if $MODULE eq 'Accounts'} var confirmMsg = '{$APP.NTC_ACCOUNT_DELETE_CONFIRMATION}' {else} var confirmMsg = '{$APP.NTC_DELETE_CONFIRMATION}' {/if}; submitFormForActionWithConfirmation('DetailView', 'Delete', confirmMsg);" type="button" name="Delete" value="{$APP.LBL_DELETE_BUTTON_LABEL}">&nbsp;
									{/if}
								
									{if $privrecord neq ''}
									<img align="absmiddle" title="{$APP.LNK_LIST_PREVIOUS}" accessKey="{$APP.LNK_LIST_PREVIOUS}" onclick="location.href='index.php?module={$MODULE}&viewtype={$VIEWTYPE}&action=DetailView&record={$privrecord}&parenttab={$CATEGORY}'" name="privrecord" value="{$APP.LNK_LIST_PREVIOUS}" src="{'rec_prev.gif'|@vtiger_imageurl:$THEME}">&nbsp;
									{else}
									<img align="absmiddle" title="{$APP.LNK_LIST_PREVIOUS}" src="{'rec_prev_disabled.gif'|@vtiger_imageurl:$THEME}">
									{/if}							
									{if $privrecord neq '' || $nextrecord neq ''}
									<img align="absmiddle" title="{$APP.LBL_JUMP_BTN}" accessKey="{$APP.LBL_JUMP_BTN}" onclick="var obj = this;var lhref = getListOfRecords(obj, '{$MODULE}',{$ID},'{$CATEGORY}');" name="jumpBtnIdBottom" id="jumpBtnIdBottom" src="{'rec_jump.gif'|@vtiger_imageurl:$THEME}">&nbsp;
									{/if}
									{if $nextrecord neq ''}
									<img align="absmiddle" title="{$APP.LNK_LIST_NEXT}" accessKey="{$APP.LNK_LIST_NEXT}" onclick="location.href='index.php?module={$MODULE}&viewtype={$VIEWTYPE}&action=DetailView&record={$nextrecord}&parenttab={$CATEGORY}'" name="nextrecord" src="{'rec_next.gif'|@vtiger_imageurl:$THEME}">&nbsp;
									{else}
									<img align="absmiddle" title="{$APP.LNK_LIST_NEXT}" src="{'rec_next_disabled.gif'|@vtiger_imageurl:$THEME}">&nbsp;
									{/if}
								</td>
							</tr>
						</table>
					</td>
				   </tr>
						</table>
					<!-- PUBLIC CONTENTS STOPS-->
					</td>
					<td align=right valign=top>
						<img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}">
					</td>
				   </tr>
				</table>
			   </div>
			</td>
		   </tr>
		</table>
		<!-- Contents - end -->

<script>
function getTagCloud()
{ldelim}
	var obj = $("tagfields");
	if(obj != null && typeof(obj) != undefined) {ldelim}
		new Ajax.Request(
        	'index.php',
        	{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
      		method: 'post',
			postBody: 'module={$MODULE}&action={$MODULE}Ajax&file=TagCloud&ajxaction=GETTAGCLOUD&recordid={$ID}',
			onComplete: function(response) {ldelim}
							$("tagfields").innerHTML=response.responseText;
                            $("txtbox_tagfields").value ='';
                        {rdelim}
        	{rdelim}
		);
	{rdelim}
{rdelim}
getTagCloud();
</script>

	</td>
   </tr>
</table>
<script language="javascript">
  var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
  var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
  var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
</script>

