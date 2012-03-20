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
// $Id: common_functions.php,v 1.42 2006/04/17 13:03:38 bigmichi1 Exp $
// HTML/XML Comment
function created_by ()
{
  global $VERSION;
  return "<!--\n\tCreated By: phpSysInfo - $VERSION\n\thttp://phpsysinfo.sourceforge.net/\n-->\n\n";
} 
// usefull during development
error_reporting(E_ALL | E_NOTICE);
// print out the bar graph
// $value as full percentages
// $maximim as current maximum 
// $b as scale factor
// $type as filesystem type
function create_bargraph ($value, $maximum, $b, $type = "")
{
  global $webpath;
  
  $textdir = direction();

  $imgpath = 'modules/System/templates/' . TEMPLATE_SET . '/images/';
  $maximum == 0 ? $barwidth = 0 : $barwidth = round((100  / $maximum) * $value) * $b;
  $red = 90 * $b;
  $yellow = 75 * $b;

  if (!file_exists("/modules/System/templates/" . TEMPLATE_SET . "/images/nobar_left.gif")) {
    if ($barwidth == 0) {
      return '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'bar_' . $textdir['left'] . '.gif" alt="">' 
           . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'bar_middle.gif" width="1" alt="">' 
	   . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'bar_' . $textdir['right'] . '.gif" alt="">';
    } elseif (file_exists("/modules/System/templates/" . TEMPLATE_SET . "/images/yellowbar_left.gif") && $barwidth > $yellow && $barwidth < $red) {
      return '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'yellowbar_' . $textdir['left'] . '.gif" alt="">' 
           . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'yellowbar_middle.gif" width="' . $barwidth . '" alt="">' 
	   . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'yellowbar_' . $textdir['right'] . '.gif" alt="">';
    } elseif (($barwidth < $red) || ($type == "iso9660") || ($type == "CDFS")) {
      return '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'bar_' . $textdir['left'] . '.gif" alt="">' 
           . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'bar_middle.gif" width="' . $barwidth . '" alt="">' 
	   . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'bar_' . $textdir['right'] . '.gif" alt="">';
    } else {
      return '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'redbar_' . $textdir['left'] . '.gif" alt="">' 
           . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'redbar_middle.gif" width="' . $barwidth . '" alt="">' 
	   . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'redbar_' . $textdir['right'] . '.gif" alt="">';
    }
  } else {
    if ($barwidth == 0) {
      return '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'nobar_' . $textdir['left'] . '.gif" alt="">' 
           . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'nobar_middle.gif" width="' . (100 * $b) . '" alt="">' 
	   . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'nobar_' . $textdir['right'] . '.gif" alt="">';
    } elseif (file_exists("/modules/System/templates/" . TEMPLATE_SET . "/images/yellowbar_left.gif") && $barwidth > $yellow && $barwidth < $red) {
      return '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'yellowbar_' . $textdir['left'] . '.gif" alt="">' 
           . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'yellowbar_middle.gif" width="' . $barwidth . '" alt="">' 
	   . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'nobar_middle.gif" width="' . ((100 * $b) - $barwidth) . '" alt="">' 
	   . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'nobar_' . $textdir['right'] . '.gif" alt="">';
    } elseif (($barwidth < $red) || $type == "iso9660" || ($type == "CDFS")) {
      return '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'bar_' . $textdir['left'] . '.gif" alt="">' 
           . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'bar_middle.gif" width="' . $barwidth . '" alt="">' 
	   . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'nobar_middle.gif" width="' . ((100 * $b) - $barwidth) . '" alt="">' 
	   . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'nobar_' . $textdir['right'] . '.gif" alt="">';
    } elseif ($barwidth == (100 * $b)) {
      return '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'redbar_' . $textdir['left'] . '.gif" alt="">' 
           . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'redbar_middle.gif" width="' . (100 * $b) . '" alt="">' 
	   . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'redbar_' . $textdir['right'] . '.gif" alt="">';
    } else {
      return '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'redbar_' . $textdir['left'] . '.gif" alt="">' 
           . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'redbar_middle.gif" width="' . $barwidth . '" alt="">' 
	   . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'nobar_middle.gif" width="' . ((100 * $b) - $barwidth) . '" alt="">' 
	   . '<img height="' . BAR_HEIGHT . '" src="' . $imgpath . 'nobar_' . $textdir['right'] . '.gif" alt="">';
    }
  }
} 

function create_bargraph_grad( $value, $maximum, $b, $type = "" ) {
  global $webpath;

  $maximum == 0 ? $barwidth = 0 : $barwidth = round((100  / $maximum) * $value);
  $startColor = '0ef424'; // green
  $endColor = 'ee200a'; // red
  if ($barwidth > 100) {
    $barwidth = 0;
  }

  return '<img height="' . BAR_HEIGHT . '" width="300" src="' . $webpath . 'includes/indicator.php?height=' . BAR_HEIGHT . '&amp;percent=' . $barwidth . '&amp;color1=' . $startColor . '&amp;color2=' . $endColor . '" alt="">';
}

function direction() {
  global $text_dir;

  if(!isset($text_dir) || $text_dir == "ltr") {
    $result['direction'] = "ltr";
    $result['left'] = "left";
    $result['right'] = "right";
  } else {
    $result['direction'] = "rtl";
    $result['left'] = "right";
    $result['right'] = "left";
  }
  
  return $result;
}

// Find a system program.  Do path checking
function find_program ($program)
{
  global $addpaths;

  $path = array('/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin');

  if( isset($addpaths) && is_array($addpaths) ) {
    $path = array_merge( $path, $addpaths );
  }

  if (function_exists("is_executable")) {
    while ($this_path = current($path)) {
      if (is_executable("$this_path/$program")) {
        return "$this_path/$program";
      } 
      next($path);
    } 
  } else {
    return strpos($program, '.exe');
  } ;

  return;
} 
// Execute a system program. return a trim()'d result.
// does very crude pipe checking.  you need ' | ' for it to work
// ie $program = execute_program('netstat', '-anp | grep LIST');
// NOT $program = execute_program('netstat', '-anp|grep LIST');
function execute_program ($programname, $args = '', $booErrorRep = true )
{
  global $error;
  $buffer = '';
  $program = find_program($programname);

  if (!$program) {
    if( $booErrorRep ) {
      $error->addError( 'find_program(' . $programname . ')', 'program not found on the machine', __LINE__, __FILE__);
    }
    return;
  } 
  
  // see if we've gotten a |, if we have we need to do patch checking on the cmd
  if ($args) {
    $args_list = split(' ', $args);
    for ($i = 0; $i < count($args_list); $i++) {
      if ($args_list[$i] == '|') {
        $cmd = $args_list[$i + 1];
        $new_cmd = find_program($cmd);
        $args = ereg_replace("\| $cmd", "| $new_cmd", $args);
      } 
    } 
  } 
  // we've finally got a good cmd line.. execute it
  if ($fp = popen("($program $args > /dev/null) 3>&1 1>&2 2>&3", 'r')) {
    while (!feof($fp)) {
      $buffer .= fgets($fp, 4096);
    }
    pclose($fp);
    $buffer = trim($buffer);
    if (!empty($buffer)) {
      if( $booErrorRep ) {
        $error->addError( $program, $buffer, __LINE__, __FILE__);
      }
    }
  }
  if ($fp = popen("$program $args", 'r')) {
    $buffer = "";
    while (!feof($fp)) {
      $buffer .= fgets($fp, 4096);
    }
    pclose($fp);
  }
  $buffer = trim($buffer);
  return $buffer;
} 

// A helper function, when passed a number representing KB,
// and optionally the number of decimal places required,
// it returns a formated number string, with unit identifier.
function format_bytesize ($kbytes, $dec_places = 2)
{
  global $text;
  $spacer = '&nbsp;';
  if ($kbytes > 1048576) {
    $result = sprintf('%.' . $dec_places . 'f', $kbytes / 1048576);
    $result .= $spacer . $text['gb'];
  } elseif ($kbytes > 1024) {
    $result = sprintf('%.' . $dec_places . 'f', $kbytes / 1024);
    $result .= $spacer . $text['mb'];
  } else {
    $result = sprintf('%.' . $dec_places . 'f', $kbytes);
    $result .= $spacer . $text['kb'];
  } 
  return $result;
} 

function get_gif_image_height($image)
{ 
  // gives the height of the given GIF image, by reading it's LSD (Logical Screen Discriptor)
  // by Edwin Meester aka MillenniumV3
  // Header: 3bytes 	Discription
  // 3bytes 	Version
  // LSD:		2bytes 	Logical Screen Width
  // 2bytes 	Logical Screen Height
  // 1bit 		Global Color Table Flag
  // 3bits   Color Resolution
  // 1bit		Sort Flag
  // 3bits		Size of Global Color Table
  // 1byte		Background Color Index
  // 1byte		Pixel Aspect Ratio
  // Open Image
  $fp = fopen($image, 'rb'); 
  // read Header + LSD
  $header_and_lsd = fread($fp, 13);
  fclose($fp); 
  // calc Height from Logical Screen Height bytes
  $result = ord($header_and_lsd{8}) + ord($header_and_lsd{9}) * 255;
  return $result;
} 

// Check if a string exist in the global $hide_mounts.
// Return true if this is the case.
function hide_mount($mount) {
	global $hide_mounts;
	if (isset($hide_mounts) && is_array($hide_mounts) && in_array($mount, $hide_mounts)) {
		return true;
	}
	else {
		return false;
	}
}

function uptime($timestamp) {
  global $text;
  $uptime = '';
    
  $min = $timestamp / 60;
  $hours = $min / 60;
  $days = floor($hours / 24);
  $hours = floor($hours - ($days * 24));
  $min = floor($min - ($days * 60 * 24) - ($hours * 60));

  if ($days != 0) {
    $uptime .= $days. "&nbsp;" . $text['days'] . "&nbsp;";
  }

  if ($hours != 0) {
    $uptime .= $hours . "&nbsp;" . $text['hours'] . "&nbsp;";
  }
  
  $uptime .= $min . "&nbsp;" . $text['minutes'];
  return $uptime;
}

//Replace some chars which are not valid in xml with iso-8859-1 encoding
function replace_specialchars(&$xmlstring) {
    $xmlstring = str_replace( chr(174), "(R)", $xmlstring );
    $xmlstring = str_replace( chr(169), "(C)", $xmlstring );
}

// find duplicate entrys and count them, show this value befor the duplicated name
function finddups( $arrInput ) {
  $result = array();
  //Pinaki: Fix for ticket #4462
  if($arrInput!=null)
  {
  	$buffer = array_count_values($arrInput);
  	foreach ($buffer as $key => $value) {
    	if( $value > 1 ) {
      		$result[] = "(" . $value . "x) " . $key;
    	} else {
      		$result[] = $key;
    	}
  	}	
  }
  return $result;
}

function rfts( $strFileName, $intLines = 0, $intBytes = 4096, $booErrorRep = true ) {
  global $error;
  $strFile = "";
  $intCurLine = 1;
  
  if( file_exists( $strFileName ) ) {
    if( $fd = fopen( $strFileName, 'r' ) ) {
      while( !feof( $fd ) ) {
        $strFile .= fgets( $fd, $intBytes );
	if( $intLines <= $intCurLine && $intLines != 0 ) {
	  break;
	} else {
	  $intCurLine++;
	}
      }
      fclose( $fd );
    } else {
      if( $booErrorRep ) {
        $error->addError( 'fopen(' . $strFileName . ')', 'file can not read by phpsysinfo', __LINE__, __FILE__ );
      }
      return "ERROR";
    }
  } else {
    if( $booErrorRep ) {
      $error->addError( 'file_exists(' . $strFileName . ')', 'the file does not exist on your machine', __LINE__, __FILE__ );
    }
    return "ERROR";
  }

  return $strFile;
}

function gdc( $strPath, $booErrorRep = true ) {
  global $error;
  $arrDirectoryContent = array();
  
  if( is_dir( $strPath ) ) {
    if( $handle = opendir( $strPath ) ) {
      while( ( $strFile = readdir( $handle ) ) !== false ) {
        if( $strFile != "." && $strFile != ".." && $strFile != "CVS" ) {
          $arrDirectoryContent[] = $strFile;
	}
      }
      closedir( $handle );
    } else {
      if( $booErrorRep ) {
        $error->addError( 'opendir(' . $strPath . ')', 'directory can not be read by phpsysinfo', __LINE__, __FILE__ );
      }
    }
  } else {
    if( $booErrorRep ) {
      $error->addError( 'is_dir(' . $strPath . ')', 'directory does not exist on your machine', __LINE__, __FILE__ );
    }
  }
  
  return $arrDirectoryContent;
}
?>
