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
// WINNT implementation written by Carl C. Longnecker, longneck@iname.com
// $Id: class.WINNT.inc.bak.php 5728 2006-05-03 21:19:46Z allanbush $
class sysinfo {
  // winnt needs some special prep
  // $wmi holds the COM object that we pull all the WMI data from
  var $wmi; 
  // this constructor initialis the $wmi object
  function sysinfo ()
  {
    $this->wmi = new COM("WinMgmts:\\\\.");
  } 
  // get our apache SERVER_NAME or vhost
  function vhostname ()
  {
    if (! ($result = getenv('SERVER_NAME'))) {
      $result = 'N.A.';
    } 
    return $result;
  } 
  // get our canonical hostname
  function chostname ()
  {
    if (! ($result = getenv('SERVER_NAME'))) {
      $result = 'N.A.';
    } 
    return $result;
  } 
  // get the IP address of our canonical hostname
  function ip_addr ()
  {
    if (!($result = getenv('SERVER_ADDR'))) {
      $result = 'N.A.';
    } 
    return $result;
  } 

  function kernel ()
  {
    $objInstance = $this->wmi->InstancesOf("Win32_OperatingSystem");
    foreach ($objInstance as $obj) {
      $result = $obj->Version;
      if ($obj->ServicePackMajorVersion > 0) {
        $result .= ' SP' . $obj->ServicePackMajorVersion;
      } 
    } 
    return $result;
  } 

  function uptime ()
  {
    $objInstance = $this->wmi->InstancesOf("Win32_OperatingSystem");
    foreach ($objInstance as $obj) {
      $result = 0;

      $year = intval(substr($obj->LastBootUpTime, 0, 4));
      $month = intval(substr($obj->LastBootUpTime, 4, 2));
      $day = intval(substr($obj->LastBootUpTime, 6, 2));
      $hour = intval(substr($obj->LastBootUpTime, 8, 2));
      $minute = intval(substr($obj->LastBootUpTime, 10, 2));
      $seconds = intval(substr($obj->LastBootUpTime, 12, 2));

      $boottime = mktime($hour, $minute, $seconds, $month, $day, $year);

      $diff_seconds = mktime() - $boottime;

      $result = $diff_seconds;
    } 
    return $result;
  } 

  function users ()
  {
    $objInstance = $this->wmi->InstancesOf("Win32_PerfRawData_TermService_TerminalServices");
    foreach ($objInstance as $obj) {
      return $obj->TotalSessions;
    } 
  } 

  function loadavg ()
  {
    $objInstance = $this->wmi->InstancesOf("Win32_Processor");

    $cpuload = array();
    foreach ($objInstance as $obj) {
      $cpuload[] = $obj->LoadPercentage;
    } 
    // while
    return $cpuload;
  } 

  function cpu_info ()
  {
    $objInstance = $this->wmi->InstancesOf("Win32_Processor");

    foreach ($objInstance as $obj) {
      // still need bogomips (wtf are bogomips?)
      $results['cpus'] = getenv('NUMBER_OF_PROCESSORS');
      $results['model'] = $obj->Name;
      $results['cache'] = $obj->L2CacheSize;
      $results['mhz'] = $obj->CurrentClockSpeed . "/" . $obj->ExtClock;
    } 
    return $results;
  } 

  function pci ()
  {
    $objInstance = $this->wmi->InstancesOf("Win32_PnPEntity");

    $pci = array();
    foreach ($objInstance as $obj) {
      if (substr($obj->PNPDeviceID, 0, 4) == "PCI\\") {
        $pci[] = $obj->Name;
      } 
    } // while
    return $pci;
  } 

  function ide ()
  {
    $objInstance = $this->wmi->InstancesOf("Win32_PnPEntity");

    $ide = array();
    foreach ($objInstance as $obj) {
      if (substr($obj->PNPDeviceID, 0, 4) == "IDE\\") {
        $ide[]['model'] = $obj->Name;
      } 
    } // while
    return $ide;
  } 

  function scsi ()
  {
    $objInstance = $this->wmi->InstancesOf("Win32_PnPEntity");

    $scsi = array();
    foreach ($objInstance as $obj) {
      if (substr($obj->PNPDeviceID, 0, 5) == "SCSI\\") {
        $scsi[] = $obj->Name;
      } 
    } // while
    return $scsi;
  } 

  function usb ()
  {
    $objInstance = $this->wmi->InstancesOf("Win32_PnPEntity");

    $usb = array();
    foreach ($objInstance as $obj) {
      if (substr($obj->PNPDeviceID, 0, 4) == "USB\\") {
        $usb[] = $obj->Name;
      } 
    } // while
    return $usb;
  } 

  function sbus ()
  {
    $objInstance = $this->wmi->InstancesOf("Win32_PnPEntity");

    $sbus = array();
    foreach ($objInstance as $obj) {
      if (substr($obj->PNPDeviceID, 0, 5) == "SBUS\\") {
        $sbus[] = $obj->Name;
      } 
    } // while
    return $sbus;
  } 

  function network ()
  {
    /**
    * need this for documentation in case i find some better net stats
    * $results[$dev_name]['rx_bytes'] = $stats[0];
    * $results[$dev_name]['rx_packets'] = $stats[1];
    * $results[$dev_name]['rx_errs'] = $stats[2];
    * $results[$dev_name]['rx_drop'] = $stats[3];
    * 
    * $results[$dev_name]['tx_bytes'] = $stats[8];
    * $results[$dev_name]['tx_packets'] = $stats[9];
    * $results[$dev_name]['tx_errs'] = $stats[10];
    * $results[$dev_name]['tx_drop'] = $stats[11];
    * 
    * $results[$dev_name]['errs'] = $stats[2] + $stats[10];
    * $results[$dev_name]['drop'] = $stats[3] + $stats[11];
    */

    $objInstance = $this->wmi->InstancesOf("Win32_PerfRawData_Tcpip_NetworkInterface");

    $results = array();
    foreach ($objInstance as $obj) {
      $results[$obj->Name]['errs'] = $obj->PacketsReceivedErrors;
      $results[$obj->Name]['drop'] = $obj->PacketsReceivedDiscarded;
    } // while
    return $results;
  } 

  function memory ()
  {
    $objInstance = $this->wmi->InstancesOf("Win32_LogicalMemoryConfiguration");
    foreach ($objInstance as $obj) {
      $results['ram']['total'] = $obj->TotalPhysicalMemory;
    } 
    $objInstance = $this->wmi->InstancesOf("Win32_PerfRawData_PerfOS_Memory");
    foreach ($objInstance as $obj) {
      $results['ram']['free'] = $obj->AvailableKBytes;
    } 
    $results['ram']['used'] = $results['ram']['total'] - $results['ram']['free'];
    $results['ram']['t_used'] = $results['ram']['used'];
    $results['ram']['t_free'] = $results['ram']['total'] - $results['ram']['t_used'];
    $results['ram']['percent'] = round(($results['ram']['t_used'] * 100) / $results['ram']['total']);

    $results['swap']['total'] = 0;
    $results['swap']['used'] = 0;
    $results['swap']['free'] = 0;

    $objInstance = $this->wmi->InstancesOf("Win32_PageFileUsage");

    $k = 0;
    foreach ($objInstance as $obj) {
      $results['devswap'][$k]['dev'] = $obj->Name;
      $results['devswap'][$k]['total'] = $obj->AllocatedBaseSize * 1024;
      $results['devswap'][$k]['used'] = $obj->CurrentUsage * 1024;
      $results['devswap'][$k]['free'] = ($obj->AllocatedBaseSize - $obj->CurrentUsage) * 1024;
      $results['devswap'][$k]['percent'] = $obj->CurrentUsage / $obj->AllocatedBaseSize;

      $results['swap']['total'] += $results['devswap'][$k]['total'];
      $results['swap']['used'] += $results['devswap'][$k]['used'];
      $results['swap']['free'] += $results['devswap'][$k]['free'];
      $k += 1;
    } 

    $results['swap']['percent'] = round($results['swap']['used'] / $results['swap']['total'] * 100);

    return $results;
  } 

  function filesystems ()
  {
    $objInstance = $this->wmi->InstancesOf("Win32_LogicalDisk");

    $k = 0;
    foreach ($objInstance as $obj) {
      $results[$k]['mount'] = $obj->Name;
      $results[$k]['size'] = $obj->Size / 1024;
      $results[$k]['used'] = ($obj->Size - $obj->FreeSpace) / 1024;
      $results[$k]['free'] = $obj->FreeSpace / 1024;
      $results[$k]['percent'] = round($results[$k]['used'] / $results[$k]['size'] * 100);
      $results[$k]['fstype'] = $obj->FileSystem;

      $typearray = array("Unknown", "No Root Directory", "Removeable Disk",
        "Local Disk", "Network Drive", "Compact Disc", "RAM Disk");
      $floppyarray = array("Unknown", "5 1/4 in.", "3 1/2 in.", "3 1/2 in.",
        "3 1/2 in.", "3 1/2 in.", "5 1/4 in.", "5 1/4 in.", "5 1/4 in.",
        "5 1/4 in.", "5 1/4 in.", "Other", "HD", "3 1/2 in.", "3 1/2 in.",
        "5 1/4 in.", "5 1/4 in.", "3 1/2 in.", "3 1/2 in.", "5 1/4 in.",
        "3 1/2 in.", "3 1/2 in.", "8 in.");

      $results[$k]['disk'] = $typearray[$obj->DriveType];
      if ($obj->DriveType == 2) $results[$k]['disk'] .= " (" . $floppyarray[$obj->MediaType] . ")";
      $k += 1;
    } 

    return $results;
  } 

  function distro ()
  {
    $objInstance = $this->wmi->InstancesOf("Win32_OperatingSystem");
    foreach ($objInstance as $obj) {
      return $obj->Caption;
    } 
  } 

  function distroicon ()
  {
    return 'xp.gif';
  } 
} 

?>
