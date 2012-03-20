<script type="text/javascript" charset="utf-8">
{literal}
	function addStylesheet(url){
		/*From: http://www.hunlock.com/blogs/Howto_Dynamically_Insert_Javascript_And_CSS*/
		var headID = document.getElementsByTagName("head")[0];         
		var cssNode = document.createElement('link');
		cssNode.type = 'text/css';
		cssNode.rel = 'stylesheet';
		cssNode.href = url;
		cssNode.media = 'screen';
		headID.appendChild(cssNode);
	}
	addStylesheet('modules/com_vtiger_workflow/resources/style.css');
	
	{/literal}
</script>
{include file='SetMenu.tpl'}
<div id="view">
	{include file='com_vtiger_workflow/ModuleTitle.tpl'}
	
	<table width="98%" cellspacing="0" cellpadding="0" border="0" align="center"><tbody>
		<tr>
			<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"/></td>
			<td width="100%" valign="top" style="padding: 10px;" class="showPanelBg">
	
	
	<form action="index.php" method="get" accept-charset="utf-8" onsubmit="VtigerJS_DialogBox.block();">
		<p>
			Create task
			<select name="task_type">
{foreach item=taskType from=$taskTypes}
				<option>
					{$taskType}
				</option>
{/foreach}
			</select>
			for  
			<select name="module_name">
{foreach item=moduleName from=$moduleNames}
				<option>
					{$moduleName}
				</option>
{/foreach}
			</select>
			<input type="submit" name="create" value="Create" id="create_new">
			<input type="hidden" name="save_type" value="new" id="save_type_new">
			<input type="hidden" name="module" value="Workflow" id="save_module">
			<input type="hidden" name="action" value="edittask" id="save_action">
			<input type="hidden" name="return_url" value="{$returnUrl}" id="save_return_url">
		</p>
	</form>
	<table class="lvt" width="100%" cellspacing="1" cellpadding="3" border="0">
		<tr><td class="lvtCol">Module <a href"#"></a></td><td class="lvtCol">Description</td></tr>
{foreach item=task from=$tasks}
		<tr><td class="lvtColData"><a href="index.php?action=edittask&module=Workflow&save_type=edit&task_id={$task->id}">{$task->moduleName}</a></td>
			<td class="lvtColData"><a href="index.php?action=edittask&module=Workflow&save_type=edit&task_id={$task->id}">{$task->summary}</a></td></tr>
{foreachelse}
			{include file='Workflow/EmptyList.tpl'} 
{/foreach}
	</table>
	</td>
</tr>
<tbody></table>
</div>