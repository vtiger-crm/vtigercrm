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


global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('include/database/PearDatabase.php');
require_once('include/utils/UserInfoUtil.php');
require_once('vtigerversion.php');
if($patch_version !='')
{
	    $patch_string = $vtiger_current_version . " Patch " . $patch_version;
}
else
{
	    $patch_string = $vtiger_current_version;
}
global $app_strings;
global $app_list_strings;
global $mod_strings;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $app_strings['LBL_CHARSET'];?>">
<title><?php echo $mod_strings['TITLE_VTIGER_CRM_5'];?></title>
<link href="<?php echo $theme_path;?>style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="include/js/popup.js"></script>
</head>
<style>
	.rollOver{
		font-family:Verdana, Arial, Helvetica, sans-serif;
		font-size:12px;
		border:0px solid white;
		width:100%;
		padding:0px;
	}
	
	.rollOver tr th{
		font-family:Verdana, Arial, Helvetica, sans-serif;
		font-size:12px;
		border:0px solid white;
		padding-left:10px;
		padding-bottom:5px;
		font-weight:bold;
		text-decoration:underline;
		color:#000000;
		text-align:left;
	}
	
	.rollOver tr td{
		font-family:Verdana, Arial, Helvetica, sans-serif;
		font-size:11px;
		border:0px solid white;
		padding-left:30px;
		font-weight:normal;
		text-decoration:none;
		color:#000000;
		text-align:left;
		padding-bottom:1px;
	}
</style>
<body>
<table border="0" cellpadding="0" cellspacing="0" width="500">
		<tr>
				<td colspan="3"><img src="<?php echo vtiger_imageurl('aboutUS.jpg', $theme) ?>" width="500" height="301"></td>
		</tr>
		<tr>
				<td width="15%" style="border-left:2px solid #7F7F7F;">&nbsp;</td>
				<td width="70%" style="border:3px solid #CCCCCC;" height="100" >
						<marquee behavior="scroll" direction="up" width="100%" scrollamount="1" scrolldelay="50"  height="100" onMouseOut="javascript:start();" onMouseOver="javascript:stop();">
								<table width="100%" border="0" cellpadding="0" cellspacing="0" class="rollOver">
								<tr><th><?php echo $mod_strings['LBL_TEAM'];?></th></tr>
										<tr><td>Akilan</td></tr>
										<tr><td>Anusha</td></tr>
										<tr><td>Asha</td></tr>
										<tr><td>Bharath</td></tr>
										<tr><td>Bharathi</td></tr>
										<tr><td>Dhananjay</td></tr>
										<tr><td>Dina</td></tr>
										<tr><td>Don</td></tr>
										<tr><td>Fenzik</td></tr>
										<tr><td>Gopal</td></tr>
										<tr><td>Jeri</td></tr>
										<tr><td>Kiran</td></tr>
										<tr><td>Mani</td></tr>
										<tr><td>Mickie</td></tr>
										<tr><td>Minnie</td></tr>
										<tr><td>Musavir</td></tr>
										<tr><td>Nitin</td></tr>
										<tr><td>Panchavarnam</td></tr>
										<tr><td>Pavani</td></tr>
										<tr><td>Philip</td></tr>
										<tr><td>Pinaki</td></tr>
										<tr><td>Prasad</td></tr>
										<tr><td>Preethi</td></tr>
										<tr><td>Puneeth</td></tr>
										<tr><td>Radiant</td></tr>
										<tr><td>Richie</td></tr>
										<tr><td>Sandeep</td></tr>
										<tr><td>Shahul</td></tr>
										<tr><td>Sidharth</td></tr>
										<tr><td>Sreenivas</td></tr>
										<tr><td>Srinivasan</td></tr>
										<tr><td>Tamilmani</td></tr>
										<tr><td>Vamsee</td></tr>
										<tr><td>Varma</td></tr>
										<tr><td>Vashni</td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><th><?php echo $mod_strings['LBL_CREDITS'];?></th></tr>
										<tr><td>Aissa Belaid</td></tr>
										<tr><td>Allan Bush</td></tr>
										<tr><td>Brian Devendorf</td></tr>
										<tr><td>Brian Laughlin</td></tr>
										<tr><td>Davide Giarolo</td></tr>	
										<tr><td>Dennis Grant</td></tr>
										<tr><td>Dhr. R.R. Gerbrands</td></tr>
										<tr><td>Dino Eberle</td></tr> 
										<tr><td>Dirk Gorny</td></tr>
										<tr><td>Fathi Boudra</td></tr>
										<tr><td>Frank Piepiorra</td></tr>
										<tr><td>Jamie Jackson</td></tr>
										<tr><td>Jeff Kowalczyk</td></tr>
										<tr><td>Jens Gammelgaard</td></tr>
										<tr><td>Jens Hamisch</td></tr>
										<tr><td>Joao Oliveira</td></tr>
										<tr><td>Joel Rydbeck</td></tr>
										<tr><td>Josh Lee</td></tr>
										<tr><td>Ken Lyle</td></tr>
										<tr><td>Kim Haverblad</td></tr>
										<tr><td>Manilal K M</td></tr>
										<tr><td>Matjaz Slak</td></tr>
										<tr><td>Matthew Brichacek</td></tr>
										<tr><td>Michel Jacquemes</td></tr> 
										<tr><td>Mike Crowe</td></tr> 
										<tr><td>Mike Fedyk</td></tr>
										<tr><td>Neil</td></tr>
										<tr><td>Tim Smith</td></tr>
										<tr><td>Sergio A. Kessler</td></tr>
										<tr><td>Steve Fairchild</td></tr>
										<tr><td>Valmir Carlos Trindade</td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><th><?php echo $mod_strings['LBL_CREDITS'];?> - <?php echo $mod_strings['LBL_THIRD_PARTY'];?></th></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://adodb.sourceforge.net')">ADOdb</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://www.os-solution.com/demo/ajaxcsspopupchat/index.php')">Ajax Popup Chat</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://httpd.apache.org/')">Apache HTTP Server</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://www.linuxscope.net/articles/mailAttachmentsPHP.html')">Attachments in E-mail Client</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://www.hmhd.com/steve')">Calculator</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://www.dynamicdrive.com/dynamicindex14/carousel2.htm')">Carousel Slideshow</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://www.troywolf.com/articles/php/class_http/')">class_http</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://freshmeat.net/projects/phpexcelreader/')">ExcelReader</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://ckeditor.com/download')">CKEditor</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://www.fpdf.org')">FPDF</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://www.getluky.net')">freetag</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://www.boutell.com/gd/')">gdwin32</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://pear.php.net/package/Image_Graph')">Graph</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://slayeroffice.com/code/imageCrossFade/xfade2.html')">Image Crossfade Redux</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://pear.php.net/pepr/pepr-proposal-show.php?id=212')">Image_Canvas</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://pear.php.net/package/Image_Color')">Image_Color</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://www.dynarch.com/projects/calendar/')">jscalendar</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://www.vxr.it/log4php/')">log4php</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://magpierss.sourceforge.net/')">MagpieRSS</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://wiki.wonko.com/software/mailfeed/')">Mailfeed</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://www.mysql.com')">MySQL</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://sourceforge.net/projects/nusoap')">nusoap</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://nocc.sourceforge.net')">NOCC</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://www.php.net')">PHP</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://phpmailer.sourceforge.net/')">PHPMailer</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://phpsysinfo.sourceforge.net/')">phpSysinfo</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://prototype.conio.net')">Prototype</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://script.aculo.us')">script.oculo.us</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://smarty.php.net/')">Smarty Template Engine</a></td></tr>
										<tr><td><a href="javascript:;" onClick=" newpopup('http://www.sugarcrm.com')">SugarCRM</a> (SPL 1.1.2)</td></tr>

<tr><td><a href="javascript:;" onClick=" newpopup('http://tcpdf.sourceforge.net')">TCPDF</a></td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td><b><?php echo $mod_strings['LBL_COMMUNITY'];?></b></td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td>&nbsp;</td></tr>
								</table>
						</marquee>
				</td>
				<td width="15%" style="border-right:2px solid #7F7F7F;">&nbsp;</td>
		</tr>
		<tr><td colspan="3"  style="border-left:2px solid #7F7F7F;border-right:2px solid #7F7F7F">&nbsp;</td></tr>
		<tr>
		  <td colspan="3" background="<?php echo vtiger_imageurl('about_btm.jpg', $theme) ?>" height="75">
		  		<table width="100%" border="0" cellpadding="5" cellspacing="0">
						<tr>
							<td width="70%" align="left" class="small">
							<span class="small" style="color:#999999;"><?php echo $mod_strings['LBL_VERSION'] ." ".$patch_string;?></span>&nbsp;|&nbsp;
									<a href="javascript:;" onClick=" newpopup('http://www.vtiger.com/copyrights/LICENSE_AGREEMENT.txt')"><?php echo $mod_strings['LBL_READ_LICENSE'];?></a>&nbsp;|&nbsp;
									<a href="javascript:;" onClick=" newpopup('http://www.vtiger.com/products/crm/privacy_policy.html')"><?php echo $app_strings['LNK_PRIVACY_POLICY'];?></a>&nbsp;|&nbsp;
									<a href="javascript:;" onClick=" newpopup('http://www.vtiger.com/index.php?option=com_content&task=view&id=26&Itemid=54')"><?php echo $mod_strings['LBL_CONTACT_US'];?></a>
							</td>
							<td align="right">
									<input type="button" name="close" value=" &nbsp;<?php echo $mod_strings['LBL_CLOSE'];?>&nbsp; " onClick="window.close();" class="crmbutton small cancel">
							</td>
						</tr>
				</table>
		  </td>
  </tr>
</table>
</body>
</html>
