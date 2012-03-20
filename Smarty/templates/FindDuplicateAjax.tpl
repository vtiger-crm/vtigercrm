<table width='100%' border='0' cellpadding='5' cellspacing='0' class='tableHeading'>
	<tr>
		<td class='big' align='left'>
			<b>{$APP.LBL_DUPLICATE_DATA_IN} {$MOD.LBL_MODULE_NAME}</b>
		</td>
	</tr>
</table>
<br>
<table border="0" align ="center" width ="95%">
	<tr>
		<td >
            {if $DELETE eq 'Delete'}
                 <input class="crmbutton small delete" type="button" value="{$APP.LBL_DELETE}" onclick="return delete_fields('{$MODULE}')"/>
            {/if}
        </td>
		<td nowrap >
			<table border=0 cellspacing=0 cellpadding=0 class="small">
				<tr>{$NAVIGATION}</tr>
			</table>	
        </td>
	</tr>
</table>

<table class="lvt small" border="0" cellpadding="3" align="center" cellspacing="1" width="95%" >
<tr>
	<td class="lvtCol" width="40px">
		<input type="checkbox" name="CheckAll" onclick='selectAllDel(this.checked,"del");' >
	</td>
	{foreach key=key item=field_values from=$FIELD_NAMES}
		<td class="lvtCol big"> 
			<b>{$key|@getTranslatedString:$MODULE}</b>
		</td>
	{/foreach}
	<td class="lvtCol big" cellpadding="3">
		{$APP.LBL_MERGE_SELECT}
	</td>
	<td class="lvtCol big" cellpadding="2" width="120px">
		{$APP.LBL_ACTION}
	</td>
</tr>
	{assign var=tdclass value='IvtColdata'}
	{foreach key=key1 item=data from=$ALL_VALUES}
		{assign var=cnt value=$data|@sizeof}
		{assign var=cnt2 value=0}
		{if $tdclass eq 'IvtColdata'}
			{assign var=tdclass value='sep1'}
		{else if $tdclass eq 'sep1'}
			{assign var=tdclass value='IvtColdata'}
		{/if}
			{foreach key=key3 item=newdata1 from=$data}
				<tr class="{$tdclass}" nowrap="nowrap" >
					<td>
						<input type="checkbox" name="del" value="{$data.$key3.recordid}" onclick='selectDel(this.name,"CheckAll");'  >
					</td>
					{foreach key=key item=newdata2 from=$newdata1}
						<td >
							{if $key eq 'recordid'}	
								<a href="index.php?module={$MODULE}&action=DetailView&record={$data.$key3.recordid}&parenttab={$CATEGORY}" target ="blank">{$newdata2}</a>
							{else}
								{if $key eq 'Entity Type'}
									{if $newdata2 eq 0 && $newdata2 neq NULL}
										{if $VIEW eq true}
											{$APP.LBL_LAST_IMPORTED}
										{else}
											{$APP.LBL_NOW_IMPORTED}
										{/if}
									{else}
										{$APP.LBL_EXISTING}
								{/if}
							{else}
								{$newdata2}
							{/if}
							{/if}
						</td>
					{/foreach}	
					<td cellpadding="3" nowrap width="80px">
						<input name="{$key1}" id="{$key1}" value="{$data.$key3.recordid}"  type="checkbox">
					</td>
					{if $cnt2 eq 0}
						<td align="center" rowspan='{$cnt}'><input class="crmbutton small edit" name="merge" value="{$APP.LBL_MERGE}" onclick="merge_fields('{$key1}','{$MODULE}','{$CATEGORY}');" type="button"></td>
					{/if}
					{assign var=cnt2 value=$cnt2+1}
				</tr>
			{/foreach}
	{/foreach}
</table>
<div name="group_count" id="group_count" style="display :none">
		{$NUM_GROUP}
</div>
