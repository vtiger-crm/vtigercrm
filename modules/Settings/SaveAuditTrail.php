<?php
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/

global $root_directory;
$filename = $root_directory.'user_privileges/audit_trail.php';

$readhandle = @fopen($filename, "r+");

if($readhandle)
{
	$buffer = '';
	$new_buffer = '';
	while(!feof($readhandle))
	{
		$buffer = fgets($readhandle, 5200);
		list($starter, $tmp) = explode(" = ", $buffer);

		if($starter == '$audit_trail' && stristr($tmp,'false'))
		{
			$new_buffer .= "\$audit_trail = 'true';\n";
		}
		elseif($starter == '$audit_trail' && stristr($tmp,'true'))
		{
			$new_buffer .= "\$audit_trail = 'false';\n";
		}
		else
			$new_buffer .= $buffer;
	}
	fclose($readhandle);
}

$handle = fopen($filename, "w");
fputs($handle, $new_buffer);
fclose($handle);

?>
