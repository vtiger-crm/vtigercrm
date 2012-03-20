<tr>
	<td align="center" colspan="9" style="background-color: rgb(239, 239, 239); height: 340px;">
		<div style="border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 45%; position: relative; z-index: 10000000;">
			<table width="98%" cellspacing="0" cellpadding="5" border="0"><tbody>
				<tr>
					<td width="25%" rowspan="2"><img width="61" height="60" src="{'empty.jpg'|@vtiger_imageurl:$THEME}"/></td>
					<td width="75%" nowrap="nowrap" style="border-bottom: 1px solid rgb(204, 204, 204);">
						<span class="genHeaderSmall">{$APP.LBL_NO} {$module->label} {$APP.LBL_FOUND}</span>
					</td>
				</tr>
				<tr>
					<td nowrap="nowrap" align="left" class="small">
						{$APP.LBL_YOU_CAN_CREATE} {$APP.LBL_A} {$module->label}. Click the link below:<br/>  
						-<a href="index.php?module={$module->name}&action=editworkflow&return_url={$module->currentUrl()|urlencode}{$MODULE_NAME}&parenttab=Settings">
							{$APP.LBL_CREATE} {$APP.LBL_A} {$module->label}.
						</a><br/>
					</td>
				</tr>
			</tbody></table> 
		</div>
	</td>
</tr>