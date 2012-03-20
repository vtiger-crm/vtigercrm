<?php 
// phpSysInfo - A PHP System Information Script
// http://phpsysinfo.sourceforge.net/
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// $Id: index.php,v 1.113 2006/04/17 15:24:46 bigmichi1 Exp $
// phpsysinfo release version number
$VERSION = "2.5.2_rc2";
$startTime = array_sum( explode( " ", microtime() ) );
global $app_strings;


define('IN_PHPSYSINFO', true);

ini_set('magic_quotes_runtime', 'off');
ini_set('register_globals', 'off');
// ini_set('display_errors','on');
define('APP_ROOT', getcwd().'/modules/System');
require_once(APP_ROOT . '/includes/class.error.inc.php');
$error = new Error;

// Figure out which OS where running on, and detect support
if ( file_exists( APP_ROOT . '/includes/os/class.' . PHP_OS . '.inc.php' ) ) {
} else {
  $error->addError('include(class.' . PHP_OS . '.php.inc)' , PHP_OS . ' is not currently supported', __LINE__, __FILE__ );
}

if (!extension_loaded('xml')) {
  $error->addError('extension_loaded(xml)', 'phpsysinfo requires the xml module for php to work', __LINE__, __FILE__);
} 
if (!extension_loaded('pcre')) {
  $error->addError('extension_loaded(pcre)', 'phpsysinfo requires the pcre module for php to work', __LINE__, __FILE__);
} 

if (!file_exists(APP_ROOT . '/config.php')) {
  $error->addError('file_exists(config.php)', 'config.php does not exist in the phpsysinfo directory.', __LINE__, __FILE__);
} else { 
  require_once(APP_ROOT . '/config.php'); 			// get the config file
}

if ( !empty( $sensor_program ) ) {
  $sensor_program = basename( $sensor_program );
  if( !file_exists( APP_ROOT . '/includes/mb/class.' . $sensor_program . '.inc.php' ) ) {
    $error->addError('include(class.' . htmlspecialchars($sensor_program, ENT_QUOTES) . '.inc.php)', 'specified sensor programm is not supported', __LINE__, __FILE__ );
  } 
} 

if ( !empty( $hddtemp_avail ) && $hddtemp_avail != "tcp" && $hddtemp_avail != "suid" ) {
  $error->addError('include(class.hddtemp.inc.php)', 'bad configuration in config.php for $hddtemp_avail', __LINE__, __FILE__ );
}

if( $error->ErrorsExist() ) {
  echo $error->ErrorsAsHTML();
  exit;
}

require_once(APP_ROOT . '/includes/common_functions.php'); 	// Set of common functions used through out the app

// commented for security
// Check to see if where running inside of phpGroupWare
//if (file_exists("../header.inc.php") && isset($_REQUEST['sessionid']) && $_REQUEST['sessionid'] && $_REQUEST['kp3'] && $_REQUEST['domain']) {
//  define('PHPGROUPWARE', 1);
//  $phpgw_info['flags'] = array('currentapp' => 'phpsysinfo-dev');
//  include('../header.inc.php');
//} else {
//  define('PHPGROUPWARE', 0);
//}

// DEFINE TEMPLATE_SET
if (isset($_POST['template'])) {
  $template = $_POST['template'];
} elseif (isset($_GET['template'])) {
  $template = $_GET['template'];
} elseif (isset($_COOKIE['template'])) {
  $template = $_COOKIE['template'];
} else {
  $template = $default_template; 
}

// check to see if we have a random
if ($template == 'random') {
  $buf = gdc( APP_ROOT . "/templates/" );
  $template = $buf[array_rand($buf, 1)];
}

if ($template != 'xml' && $template != 'wml') {
  // figure out if the template exists
 $template = basename(APP_ROOT .'/templates/' . $template);
  if (!file_exists(APP_ROOT . "/templates/" . $template)) {
    // use default if not exists.
    $template = $default_template;
  }
  // Store the current template name in a cookie, set expire date to 30 days later
  // if template is xml then skip
  @setcookie("template", $template, (time() + 60 * 60 * 24 * 30));
  $_COOKIE['template'] = $template; //update COOKIE Var
}

// get our current language
// default to english, but this is negotiable.
if ($template == "wml") {
  $lng = "en";
} elseif (isset($_POST['lng'])) {
  $lng = $_POST['lng'];
} elseif (isset($_GET['lng'])) {
  $lng = $_GET['lng'];
} elseif (isset($_COOKIE['lng'])) {
  $lng = $_COOKIE['lng'];
} else {
  $lng = $default_lng;
} 

if ($lng == 'browser') {
  // see if the browser knows the right languange.
  if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $plng = split(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    if (count($plng) > 0) {
      while (list($k, $v) = each($plng)) {
        $k = split(';', $v, 1);
        $k = split('-', $k[0]);
        if (file_exists(APP_ROOT . '/includes/lang/' . $k[0] . '.php')) {
          $lng = $k[0];
          break;
        }
      }
    }
  }
}

$lng = basename($lng);
if (file_exists(APP_ROOT . '/includes/lang/' . $lng . '.php')) {
  $charset = $app_strings['LBL_CHARSET'];
  require_once(APP_ROOT . '/includes/lang/' . $lng . '.php'); // get our language include
  // Store the current language selection in a cookie, set expire date to 30 days later
 @setcookie("lng", $lng, (time() + 60 * 60 * 24 * 30));
  $_COOKIE['lng'] = $lng; //update COOKIE Var
} else {
  $error->addError('include(' . $lng . ')', 'we do not support this language', __LINE__, __FILE__ );
  $lng = $default_lng;
}

// include the files and create the instances
define('TEMPLATE_SET', $template);
require_once( APP_ROOT . '/includes/os/class.' . PHP_OS . '.inc.php' );
$sysinfo = new sysinfo;
if( !empty( $sensor_program ) ) {
  require_once(APP_ROOT . '/includes/mb/class.' . $sensor_program . '.inc.php');
  $mbinfo = new mbinfo;
}
if ( !empty($hddtemp_avail ) ) {
  require_once(APP_ROOT . '/includes/mb/class.hddtemp.inc.php');
}

require_once(APP_ROOT . '/includes/xml/vitals.php');
require_once(APP_ROOT . '/includes/xml/network.php');
require_once(APP_ROOT . '/includes/xml/hardware.php');
require_once(APP_ROOT . '/includes/xml/memory.php');
require_once(APP_ROOT . '/includes/xml/filesystems.php');
require_once(APP_ROOT . '/includes/xml/mbinfo.php');
require_once(APP_ROOT . '/includes/xml/hddtemp.php');

// build the xml
$xml = "<?xml version=\"1.0\" encoding=\"".$app_strings['LBL_CHARSET']."\"?>\n";
$xml .= "<!DOCTYPE phpsysinfo SYSTEM \"phpsysinfo.dtd\">\n\n";
$xml .= created_by();
$xml .= "<phpsysinfo>\n";
$xml .= "  <Generation version=\"$VERSION\" timestamp=\"" . time() . "\"/>\n";
$xml .= xml_vitals();
$xml .= xml_network();
$xml .= xml_hardware($hddtemp_devices);
$xml .= xml_memory();
$xml .= xml_filesystems();
if ( !empty( $sensor_program ) ) {
  $xml .= xml_mbtemp();
  $xml .= xml_mbfans();
  $xml .= xml_mbvoltage();
};
if ( !empty($hddtemp_avail ) ) {
  $hddtemp = new hddtemp($hddtemp_devices);
  $xml .= xml_hddtemp($hddtemp);
}
$xml .= "</phpsysinfo>";
replace_specialchars($xml);

// output
if (TEMPLATE_SET == 'xml') {
  // just printout the XML and exit
  header("Content-Type: text/xml\n\n");
  print $xml;
} elseif (TEMPLATE_SET == 'wml') {
  require_once(APP_ROOT . '/includes/XPath.class.php');
  $XPath = new XPath();
  $XPath->importFromString($xml); 

  header("Content-type: text/vnd.wap.wml; charset=".$app_strings['LBL_CHARSET']);
  header("");
  header("Cache-Control: no-cache, must-revalidate");
  header("Pragma: no-cache");

  echo "<?xml version='1.0' encoding='".$app_strings['LBL_CHARSET']."'?>\n";
  echo "<!DOCTYPE wml PUBLIC \"-//WAPFORUM//DTD WML 1.1//EN\" \"http://www.wapforum.org/DTD/wml_1.1.xml\" >\n";
  echo "<wml>\n";
  echo "<card id=\"start\" title=\"phpSysInfo - Menu\">\n";
  echo "<p><a href=\"#vitals\">" . $text['vitals'] . "</a></p>\n";
  echo "<p><a href=\"#network\">" . $text['netusage'] . "</a></p>\n";
  echo "<p><a href=\"#memory\">" . $text['memusage'] . "</a></p>\n";
  echo "<p><a href=\"#filesystem\">" . $text['fs'] . "</a></p>\n";
  if (!empty($sensor_program) || (isset($hddtemp_avail) && $hddtemp_avail)) {
    echo "<p><a href=\"#temp\">" . $text['temperature'] . "</a></p>\n";
  }
  if (!empty($sensor_program)) {
    echo "<p><a href=\"#fans\">" . $text['fans'] . "</a></p>\n";
    echo "<p><a href=\"#volt\">" . $text['voltage'] . "</a></p>\n";
  }
  echo "</card>\n";
  echo wml_vitals();
  echo wml_network();
  echo wml_memory();
  echo wml_filesystem();
  
  $temp = "";
  if (!empty($sensor_program)) {
    echo wml_mbfans();
    echo wml_mbvoltage();
    $temp .= wml_mbtemp();
  }
  if (isset($hddtemp_avail) && $hddtemp_avail)
    if ($XPath->match("/phpsysinfo/HDDTemp/Item"))
      $temp .= wml_hddtemp();
  if(strlen($temp) > 0)
    echo "<card id=\"temp\" title=\"" . $text['temperature'] . "\">" . $temp . "</card>";
  echo "</wml>\n";

} else {
  $image_height = get_gif_image_height(APP_ROOT . '/templates/' . TEMPLATE_SET . '/images/bar_middle.gif');
  define('BAR_HEIGHT', $image_height);

//  if (PHPGROUPWARE != 1) {
    require_once(APP_ROOT . '/includes/class.Template.inc.php'); // template library
//  } 
  // fire up the template engine
  $tpl = new Template(APP_ROOT . '/templates/' . TEMPLATE_SET);
  $tpl->set_file(array('form' => 'form.tpl')); 
  // print out a box of information
  function makebox ($title, $content)
  {
    if (empty($content)) {
      return "";
    } else {
      global $webpath;
      $textdir = direction();
      $t = new Template(APP_ROOT . '/templates/' . TEMPLATE_SET);
      $t->set_file(array('box' => 'box.tpl'));
      $t->set_var('title', $title);
      $t->set_var('content', $content);
      $t->set_var('webpath', $webpath);
      $t->set_var('text_dir', $textdir['direction']);
      return $t->parse('out', 'box');
    } 
  } 
  // Fire off the XPath class
  require_once(APP_ROOT . '/includes/XPath.class.php');
  $XPath = new XPath();
  $XPath->importFromString($xml); 
  // let the page begin.
  require_once(APP_ROOT . '/includes/system_header.php');

  if ( $error->ErrorsExist() && isset($showerrors) && $showerrors ) {
    $tpl->set_var('errors', makebox("ERRORS", $error->ErrorsAsHTML() ));
  }

  $tpl->set_var('title', $text['title'] . ': ' . $XPath->getData('/phpsysinfo/Vitals/Hostname') . ' (' . $XPath->getData('/phpsysinfo/Vitals/IPAddr') . ')');
  $tpl->set_var('vitals', makebox($text['vitals'], html_vitals()));
  $tpl->set_var('network', makebox($text['netusage'], html_network()));
  $tpl->set_var('hardware', makebox($text['hardware'], html_hardware()));
  $tpl->set_var('memory', makebox($text['memusage'], html_memory()));
  $tpl->set_var('filesystems', makebox($text['fs'], html_filesystems()));
  // Timo van Roermund: change the condition for showing the temperature, voltage and fans section
  $html_temp = "";
  if (!empty($sensor_program)) {
    if ($XPath->match("/phpsysinfo/MBinfo/Temperature/Item")) {
      $html_temp = html_mbtemp();
    }
    if ($XPath->match("/phpsysinfo/MBinfo/Fans/Item")) {
      $tpl->set_var('mbfans', makebox($text['fans'], html_mbfans()));
    } else {
      $tpl->set_var('mbfans', '');
    };
    if ($XPath->match("/phpsysinfo/MBinfo/Voltage/Item")) {
      $tpl->set_var('mbvoltage', makebox($text['voltage'], html_mbvoltage()));
    } else {
      $tpl->set_var('mbvoltage', '');
    };
  }
  if (isset($hddtemp_avail) && $hddtemp_avail) {
    if ($XPath->match("/phpsysinfo/HDDTemp/Item")) {
      $html_temp .= html_hddtemp();
    };
  }
  if (strlen($html_temp) > 0) {
    $tpl->set_var('mbtemp', makebox($text['temperature'], "\n<table width=\"100%\">\n" . $html_temp . "</table>\n"));
  }
  
  // parse our the template
  $tpl->pfp('out', 'form'); 
 
  // finally our print our footer
//  if (PHPGROUPWARE == 1) {
//    $phpgw->common->phpgw_footer();
//  } else {
  require_once(APP_ROOT . '/includes/system_footer.php');
//  } 
} 

?>
