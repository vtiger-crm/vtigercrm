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

<div>	
	<table border="0" cellspacing="0" cellpadding="5" width="100%" height="450px">
		<tr>
			<td align="center">
				<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 80%; position: relative; z-index: 10000000;'>
					<table border='0' cellpadding='5' cellspacing='0' width='98%'>
						<tr>
							<td rowspan='2' width='11%'><img src="{'denied.gif'|@vtiger_imageurl:$THEME}" ></td>
							<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'>
								<span class='genHeaderSmall'>{$MOD_PICKLIST.LBL_ERR_CYCLIC_DEPENDENCY}</span>
							</td>
						</tr>
						<tr>
							<td class='small' align='right' nowrap='nowrap'>
								<a href='{$RETURN_URL}'>{$APP.LBL_GO_BACK}</a><br>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</div>