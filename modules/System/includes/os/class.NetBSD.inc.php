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

// $Id: class.NetBSD.inc.php,v 1.18 2006/04/18 16:57:32 bigmichi1 Exp $
if (!defined('IN_PHPSYSINFO')) {
    die("No Hacking");
}

require_once(APP_ROOT . '/includes/os/class.BSD.common.inc.php');

class sysinfo extends bsd_common {
  var $cpu_regexp;
  var $scsi_regexp; 
  // Our contstructor
  // this function is run on the initialization of this class
  function sysinfo () {
    $this->bsd_common();
    
    $this->cpu_regexp = "^cpu(.*)\, (.*) MHz";
    $this->scsi_regexp1 = "^(.*) at scsibus.*: <(.*)> .*";
    $this->scsi_regexp2 = "^(da[0-9]): (.*)MB ";
    $this->cpu_regexp2 = "/user = (.*), nice = (.*), sys = (.*), intr = (.*), idle = (.*)/";
    $this->pci_regexp1 = '/(.*) at pci[0-9] dev [0-9]* function [0-9]*: (.*)$/';
    $this->pci_regexp2 = '/"(.*)" (.*).* at [.0-9]+ irq/';
  } 

  function get_sys_ticks () {
    $a = $this->grab_key('kern.boottime');
    $sys_ticks = time() - $a;
    return $sys_ticks;
  } 

  function network () {
    $netstat_b = execute_program('netstat', '-nbdi | cut -c1-25,44- | grep "^[a-z]*[0-9][ \t].*Link"');
    $netstat_n = execute_program('netstat', '-ndi | cut -c1-25,44- | grep "^[a-z]*[0-9][ \t].*Link"');
    $lines_b = split("\n", $netstat_b);
    $lines_n = split("\n", $netstat_n);
    $results = array();
    for ($i = 0, $max = sizeof($lines_b); $i < $max; $i++) {
      $ar_buf_b = preg_split("/\s+/", $lines_b[$i]);
      $ar_buf_n = preg_split("/\s+/", $lines_n[$i]);
      if (!empty($ar_buf_b[0]) && !empty($ar_buf_n[3])) {
        $results[$ar_buf_b[0]] = array();

        $results[$ar_buf_b[0]]['rx_bytes'] = $ar_buf_b[3];
        $results[$ar_buf_b[0]]['rx_packets'] = $ar_buf_n[3];
        $results[$ar_buf_b[0]]['rx_errs'] = $ar_buf_n[4];
        $results[$ar_buf_b[0]]['rx_drop'] = $ar_buf_n[8];

        $results[$ar_buf_b[0]]['tx_bytes'] = $ar_buf_b[4];
        $results[$ar_buf_b[0]]['tx_packets'] = $ar_buf_n[5];
        $results[$ar_buf_b[0]]['tx_errs'] = $ar_buf_n[6];
        $results[$ar_buf_b[0]]['tx_drop'] = $ar_buf_n[8];

        $results[$ar_buf_b[0]]['errs'] = $ar_buf_n[4] + $ar_buf_n[6];
        $results[$ar_buf_b[0]]['drop'] = $ar_buf_n[8];
      } 
    } 
    return $results;
  } 

  // get the ide device information out of dmesg
  function ide () {
    $results = array();

    $s = 0;
    for ($i = 0, $max = count($this->read_dmesg()); $i < $max; $i++) {
      $buf = $this->dmesg[$i];
      if (preg_match('/^(.*) at (pciide|wdc|atabus|atapibus)[0-9] (.*): <(.*)>/', $buf, $ar_buf)) {
        $s = $ar_buf[1];
        $results[$s]['model'] = $ar_buf[4];
        $results[$s]['media'] = 'Hard Disk'; 
        // now loop again and find the capacity
        for ($j = 0, $max1 = count($this->read_dmesg()); $j < $max1; $j++) {
          $buf_n = $this->dmesg[$j];
          if (preg_match("/^($s): (.*), (.*), (.*)MB, .*$/", $buf_n, $ar_buf_n)) {
            $results[$s]['capacity'] = $ar_buf_n[4] * 2048 * 1.049;
          } elseif (preg_match("/^($s): (.*) MB, (.*), (.*), .*$/", $buf_n, $ar_buf_n)) {
	    $results[$s]['capacity'] = $ar_buf_n[2] * 2048;
	  }
        } 
      } 
    } 
    asort($results);
    return $results;
  } 

  function distroicon () {
    $result = 'NetBSD.png';
    return($result);
  }
  
} 

?>
