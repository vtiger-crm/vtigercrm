<div style='overflow: hidden;'>
<table border='0' cellpadding='2' cellspacing='0' width="100%">
	<tr valign=top>
		<td align='left'><b>{$APP.LBL_VTIGER_NEWS}</b></td>
		<td align='right'>&nbsp;</td>
		<td align='right'>
			<a style='padding-left: 10px;' href="javascript:;" onclick="fninvsh('vtigerNewsPopupLay');"><img src='{'close.gif'|@vtiger_imageurl:$THEME}' align='absmiddle' border='0'></a></td>
	</tr>

	<tr>
		<td colspan='3'><hr></td>
	</tr>

	{foreach item=NEWS from=$NEWSLIST}
	<tr>
		<td colspan='3' align='left'><a class='small' href='{$NEWS->get_link()}' target='_blank' style='color: #0070BA;'>{$NEWS->get_title()}</a></td>
	</tr>
	{foreachelse}
	<tr>
		<td colspan='3'>{$MOD.LBL_NEWS_NO}</td>
	</tr>
	{/foreach}
</table>
</div>
