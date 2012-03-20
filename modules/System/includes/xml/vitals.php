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

// $Id: vitals.php,v 1.27 2005/12/31 17:25:26 bigmichi1 Exp $

// xml_vitals()

function xml_vitals ()
{
  global $sysinfo;
  global $loadbar;

  $load_avg = "";
  $ar_buf = ($loadbar ? $sysinfo->loadavg($loadbar) : $sysinfo->loadavg());

  for ($i = 0; $i < count($ar_buf['avg']); $i++) {
    $load_avg .= $ar_buf['avg'][$i] . ' ';
  } 

  $_text = "  <Vitals>\n"
   . "    <Hostname>" . htmlspecialchars($sysinfo->chostname(), ENT_QUOTES) . "</Hostname>\n"
   . "    <IPAddr>" . htmlspecialchars($sysinfo->ip_addr(), ENT_QUOTES) . "</IPAddr>\n"
   . "    <Kernel>" . htmlspecialchars($sysinfo->kernel(), ENT_QUOTES) . "</Kernel>\n"
   . "    <Distro>" . htmlspecialchars($sysinfo->distro(), ENT_QUOTES) . "</Distro>\n"
   . "    <Distroicon>" . htmlspecialchars($sysinfo->distroicon(), ENT_QUOTES) . "</Distroicon>\n"
   . "    <Uptime>" . htmlspecialchars($sysinfo->uptime(), ENT_QUOTES) . "</Uptime>\n"
   . "    <Users>" . htmlspecialchars($sysinfo->users(), ENT_QUOTES) . "</Users>\n"
   . "    <LoadAvg>" . htmlspecialchars(trim($load_avg), ENT_QUOTES) . "</LoadAvg>\n";
   if (isset($ar_buf['cpupercent']))
     $_text .= "   <CPULoad>" . htmlspecialchars(round($ar_buf['cpupercent'], 2), ENT_QUOTES) . "</CPULoad>";
   $_text .= "  </Vitals>\n";
  return $_text;
} 

// html_vitals()

function html_vitals ()
{
  global $webpath;
  global $XPath;
  global $text;

  $textdir = direction();
  $scale_factor = 2;
  $loadbar = "";
  $uptime = "";
  
  if($XPath->match("/phpsysinfo/Vitals/CPULoad"))
    $loadbar = "<br/>" . create_bargraph($XPath->getData("/phpsysinfo/Vitals/CPULoad"), 100, $scale_factor) . "&nbsp;" . $XPath->getData("/phpsysinfo/Vitals/CPULoad") . "%";

  $_text = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\" "
	 . "  <tr>\n"
	 . "    <td width=20% align=right class=\"cellLabel small\" valign=\"top\"><font size=\"-1\">" . $text['hostname'] . "</font></td>\n"
	 . "    <td width=80% class=\"cellText small\"><font size=\"-1\">" . $XPath->getData("/phpsysinfo/Vitals/Hostname") . "</font></td>\n"
	 . "  </tr>\n"
	 . "  <tr>\n"
	 . "    <td align=right class=\"cellLabel small\" valign=\"top\"><font size=\"-1\">" . $text['ip'] . "</font></td>\n"
	 . "    <td class=\"cellText small\"><font size=\"-1\">" . $XPath->getData("/phpsysinfo/Vitals/IPAddr") . "</font></td>\n"
	 . "  </tr>\n"
	 . "  <tr>\n"
	 . "    <td align=right  class=\"cellLabel small\" valign=\"top\"><font size=\"-1\">" . $text['kversion'] . "</font></td>\n"
	 . "    <td class=\"cellText small\"><font size=\"-1\">" . $XPath->getData("/phpsysinfo/Vitals/Kernel") . "</font></td>\n"
	 . "  </tr>\n"
	 . "  <tr>\n"
	 . "    <td align=right class=\"cellLabel small\" valign=\"top\"><font size=\"-1\">" . $text['dversion'] . "</font></td>\n"
	 . "    <td class=\"cellText small\"><img width=\"16\" height=\"16\" alt=\"\" src=\"modules/System/images/" . $XPath->getData("/phpsysinfo/Vitals/Distroicon") . "\">&nbsp;<font size=\"-1\">" . $XPath->getData("/phpsysinfo/Vitals/Distro") . "</font></td>\n"
	 . "  </tr>\n"
	 . "  <tr>\n"
	 . "    <td align=right  class=\"cellLabel small\" valign=\"top\"><font size=\"-1\">" . $text['uptime'] . "</font></td>\n"
	 . "    <td class=\"cellText small\"><font size=\"-1\">" . uptime($XPath->getData('/phpsysinfo/Vitals/Uptime')) . "</font></td>\n"
	 . "  </tr>\n"
	 . "  <tr>\n"
	 . "    <td align=right class=\"cellLabel small\" valign=\"top\"><font size=\"-1\">" . $text['users'] . "</font></td>\n"
	 . "    <td class=\"cellText small\"><font size=\"-1\">" . $XPath->getData("/phpsysinfo/Vitals/Users") . "</font></td>\n"
	 . "  </tr>\n"
	 . "  <tr>\n"
	 . "    <td align=right class=\"cellLabel small\" valign=\"top\"><font size=\"-1\">" . $text['loadavg'] . "</font></td>\n"
	 . "    <td class=\"cellText small\"><font size=\"-1\">" . $XPath->getData("/phpsysinfo/Vitals/LoadAvg") . $loadbar . "</font></td>\n"
	 . "  </tr>\n"
	 . "</table>\n";

  return $_text;
} 

function wml_vitals ()
{
  global $XPath;
  global $text;
  
  $_text = "<card id=\"vitals\" title=\"" . $text['vitals']  . "\">\n"
         . "<p>" . $text['hostname'] . ":<br/>\n"
 	 . "- " . $XPath->getData("/phpsysinfo/Vitals/Hostname") . "</p>\n"
	 . "<p>" . $text['ip'] . ":<br/>\n"
	 . "- " . $XPath->getData("/phpsysinfo/Vitals/IPAddr") . "</p>\n"
	 . "<p>" . $text['kversion'] . ":<br/>\n"
	 . "- " . $XPath->getData("/phpsysinfo/Vitals/Kernel") . "</p>\n"
	 . "<p>" . $text['uptime'] . ":<br/>\n"
	 . "- " . uptime($XPath->getData('/phpsysinfo/Vitals/Uptime')) . "</p>"
	 . "<p>" . $text['users'] . ":<br/>"
	 . "- " . $XPath->getData("/phpsysinfo/Vitals/Users") . "</p>"
	 . "<p>" . $text['loadavg'] . ":<br/>"
	 . "- " . $XPath->getData("/phpsysinfo/Vitals/LoadAvg") . "</p>"
	 . "</card>\n";

  return $_text;
}
?>
