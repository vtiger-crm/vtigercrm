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


global $mod_strings;
global $app_strings;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

require_once('include/home.php');
require_once('Smarty_setup.php');
require_once('include/freetag/freetag.class.php');

$homeObj=new Homestuff;
$smarty=new vtigerCRM_Smarty;
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);

if(!empty($_REQUEST['homestuffid'])){
	$stuffid = $_REQUEST['homestuffid'];
}
if(!empty($_REQUEST['blockstufftype'])){
	$stufftype = $_REQUEST['blockstufftype'];
}

if($stufftype=='Tag Cloud'){
	$freetag = new freetag();
	$smarty->assign("ALL_TAG",$freetag->get_tag_cloud_html("",$current_user->id));
	$smarty->display("Home/TagCloud.tpl");
}elseif($stufftype == 'Notebook'){
	$contents = $homeObj->getNoteBookContents($stuffid);
	$smarty->assign("NOTEBOOK_CONTENTS",$contents);
	$smarty->assign("NOTEBOOKID", $stuffid);
	$smarty->display("Home/notebook.tpl");
}elseif($stufftype == 'URL'){
	$url = $homeObj->getWidgetURL($stuffid);
	if(strpos($url, "://") === false){
		$url = "http://".trim($url);
	}
	$smarty->assign("URL",$url);
	$smarty->assign("WIDGETID", $stuffid);
	$smarty->display("Home/HomeWidgetURL.tpl");
}else{
	$homestuff_values=$homeObj->getHomePageStuff($stuffid,$stufftype);
	if($stufftype=="DashBoard"){
		$homeObj->getDashDetails($stuffid,'type');
		$dashdet=$homeObj->dashdetails;
	}
}

$smarty->assign("DASHDETAILS",$dashdet);
$smarty->assign("HOME_STUFFTYPE",$stufftype);
$smarty->assign("HOME_STUFFID",$stuffid);
$smarty->assign("HOME_STUFF",$homestuff_values);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);

$smarty->display("Home/HomeBlock.tpl");
?>
