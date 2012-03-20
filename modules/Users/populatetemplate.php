<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

//query the specific vtiger_table and then get the data and write the data here 
include_once('modules/Contacts/Contacts.php');
include_once('modules/Leads/Leads.php');
include_once('modules/Users/Users.php');
require_once('include/utils/utils.php');
global $log;

//download the template file and store it in some specific location
$sql = "select templatename,body from vtiger_emailtemplates where templateid=?";
$tempresult = $adb->pquery($sql, array($_REQUEST["templateid"]));
$tempArray = $adb->fetch_array($tempresult);
$fileContent = $tempArray["body"];

checkFileAccess($root_directory.'/modules/Emails/templates/'.$_REQUEST["templatename"]);
$handle = fopen($root_directory.'/modules/Emails/templates/'.$_REQUEST["templatename"],"wb") ;
fwrite($handle,$fileContent,89999999);
fclose($handle);

//create a file and write to it so that it can be used as the emailtemplateusage.php file

if (is_file($root_directory.'/modules/Emails/templates/testemailtemplateusage.php')) {
	$is_writable = is_writable($root_directory.'/modules/Emails/templates/testemailtemplateusage.php');
} else {
	$is_writable = is_writable('.');
}
 
$myString = "<?php \n";
$myString .= "/*********************************************************************************\n";
$myString .= " * The contents of this file are subject to the vtigerCRM License \n";
$myString .= " * All Rights Reserved.\n";
$myString .= " * Contributor(s): ______________________________________.\n";
$myString .= "********************************************************************************/\n\n";

$module = $_REQUEST['entity'];
$recordid = $_REQUEST['entityid'];

$focus = CRMEntity::getInstance($module);
$focus->retrieve_entity_info($recordid,$module);

$i=0;
$m=0;
$n=0;
$myString;

//storing the columnname and the value pairs
foreach ($focus->column_fields as $columnName=>$value) {
  $myString .= "$" .$module ."_" .$columnName.' = "'. $value."\";\n\n";
  $colName[$i] = $columnName;
  $i++;
  $j=$i;
}

global $current_user;
global $adb;
$query = 'select * from vtiger_users where id= ?';
$result = $adb->pquery($query, array($current_user->id));
$res_row = $adb->fetchByAssoc($result);
foreach ($res_row as $columnName=>$value) {
	$myString .='$users_' .$columnName.' = "'. $value."\";\n\n";
	$usercolName[$n] = $columnName;
	$n++;
	$m=$n;
}

$myString .= "\$globals = \"";

for($i=0;$i<$j-1;$i++) {
	$myString .= "\\$" .$module ."_" .$colName[$i].", ";
}
for($n=0;$n<$m;$n++) {
	$myString .= '\\$users_' .$usercolName[$n].", ";
}

$myString .= "\\$" .$module ."_" .$colName[$i];
$myString .="\"; \n\n";

$myString .= "?> \n";
if ($is_writable && ($config_file = @ fopen($root_directory.'/modules/Emails/templates/testemailtemplateusage.php', "w"))) {
	$log->debug("writing to the testemailtemplatuseage.php file");
	fputs($config_file, $myString, strlen($myString));
	fclose($config_file);
}
checkFileAccess($root_directory.'/modules/Emails/templates/'.$_REQUEST["templatename"]);
$templatename = $root_directory.'/modules/Emails/templates/'.$_REQUEST["templatename"];
header("Location:index.php?module=Users&action=TemplateMerge&templatename=".$templatename);

?>
<script>
window.close()
</script>
