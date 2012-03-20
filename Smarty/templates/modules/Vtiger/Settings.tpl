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
<br />
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	    <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	    	<br />
	    	<div align=center>
			{include file='SetMenu.tpl'}

			<table class="settingsSelUITopLine" align="center" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
			    	
					<td rowspan="2" valign="top" width="50"><img src="{'vtlib_modmng.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_MODULE_MANAGER}" title="{$MOD.LBL_MODULE_MANAGER}" border="0" height="48" width="48"></td>
					<td class="heading2" valign="bottom"> <b><a href="index.php?module=Settings&action=ModuleManager&parenttab=Settings">{$MOD.VTLIB_LBL_MODULE_MANAGER}</a> &gt; {$MODULE_LBL} </td>
				</tr>
				<tr>
					<td class="small" valign="top">{$MOD.VTLIB_LBL_MODULE_MANAGER_DESCRIPTION}</td>
				</tr>
				</table>
				
				<br>
				<table border="0" cellspacing="0" cellpadding="20" width="100%" class="settingsUI">
					<tr>
						<td>
							<table border="0" cellspacing="0" cellpadding="10" width="100%">
								<tr>
									{foreach key=mod_name item=mod_array from=$MENU_ARRAY name=itr}
									<td width=25% valign=top>
										{if $mod_array.label eq ''}
											&nbsp;
										{else}
										<table border=0 cellspacing=0 cellpadding=5 width="100%">
											<tr>
												{assign var=count value=$smarty.foreach.itr.iteration}
												<td rowspan=2 valign=top width="20%">
													<a href="{$mod_array.location}">
													<img src="{$mod_array.image_src}" alt="{$mod_array.label}" width="48" height="48" border=0 title="{$mod_array.label}">
													</a>
												</td>
												<td class=big valign=top>
													<a href="{$mod_array.location}">
													{$mod_array.label}
													</a>
												</td>
											</tr>
											<tr>
												<td class="small" valign=top width="80%">
													{$mod_array.desc}
												</td>
											</tr>
										</table>
										{/if}
									</td>
									{if $count mod 3 eq 0}
										</tr><tr>
									{/if}
									{/foreach}
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
</table>
			</div>
		</td>
		<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	</tr>
</table>
<br>