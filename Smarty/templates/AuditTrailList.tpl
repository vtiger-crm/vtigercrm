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

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
<form action="index.php" method="post" name="AuditTrail" id="form" onsubmit="VtigerJS_DialogBox.block();">
<input type='hidden' name='module' value='Settings'>
<input type='hidden' name='action' value='AuditTrail'>
<input type='hidden' name='return_action' value='ListView'>
<input type='hidden' name='return_module' value='Settings'>
<input type='hidden' name='parenttab' value='Settings'>

        <br>

	<div align=center>
			{include file='SetMenu.tpl'}
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'audit.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_AUDIT_TRAIL}" width="48" height="48" border=0 title="{$MOD.LBL_AUDIT_TRAIL}"></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > {$MOD.LBL_AUDIT_TRAIL}</b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_AUDIT_TRAIL_DESC}</td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
				
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
				<tr>
				<td class="big" height="40px;" width="70%"><strong>{$MOD.LBL_AUDIT_TRAIL}</strong></td>
				<td class="small" align="center" width="30%">&nbsp;
					<span id="audit_info" class="crmButton small cancel" style="display:none;"></span>
				</td>
				</tr>
				</table>
			
							<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
			<tr>
	         	    <td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
                        <tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_ENABLE_AUDIT_TRAIL} </strong></td>
                            <td width="80%" class="small cellText">
			{if $AuditStatus eq 'enabled'}
				<input type="checkbox" checked name="enable_audit" onclick="auditenabled(this)"></input>
			{else}
				<input type="checkbox" name="enable_audit" onclick="auditenabled(this)"></input>
			{/if}
			</td>
                        </tr>
                        <tr valign="top">
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_USER_AUDIT}</strong></td>
                            <td class="small cellText">
				<select name="user_list" id="user_list" class="small">
					{$USERLIST}
				</select>	
			    </td>
			    <td class="small cellText" align=right>
							<input title="{$MOD.LBL_VIEW_AUDIT_TRAIL}" class="crmButton small edit" onclick="showAuditTrail();" type="button" name="button" value="{$MOD.LBL_VIEW_AUDIT_TRAIL}" >&nbsp;&nbsp;
				</td>	
                        </tr>
                        
                        </table>
	    </td>
                        </tr>
                        </table>	
				<!--table border=0 cellspacing=0 cellpadding=5 width=100% >
				<tr>
					  <td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
				</tr>
				</table-->
	
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

function auditenabled(ochkbox)
{
	if(ochkbox.checked == true)
	{
	     var status='enabled';
	$('audit_info').innerHTML = 'Audit Trail Enabled';
	     $('audit_info').style.display = 'block';		
		
			
	}
	else
	{
	    var status = 'disabled';	
	     $('audit_info').innerHTML = 'Audit Trail Disabled';
	     $('audit_info').style.display = 'block';		
	
	}
             $("status").style.display="block";
	     new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'module=Settings&action=SettingsAjax&file=SaveAuditTrail&ajax=true&audit_trail='+status,
                        onComplete: function(response) {
                                $("status").style.display="none";
                        }
                }
        );
			
	setTimeout("hide('audit_info')",3000);
}

function showAuditTrail()
{
	
	var userid = $('user_list').options[$('user_list').selectedIndex].value;
	window.open("index.php?module=Settings&action=SettingsAjax&file=ShowAuditTrail&userid="+userid,"","width=645,height=750,resizable=0,scrollbars=1,left=100");
	

}
</script>
{/literal}
