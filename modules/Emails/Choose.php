<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/database/PearDatabase.php');
$idlist = vtlib_purify($_POST['idlist']);
$pmodule=vtlib_purify($_REQUEST['return_module']);
$ids=explode(';',$idlist);

if ($pmodule=='Accounts')
{
	$querystr="select fieldid,fieldlabel,columnname,tablename from vtiger_field where tabid=6 and uitype='13' and vtiger_field.presence in (0,2)"; 
}
elseif ($pmodule=='Contacts')
{
	$querystr="select fieldid,fieldlabel,columnname from vtiger_field where tabid=4 and uitype='13' and vtiger_field.presence in (0,2)";
}
elseif ($pmodule=='Leads')
{
	$querystr="select fieldid,fieldlabel,columnname from vtiger_field where tabid=7 and uitype='13' and vtiger_field.presence in (0,2)";
}

$result=$adb->pquery($querystr, array());
$numrows = $adb->num_rows($result);

?>
<form name="choosemails" action="post" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="emailids" value="">
<h4>The following emails are available for the selected record. Please choose the ones you would like to use.</h4>
<div align="center">
<table cellpadding="0" cellspacing="0" border="0">
	<?php
	for ($i=0;$i<$numrows;$i++)
	{
		$temp=$adb->query_result($result,$i,'columnname');
		$temp1=br2nl($myfocus->column_fields[$temp]);
		echo '<tr><td>'.$adb->query_result($result,$i,'fieldlabel').' </td><td>&nbsp;&nbsp;&nbsp;<input name="emails'.$i.'" type="checkbox" title="Raju"></td><td>'.$temp1.'</tr>';
	}
	?>

	<script language="javascript">
	function passemail()
	{		
		y=new Array();
		<?php  
		foreach ($ids as $id_num => $id)
		{
			print "y.push(\"$id\" );";
		}
		$cnt=count($ids);
		print "idcount=$cnt;";
		?>
		<?php 
		for ($x=0;$x<$numrows;$x++)
		{
			?>
			if (document.choosemails.emails<?php echo $x;?>.checked)
			{
				for (m=0;m<idcount;m++)
				{
					y[m]=y[m]+"@<?php echo $adb->query_result($result,$x,'fieldid');?>";
				}
			}
			<?php 
		} 
		?>
		stry = y.join("");
		document.choosemails.emailids.value=stry;
		document.choosemails.submit();
	}
	</script>

</table>
<input type="button" name="OK" onClick="passemail()" value="OK">
</div>
</form>
