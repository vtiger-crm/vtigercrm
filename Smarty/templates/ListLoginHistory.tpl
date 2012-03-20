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
<script src="include/scriptaculous/prototype.js" type="text/javascript"></script>
<script src="include/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript" src="include/js/general.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/dtlviewajax.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/menu.js"></script>

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
<form action="index.php" method="post" name="ListLoginHistory" id="form" onsubmit="VtigerJS_DialogBox.block();">
<input type='hidden' name='module' value='Users'>
<input type='hidden' name='action' value='ListLoginHistory'>
<input type='hidden' name='record' id='record' value="{$ID}">
<input type='hidden' name='parenttab' value='Settings'>

        <br>

	<div align=center>
			{include file='SetMenu.tpl'}
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'set-IcoLoginHistory.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_AUDIT_TRAIL}" width="48" height="48" border=0 title="{$MOD.LBL_LOGIN_HISTORY_DETAILS}"></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > {$MOD.LBL_LOGIN_HISTORY_DETAILS}</b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_LOGIN_HISTORY_DESCRIPTION}</td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
				
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
				<tr>
				<td class="big" height="30px;"><strong>{$MOD.LBL_LOGIN_HISTORY_DETAILS}</strong></td>
				<td class="small" align="left">&nbsp;</td>
				</tr>
				</table>
			
							<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
			<tr>
	         	    <td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
                       
                        <tr valign="top">
                            <td nowrap width="18%" class="small cellLabel"><strong>{$MOD.LBL_USER_AUDIT}</strong></td>
                            <td class="small cellText">
				<select name="user_list" id="user_list" onchange="fetchlogin_js({$ID});">
				<option value="none" selected="true">{$APP.LBL_NONE}</option>
				{$USERLIST}
				</select>	
			    </td>
			    
                        </tr>
                        
                        </table>
	    </td>
                        </tr>
			    <table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
			    	<tr>
				     <td class="big">	
					<strong>{$CMOD.LBL_LOGIN_HISTORY}</strong>
				     </td>
			        </tr>
			    </table>
			    <table border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr><td align="left"><div id="login_history_cont" style="display:none;"></div></td><td></td></tr>	
			    </table>	
			    <br>	
                        </table>	
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
	</table>
		
	</div>

</td>
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</tbody>
</form>
</table>

{literal}
<script>
function fetchlogin_js(id)
{
	fetchLoginHistory(id);
}
function fetchLoginHistory(id)
{
	var oUser_combo = $('user_list');
	var id = oUser_combo.options[oUser_combo.selectedIndex].value;
	if ( id == 'none')
	{
		Effect.Fade('login_history_cont');
	}
	else
	{
        	$("status").style.display="inline";
	        new Ajax.Request(
        	        'index.php',
                	{queue: {position: 'end', scope: 'command'},
                        	method: 'post',
                       		postBody: 'module=Users&action=UsersAjax&file=ShowHistory&ajax=true&record='+id,
	                        onComplete: function(response) {
        	                        $("status").style.display="none";
                	                $("login_history_cont").innerHTML= response.responseText;
					Effect.Appear('login_history_cont');
                       		}
               		}
        		);
	}
}
</script>
{/literal}
<script>
function getListViewEntries_js(module,url)
{ldelim}
	var oUser_combo = $('user_list');
	var id = oUser_combo.options[oUser_combo.selectedIndex].value;
	$("status").style.display="inline";
        new Ajax.Request(
        	'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                	method: 'post',
                        postBody:"module="+module+"&action="+module+"Ajax&file=ShowHistory&record="+id+"&ajax=true&"+url,
			onComplete: function(response) {ldelim}
                        	$("status").style.display="none";
                                $("login_history_cont").innerHTML= response.responseText;
                  	{rdelim}
                {rdelim}
        );
{rdelim}
</script>

