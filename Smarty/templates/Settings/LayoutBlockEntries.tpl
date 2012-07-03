{*
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/ *}
			<form action="index.php" method="post" name="form" onsubmit="VtigerJS_DialogBox.block();">
				<input type="hidden" name="fld_module" value="{$MODULE}">
				<input type="hidden" name="module" value="Settings">
				<input type="hidden" name="parenttab" value="Settings">
				<input type="hidden" name="mode">
				<script language="JavaScript" type="text/javascript" src="include/js/customview.js"></script>
				<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>
						
				<table class="listTable" border="0" cellpadding="3" cellspacing="0" width="100%">
					
					{foreach item=entries key=id from=$CFENTRIES name=outer}
						{if $entries.blockid ne $RELPRODUCTSECTIONID || $entries.blocklabel neq '' }
							{if $smarty.foreach.outer.first neq true}
							<tr><td><img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;</td></tr>
							{/if}
							<tr>
								<td  class="colHeader small" colspan="2">
								<select name="display_status_{$entries.blockid}" style="border:1px solid #666666;font-family:Arial, Helvetica, sans-serif;font-size:11px; width:auto" onChange="changeShowstatus('{$entries.tabid}','{$entries.blockid}','{$MODULE}')" id='display_status_{$entries.blockid}'>
			                		    <option value="show" {if $entries.display_status==1}selected{/if}>{$MOD.LBL_Show}</option>
										<option value="hide" {if $entries.display_status==0}selected{/if}>{$MOD.LBL_Hide}</option>			                
								</select>
								&nbsp;&nbsp;{$entries.blocklabel}&nbsp;&nbsp;
				  				</td>
								<td class="colHeader small"  id = "blockid"_{$entries.blockid} colspan="2" align='right'> 
									
									{if $entries.iscustom == 1 }
									<img style="cursor:pointer;" onClick=" deleteCustomBlock('{$MODULE}','{$entries.blockid}','{$entries.no}')" src="{'delete.gif'|@vtiger_imageurl:$THEME}" border="0"  alt="Delete" title="Delete"/>&nbsp;&nbsp;
									{/if}
									{if $entries.blockid neq $COMMENTSECTIONID && $entries.blockid neq $SOLUTIONBLOCKID}
									<img src="{'hidden_fields.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;"  onclick="fnvshobj(this,'hiddenfields_{$entries.blockid}');" alt="{$MOD.HIDDEN_FIELDS}" title="{$MOD.HIDDEN_FIELDS}"/>&nbsp;&nbsp;
									{/if}	
										<div id = "hiddenfields_{$entries.blockid}" style="display:none; position:absolute; width:300px;" class="layerPopup">
											<div style="position:relative; display:block">
		 										<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
													<tr>
														<td width="95%" align="left"  class="layerPopupHeading">
															{$MOD.HIDDEN_FIELDS}
														</td>
														<td width="5%" align="right">
															<a href="javascript:fninvsh('hiddenfields_{$entries.blockid}');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"  align="absmiddle" /></a>
														</td>
													</tr>
												</table>
												<table border="0" cellspacing="0" cellpadding="0" width="95%">
													<tr>
														<td class=small >
															<table border="0" celspacing="0" cellpadding="0" width="100%" align="center" bgcolor="white">
																<tr>
																	<td align="center">	
																		<table border="0" cellspacing="0" cellpadding="0" width="100%">
																			<tr>
																				<td>{if $entries.hidden_count neq '0' || $entries.hidden_count neq null}
																					{$APP.LBL_SELECT_FIELD_TO_MOVE}
																					{/if} 
																				</td>
																			</tr>
																			<tr align="left">
																				<td>{if $entries.hidden_count neq '0'}
																					<select class="small" id="hiddenfield_assignid_{$entries.blockid}" style="width:225px" size="10" multiple>
																					{foreach name=inner item=value from=$entries.hiddenfield}	
																						<option value="{$value.fieldselect}">{$value.fieldlabel|@getTranslatedString:$MODULE}</option>
																					{/foreach}
																						</select>
																					{else}
																					{$MOD.NO_HIDDEN_FIELDS}
																					{/if}
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
												<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
													<tr>
														<td align="center">
															<input type="button" name="save" value="{$APP.LBL_UNHIDE_FIELDS}" class="crmButton small save" onclick ="show_move_hiddenfields('{$MODULE}','{$entries.tabid}','{$entries.blockid}','showhiddenfields');"/>
															<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="fninvsh('hiddenfields_{$entries.blockid}');" />
														</td>
													</tr>
												</table>
											</div>
										</div>						
																						
									{if $entries.hascustomtable && $entries.blockid neq $COMMENTSECTIONID && $entries.blockid neq $SOLUTIONBLOCKID }
										<img src="{'plus_layout.gif'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;"  onclick="fnvshobj(this,'addfield_{$entries.blockid}'); " alt="{$MOD.LBL_ADD_CUSTOMFIELD}" title="{$MOD.LBL_ADD_CUSTOMFIELD}"/>&nbsp;&nbsp;
									{/if}
											<!-- for adding customfield -->
												<div id="addfield_{$entries.blockid}" style="display:none; position:absolute; width:500px;" class="layerPopup">
												  	<input type="hidden" name="mode" id="cfedit_mode" value="add">
	  												<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
														<tr>
															<td width="60%" align="left" class="layerPopupHeading">{$MOD.LBL_ADD_FIELD}
															</td>
															<td width="40%" align="right"><a href="javascript:fninvsh('addfield_{$entries.blockid}');">
															<img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"  align="absmiddle" /></a>
															</td>
														</tr>
													</table>
													<table border="0" cellspacing="0" cellpadding="5" width="95%" align="center"> 
														<tr>
															<td class="small" >
																<table border="0" celspacing="0" cellpadding="5" width="100%" align="center" bgcolor="white">
																	<tr>
																		<td>
																			<table>
																				<tr>
																					<td>{$APP.LBL_SELECT_FIELD_TYPE}
																					</td>
																				</tr>
																				<tr>
																					<td>
																						<div name="cfcombo" id="cfcombo" class="small"  style="width:205px; height:150px; overflow-y:auto ;overflow-x:hidden ;overflow:auto; border:1px  solid #CCCCCC ;">
																							<table>
																								<tr><td align="left"><a id="field0_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'text.gif'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,0,{$entries.blockid});">  {$MOD.Text} </a></td></tr>
																								<tr><td align="left"><a id="field1_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'number.gif'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,1,{$entries.blockid})" >  {$MOD.Number} </a></td></tr>
																								<tr><td align="left"><a id="field2_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'percent.gif'|@vtiger_imageurl:$THEME});" 	onclick = "makeFieldSelected(this,2,{$entries.blockid});">  {$MOD.Percent} </a></td></tr>
																								<tr><td align="left"><a id="field3_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'cfcurrency.gif'|@vtiger_imageurl:$THEME});" 	onclick = "makeFieldSelected(this,3,{$entries.blockid});">  {$MOD.Currency} </a></td></tr>
																								<tr><td align="left"><a id="field4_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'date.gif'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,4,{$entries.blockid});">  {$MOD.Date} </a></td></tr>
																								<tr><td align="left"><a id="field5_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'email.gif'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,5,{$entries.blockid});">  {$MOD.Email} </a></td></tr>
																								<tr><td align="left"><a id="field6_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'phone.gif'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,6,{$entries.blockid});">  {$MOD.Phone} </a>	</td></tr>
																								<tr><td align="left"><a id="field7_{$entries.blockid}" 	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'cfpicklist.gif'|@vtiger_imageurl:$THEME});" 	onclick = "makeFieldSelected(this,7,{$entries.blockid});">  {$MOD.PickList} </a></td></tr>
																								<tr><td align="left"><a id="field8_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'url.gif'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,8,{$entries.blockid});">  {$MOD.LBL_URL} </a></td></tr>
																								<tr><td align="left"><a id="field9_{$entries.blockid}" 	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'checkbox.gif'|@vtiger_imageurl:$THEME});" 	onclick = "makeFieldSelected(this,9,{$entries.blockid});">  {$MOD.LBL_CHECK_BOX} </a></td></tr>
																								<tr><td align="left"><a id="field10_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'text.gif'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,10,{$entries.blockid});"> {$MOD.LBL_TEXT_AREA} </a></td></tr>
																								<tr><td align="left"><a id="field11_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'cfpicklist.gif'|@vtiger_imageurl:$THEME});" 	onclick = "makeFieldSelected(this,11,{$entries.blockid});"> {$MOD.LBL_MULTISELECT_COMBO} </a></td></tr>
																								<tr><td align="left"><a id="field12_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'skype.gif'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,12,{$entries.blockid});"> {$MOD.Skype} </a></td></tr>
                                                                                                <tr><td align="left"><a id="field13_{$entries.blockid}"	href="javascript:void(0);" class="customMnu" style="text-decoration:none; background-image:url({'time.PNG'|@vtiger_imageurl:$THEME});" 		onclick = "makeFieldSelected(this,13,{$entries.blockid});"> {$MOD.Time} </a></td></tr>
																							</table>
																						</div>	
																					</td>
																				</tr>
																			</table>
																		</td>				
																		<td width="50%">
																			<table width="100%" border="0" cellpadding="5" cellspacing="0">
																				<tr>
																					<td class="dataLabel" nowrap="nowrap" align="right" width="30%"><b>{$MOD.LBL_LABEL} </b>
																					</td>
																					<td align="left" width="70%">
																					<input id="fldLabel_{$entries.blockid}"  value="" type="text" class="txtBox">
																					</td>
																				</tr>
																				<tr id="lengthdetails_{$entries.blockid}">
																					<td class="dataLabel" nowrap="nowrap" align="right"><b>{$MOD.LBL_LENGTH}</b>
																					</td>
																					<td align="left">
																					<input type="text" id="fldLength_{$entries.blockid}" value="" class="txtBox">
																					</td>
																				</tr>
																				<tr id="decimaldetails_{$entries.blockid}" style="visibility:hidden;">
																					<td class="dataLabel_{$entries.blockid}" nowrap="nowrap" align="right"><b>{$MOD.LBL_DECIMAL_PLACES}</b>
																					</td>
																					<td align="left">
																					<input type="text" id="fldDecimal_{$entries.blockid}" value=""  class="txtBox">
																					</td>
																				</tr>
																				<tr id="picklistdetails_{$entries.blockid}" style="visibility:hidden;">
																					<td class="dataLabel" nowrap="nowrap" align="right" valign="top"><b>{$MOD.LBL_PICK_LIST_VALUES}</b>
																					</td>
																					<td align="left" valign="top">
																					<textarea id="fldPickList_{$entries.blockid}" rows="10" class="txtBox" ></textarea>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>				
																</table>
															</td>
														</tr>
													</table>
													
													<table border="0" cellspacing="0" cellpadding="5" width="100%" class="layerPopupTransport">
														<tr>
															<td align="center">
																<input type="button" name="save" value=" {$APP.LBL_SAVE_BUTTON_LABEL}" class="crmButton small save"  onclick = "getCreateCustomFieldForm('{$MODULE}','{$entries.blockid}','add');"/>&nbsp;
																<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmButton small cancel" onclick="fninvsh('addfield_{$entries.blockid}');" />
															</td>
														<input type="hidden" name="fieldType_{$entries.blockid}" id="fieldType_{$entries.blockid}" value="">
														<input type="hidden" name="selectedfieldtype_{$entries.blockid}" id="selectedfieldtype_{$entries.blockid}" value="">
														</tr>
													</table>									
												</div>	
									<!-- end custom field -->
									
									
									{if $entries.blockid neq $COMMENTSECTIONID && $entries.blockid neq $SOLUTIONBLOCKID}
										<img src="{'moveinto.png'|@vtiger_imageurl:$THEME}" border="0"  style="cursor:pointer; height:16px; width:16px" onClick="fnvshobj(this,'movefields_{$entries.blockid}');"  alt="Move Fields" title="Move Fields"/>&nbsp;&nbsp;
									{/if}
									<div id = "movefields_{$entries.blockid}" style="display:none; position:absolute; width:300px;" class="layerPopup">
											<div style="position:relative; display:block">
		 										<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
													<tr>
														<td width="95%" align="left"  class="layerPopupHeading">
															{$MOD.LBL_MOVE_FIELDS}
														</td>
														<td width="5%" align="right">
															<a href="javascript:fninvsh('movefields_{$entries.blockid}');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"  align="absmiddle" /></a>
														</td>
													</tr>
												</table>
												<table border="0" cellspacing="0" cellpadding="0" width="95%">
													<tr>
														<td class=small align="left" >
															<table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="white">
																<tr>
																	<td>	
																		<table border="0" cellspacing="5" cellpadding="0" width="100%" align="left" class=small>
																			<tr>
																				<td>{$MOD.LBL_SELECT_FIELD_TO_MOVE}</td>
																			</tr>
																			<tr>
																				<td><select class="small" id="movefield_assignid_{$entries.blockid}" style="width:225px" size="10" multiple>
																					{foreach name=inner item=value from=$entries.movefield}	
																						<option value="{$value.fieldid}">{$value.fieldlabel}</option>
																					{/foreach}
																					</select>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
												<table border="0" cellspacing="0" cellpadding="5" width="100%" class="layerPopupTransport">
													<tr>
														<td align="center">
															<input type="button" name="save" value="{$APP.LBL_APPLY_BUTTON_LABEL}" class="crmButton small save" onclick ="show_move_hiddenfields('{$MODULE}','{$entries.tabid}','{$entries.blockid}','movehiddenfields');"/>
															<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="fninvsh('movefields_{$entries.blockid}');" />
														</td>
													</tr>
												</table>
											</div>
										</div>						
											
									{if $smarty.foreach.outer.first}
									 	<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
										<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeBlockorder('block_down','{$entries.tabid}','{$entries.blockid}','{$MODULE}') " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
									{elseif $smarty.foreach.outer.last}
										<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeBlockorder('block_up','{$entries.tabid}','{$entries.blockid}','{$MODULE}') " alt="{$MOD.UP}" title="{$MOD.UP}">&nbsp;&nbsp;
										<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
									{else}
									 	<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeBlockorder('block_up','{$entries.tabid}','{$entries.blockid}','{$MODULE}') " alt="{$MOD.UP}" title="{$MOD.UP}">&nbsp;&nbsp;
									 	<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeBlockorder('block_down','{$entries.tabid}','{$entries.blockid}','{$MODULE}') " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
									{/if}
									
								</td>
							</tr>
							<tr>
								{foreach name=inner item=value from=$entries.field}		
							
									{if $value.no % 2 == 0}
								  		</tr>
								  		{assign var="rightcellclass" value=""}
								  		<tr>
								 	{else}
								 		{assign var="rightcellclass" value="class='rightCell'"}
								 	{/if}
								<td width="30%" id="colourButton" >&nbsp;
							 	<span onmouseover="tooltip.tip(this, showProperties('{$value.label}',{$value.mandatory},{$value.presence},{$value.quickcreate},{$value.massedit}));" onmouseout="tooltip.untip(false);" >{$value.label}</span>
							 		{if $value.fieldtype eq 'M'}
							 			<font color='red'> *</font>
							 		{/if}
							 	</td>
								<td width="19%" align = "right" class="colData small" >
									<img src="{'editfield.gif'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="fnvshNrm('editfield_{$value.fieldselect}'); posLay(this, 'editfield_{$value.fieldselect}');" alt="Popup" title="{$MOD.LBL_EDIT_PROPERTIES}"/>&nbsp;&nbsp;
							 		
							 		<div id="editfield_{$value.fieldselect}" style="display:none; position: absolute; width: 225px; left: 300px; top: 300px;" >
							 			<div class="layerPopup" style="position:relative; display:block">
		 									<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
												<tr class="detailedViewHeader">
													<th width="95%" align="left">
														{$value.label}
													</th>
													<th width="5%" align="right">
														<a href="javascript:fninvsh('editfield_{$value.fieldselect}');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"  align="absmiddle" /></a>
													</th>
												</tr>
											</table>
											<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">												
												<tr>
													<td valign="top" class="dvtCellInfo" align="left" width="10px">
														<input id="mandatory_check_{$value.fieldselect}"  type="checkbox"
														{if $value.fieldtype neq 'M' && $value.mandatory eq '0'}
															 disabled
														{elseif $value.mandatory eq '0' && $value.fieldtype eq 'M'} 
															checked  disabled 
														{elseif $value.mandatory eq '3' } 
															disabled 
														{elseif $value.mandatory eq '2'}
														 	checked 
														{/if}
														 onclick = "{if $value.presence neq '0'} enableDisableCheckBox(this,presence_check_{$value.fieldselect}); {/if}
														 			{if $value.quickcreate neq '0' && $value.quickcreate neq '3'} enableDisableCheckBox(this,quickcreate_check_{$value.fieldselect}); {/if}">
													</td>
													<td valign="top" class="dvtCellInfo" align="left">
														&nbsp;{$MOD.LBL_MANDATORY_FIELD}
													</td>
												</tr>
												<tr>
													<td valign="top" class="dvtCellInfo" align="left" width="10px">
														<input id="presence_check_{$value.fieldselect}"  type="checkbox"
														{if $value.displaytype eq '2'}
															checked disabled
														{else}  
															{if $value.presence eq '0' || $value.mandatory eq '0' || $value.quickcreate eq '0' || $value.mandatory eq '2'} 
																checked  disabled   
															{/if}
															{if $value.presence eq '2'} 
														 		checked
														 	{/if}
														  	{if $value.presence eq '3'}
																disabled
															{/if}
														{/if}
														 >
													</td>
													<td valign="top" class="dvtCellInfo" align="left">	
														&nbsp;{$MOD.LBL_ACTIVE}
													</td>
												</tr>
												<tr>
													<td valign="top" class="dvtCellInfo" align="left" width="10px">
														<input id="quickcreate_check_{$value.fieldselect}"  type="checkbox" 
														{if $value.quickcreate eq '0'|| $value.quickcreate eq '2' && ($value.mandatory eq '0' || $value.mandatory eq '2')} 
															checked  disabled   
														{/if}
														{if $value.quickcreate eq '2'}
															checked
														{/if}
														{if $value.quickcreate eq '3'}
															disabled
														{/if}
														 >
													</td>
													<td valign="top" class="dvtCellInfo" align="left">	
														&nbsp;{$MOD.LBL_QUICK_CREATE} 
													</td>
												</tr>
												<tr>
													<td valign="top" class="dvtCellInfo" align="left" width="10px">
														<input id="massedit_check_{$value.fieldselect}"  type="checkbox" 
														{if $value.massedit eq '0'}
															disabled 
														{/if}
														{if $value.massedit eq '1'} 
															checked
														{/if}
														{if $value.displaytype neq '1' || $value.massedit eq '3'}
															disabled
														{/if}>
													</td>
													<td valign="top" class="dvtCellInfo" align="left">	
													&nbsp;{$MOD.LBL_MASS_EDIT}
													</td>
												</tr>
												<tr>
													<td valign="top" class="dvtCellInfo" align="left" width="10px">
														{assign var="defaultsetting" value=$value.defaultvalue}
														<input id="defaultvalue_check_{$value.fieldselect}"  type="checkbox" 
														{if $defaultsetting.permitted eq false}
															disabled 
														{/if}
														{if $defaultsetting.value neq ''}
															checked
														{/if}
													</td>
													<td valign="top" class="dvtCellInfo" align="left">	
														&nbsp;{$MOD.LBL_DEFAULT_VALUE}<br>
														{assign var="fieldElementId" value='defaultvalue_'|cat:$value.fieldselect}
														{if $defaultsetting.permitted eq true}
                											{include file="Settings/FieldUI.tpl" 
                												_FIELD_UI_TYPE=$value.uitype
                												_FIELD_SELECTED_VALUE=$defaultsetting.value
                												_FIELD_ELEMENT_ID=$fieldElementId
                												_ALL_AVAILABLE_VALUES=$defaultsetting._allvalues
                											}
														{/if}
													</td>
												</tr>
												<tr>
													<td colspan="3" class="dvtCellInfo" align="center">
														<input  type="button" name="save"  value=" &nbsp; {$APP.LBL_SAVE_BUTTON_LABEL} &nbsp; " class="crmButton small save" onclick="saveFieldInfo('{$value.fieldselect}','{$MODULE}','updateFieldProperties','{$value.typeofdata}');" />&nbsp;
														{if $value.customfieldflag neq 0}
															<input type="button" name="delete" value=" {$APP.LBL_DELETE_BUTTON_LABEL} " class="crmButton small delete" onclick="deleteCustomField('{$value.fieldselect}','{$MODULE}','{$value.columnname}','{$value.uitype}')" />
														{/if}
														<input  type="button" name="cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmButton small cancel" onclick="fninvsh('editfield_{$value.fieldselect}');" />
													</td>
												</tr>
											</table>
										</div>							 		
							 		</div>
									
									
									{if $smarty.foreach.inner.first}
										{if $value.no % 2 != 0}
											<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
										{/if}
										<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
								 		{if $value.no != ($entries.field|@count - 2) && $entries.no!=1}
											<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('down','{$value.fieldselect}','{$value.blockid}','{$MODULE}') " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
										{else}
											<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
										{/if}
										{if $entries.no!=1}
											<img src="{'arrow_right.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('Right','{$value.fieldselect}','{$value.blockid}','{$MODULE}')" alt="{$MOD.RIGHT}" title="{$MOD.RIGHT}"/>&nbsp;&nbsp;
										{else}
											<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
										{/if}
									{elseif $smarty.foreach.inner.last}
										{if $value.no % 2 != 0}
											<img src="{'arrow_left.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('Left','{$value.fieldselect}','{$value.blockid}','{$MODULE}')" alt="{$MOD.LEFT}" title="{$MOD.LEFT}"/>&nbsp;&nbsp;
										{/if}
										{if $value.no != 1}
											<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('up','{$value.fieldselect}','{$value.blockid}','{$MODULE}') " alt="{$MOD.UP}" title="{$MOD.UP}"/>&nbsp;&nbsp;
									 	{else}
											<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
										{/if}
										<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
										{if $value.no % 2 == 0}
											<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
										{/if}
									{else}
										{if $value.no % 2 != 0}
											<img src="{'arrow_left.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('Left','{$value.fieldselect}','{$value.blockid}','{$MODULE}')" alt="{$MOD.LEFT}" title="{$MOD.LEFT}"/>&nbsp;&nbsp;
										{/if}
										{if $value.no != 1}
											<img src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('up','{$value.fieldselect}','{$value.blockid}','{$MODULE}') " alt="{$MOD.UP}" title="{$MOD.UP}"/>&nbsp;&nbsp;
									 	{else}
											<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
										{/if}
										{if $value.no != ($entries.field|@count - 2)}
											<img src="{'arrow_down.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('down','{$value.fieldselect}','{$value.blockid}','{$MODULE}') " alt="{$MOD.DOWN}" title="{$MOD.DOWN}">&nbsp;&nbsp;
										{else}
											<img src="{'blank.gif'|@vtiger_imageurl:$THEME}" style="width:16px;height:16px;" border="0" />&nbsp;&nbsp;
										{/if}
										{if $value.no % 2 == 0}
											<img src="{'arrow_right.png'|@vtiger_imageurl:$THEME}" border="0" style="cursor:pointer;" onclick="changeFieldorder('Right','{$value.fieldselect}','{$value.blockid}','{$MODULE}')" alt="{$MOD.RIGHT}" title="{$MOD.RIGHT}"/>&nbsp;&nbsp;
										{/if}
									{/if}
								</td>
										
							{/foreach}
							</tr>
						{/if}
					{/foreach}
				</table>
				
					<div id="addblock" style="display:none; position:absolute; width:500px;" class="layerPopup">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
							<tr>
								<td width="95%" align="left" class="layerPopupHeading">{$MOD.LBL_ADD_BLOCK}
								</td>
								<td width="5%" align="right"><a href="javascript:fninvsh('addblock');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"  align="absmiddle" /></a>
								</td>
							</tr>
						</table>
						<table border="0" cellspacing="0" cellpadding="0" width="95%" align="center"> 
							<tr>
								<td class="small" >
									<table border="0" celspacing="0" cellpadding="0" width="100%" align="center" bgcolor="white">
										<tr>
											<td width="50%">
												<table width="100%" border="0" cellpadding="5" cellspacing="0">
													<tr>
														<td class="dataLabel" nowrap="nowrap" align="right" width="30%"><b>{$MOD.LBL_BLOCK_NAME}</b></td>
														<td align="left" width="70%">
														<input id="blocklabel" value="" type="text" class="txtBox">
														</td>
													</tr>
													<tr>
														<td class="dataLabel" align="right" width="30%"><b>{$MOD.AFTER}</b></td>
														<td align="left" width="70%">
														<select id="after_blockid" name="after_blockid">
															{foreach key=blockid item=blockname from=$BLOCKS}
															<option value = {$blockid}> {$blockname} </option>
															{/foreach}
														</select>				
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									<table border=0 cellspacing=0 cellpadding=5 width=100% >
										<tr>
											<td align="center">
												<input type="button" name="save"  value= "{$APP.LBL_SAVE_BUTTON_LABEL}"  class="crmButton small save" onclick=" getCreateCustomBlockForm('{$MODULE}','add') "/>&nbsp;
												<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}"  class="crmButton small cancel" onclick= "fninvsh('addblock');" />
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</form>
