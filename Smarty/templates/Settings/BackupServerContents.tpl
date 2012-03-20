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
{if $BKP_SERVER_MODE eq 'edit'}	
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
			<tr>
      			    <td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
                        <tr>
                            <td width="20%" nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_SERVER_ADDRESS} </strong></td>
                            <td width="80%" class="small cellText">
				<input type="text" class="detailedViewTextBox small" value="{$FTPSERVER}" name="server"></strong>
			    </td>
                          </tr>
                          <tr valign="top">

                            <td nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_USERNAME}</strong></td>
                            <td class="small cellText">
				<input type="text" class="detailedViewTextBox small" value="{$FTPUSER}" name="server_username">
			    </td>
                          </tr>
                          <tr>
                            <td nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_PASWRD}</strong></td>
                            <td class="small cellText">
				<input type="password" class="detailedViewTextBox small" value="{$FTPPASSWORD}" name="server_password">
			    </td>
                          </tr>
                        </table>
			{else}
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
			<tr>
	         	    <td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
                        <tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SERVER_ADDRESS} </strong></td>
                            <td width="80%" class="small cellText"><strong>{$FTPSERVER}&nbsp;</strong></td>
                        </tr>
                        <tr valign="top">
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_USERNAME}</strong></td>
                            <td class="small cellText">{$FTPUSER}&nbsp;</td>
                        </tr>
                        <tr>
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_PASWRD}</strong></td>
                            <td class="small cellText">
				{if $FTPPASSWORD neq ''}
				******
				{/if}&nbsp;
			    </td>
                        </tr>
                        </table>
					
			{/if}	
			</td>
			  </tr>
			</table>
			<!--table border=0 cellspacing=0 cellpadding=5 width=100% >
			<tr>
				  <td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
			</tr>
			</table-->
