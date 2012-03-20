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
// $Id: class.WINNT.inc.php,v 1.19 2006/04/22 16:47:25 bigmichi1 Exp $

class sysinfo {
  // $wmi holds the COM object that we pull all the WMI data from
  var $wmi; 

  // $wmidevices holds all devices, which are in the system
  var $wmidevices;

  // this constructor initialis the $wmi object
  function sysinfo ()
  {
    // don't set this params for local connection, it will not work
    $strHostname = '';
    $strUser = '';
    $strPassword = '';

    // initialize the wmi object
    $objLocator = new COM("WbemScripting.SWbemLocator");
    if($strHostname == "") {
        $this->wmi = $objLocator->ConnectServer();
      } else{
        $this->wmi = $objLocator->ConnectServer($strHostname, "rootcimv2", "$strHostname\$strUser", $strPassword);
      }
  } 

  // private function for getting a list of values in the specified context, optionally filter this list, based on the list from second parameter
  function _GetWMI($strClass, $strValue = array() ) {
    $objWEBM = $this->wmi->Get($strClass);

    if( PHP_VERSION < 5 ) {
      $objProp = $objWEBM->Properties_;
      $arrProp = $objProp->Next($objProp->Count);
      $objWEBMCol = $objWEBM->Instances_();
      $arrWEBMCol = $objWEBMCol->Next($objWEBMCol->Count);
    } else {
      $arrProp = $objWEBM->Properties_;
      $arrWEBMCol = $objWEBM->Instances_();
    }

    foreach($arrWEBMCol as $objItem)
    {
        @reset($arrProp);
        $arrInstance = array();
        foreach($arrProp as $propItem)
        {
            eval("\$value = \$objItem->" .$propItem->Name .";");
            if( empty( $strValue ) ) {
              $arrInstance[$propItem->Name] = trim($value);
            } else {
              if( in_array( $propItem->Name, $strValue ) ) {
                $arrInstance[$propItem->Name] = trim($value);
              }
            }
        }
        $arrData[] = $arrInstance;
    }
    return $arrData;
  }

  // private function for getting different device types from the system
  function _devicelist ( $strType ) {
    if( empty( $this->wmidevices ) ) {
      $this->wmidevices = $this->_GetWMI( "Win32_PnPEntity", array( "Name", "PNPDeviceID" ) );
    }

    $list = array();
    foreach ( $this->wmidevices as $device ) {
      if ( substr( $device["PNPDeviceID"], 0, strpos( $device["PNPDeviceID"], "\\" ) + 1 ) == ( $strType . "\\" ) ) {
        $list[] = $device["Name"];
      } 
    }

    return $list;
  }

  // get our canonical hostname
  function chostname ()
  {
    $buffer = $this->_GetWMI( "Win32_ComputerSystem", array( "Name" ) );
    $result = $buffer[0]["Name"];
    return gethostbyaddr(gethostbyname($result));
  }

  // get the IP address of our canonical hostname
  function ip_addr ()
  {
    $buffer = $this->_GetWMI( "Win32_ComputerSystem", array( "Name" ) );
    $result = $buffer[0]["Name"];
    return gethostbyname($result);
  }

  function kernel ()
  {
    $buffer = $this->_GetWMI( "Win32_OperatingSystem", array( "Version", "ServicePackMajorVersion" ) );
    $result = $buffer[0]["Version"];
    if( $buffer[0]["ServicePackMajorVersion"] > 0 ) {
      $result .= " SP" . $buffer[0]["ServicePackMajorVersion"];
    }
    return $result;
  } 

  // get the time the system is running
  function uptime ()
  {
    $result = 0;
    $buffer = $this->_GetWMI( "Win32_OperatingSystem", array( "LastBootUpTime" ) );

    $year = intval(substr($buffer[0]["LastBootUpTime"], 0, 4));
    $month = intval(substr($buffer[0]["LastBootUpTime"], 4, 2));
    $day = intval(substr($buffer[0]["LastBootUpTime"], 6, 2));
    $hour = intval(substr($buffer[0]["LastBootUpTime"], 8, 2));
    $minute = intval(substr($buffer[0]["LastBootUpTime"], 10, 2));
    $seconds = intval(substr($buffer[0]["LastBootUpTime"], 12, 2));

    $hour -= date("Z") / 60 / 60;	// GMT-Offset

    $boottime = mktime($hour, $minute, $seconds, $month, $day, $year);

    $result = mktime() - $boottime;

    return $result;
  } 

  // count the users, which are logged in
  function users ()
  {
    if( !stristr( $this->kernel(), "2000 P" ) ) return "N.A."; 
    $buffer = $this->_GetWMI( "Win32_PerfRawData_TermService_TerminalServices", array( "TotalSessions" ) );
    return $buffer[0]["TotalSessions"];
  } 

  // get the load of the processors
  function loadavg ($bar = false)
  {
    $buffer = $this->_GetWMI( "Win32_Processor", array( "LoadPercentage" ) );
    $cpuload = array();
    for( $i = 0; $i < count( $buffer ); $i++ ) {
      $cpuload['avg'][] = $buffer[$i]["LoadPercentage"];
    }
    if ($bar) {
      $cpuload['cpupercent'] = array_sum( $cpuload['avg'] ) / count( $buffer );
    }
    return $cpuload;
  } 

  // get some informations about the cpu's
  function cpu_info ()
  {
    $buffer = $this->_GetWMI( "Win32_Processor", array( "Name", "L2CacheSize", "CurrentClockSpeed", "ExtClock" ) );
    $results["cpus"] = 0;
    foreach ($buffer as $cpu) {
      $results["cpus"]++;
      $results["model"] = $cpu["Name"];
      $results["cache"] = $cpu["L2CacheSize"];
      $results["cpuspeed"] = $cpu["CurrentClockSpeed"];
      $results["busspeed"] = $cpu["ExtClock"];
    } 
    return $results;
  } 

  // get the pci devices from the system
  function pci ()
  {
    $pci = $this->_devicelist( "PCI" );
    return $pci;
  } 

  // get the ide devices from the system
  function ide ()
  {
    $buffer = $this->_devicelist( "IDE" );
    $ide = array();
    foreach ( $buffer as $device ) {
        $ide[]['model'] = $device;
    } 
    return $ide;
  } 

  // get the scsi devices from the system
  function scsi ()
  {
    $scsi = $this->_devicelist( "SCSI" );
    return $scsi;
  } 

  // get the usb devices from the system
  function usb ()
  {
    $usb = $this->_devicelist( "USB" );
    return $usb;
  } 

  // get the sbus devices from the system - currently not called
  function sbus ()
  {
    $sbus = $this->_devicelist( "SBUS" );
    return $sbus;
  } 

  // get the netowrk devices and rx/tx bytes
  function network ()
  {
    $buffer = $this->_GetWMI( "Win32_PerfRawData_Tcpip_NetworkInterface" );
    $results = array();
    foreach ( $buffer as $device ) {
      $dev_name = $device["Name"];
      // http://msdn.microsoft.com/library/default.asp?url=/library/en-us/wmisdk/wmi/win32_perfrawdata_tcpip_networkinterface.asp
      $results[$dev_name]['rx_bytes'] = $device["BytesReceivedPersec"];
      $results[$dev_name]['rx_packets'] = $device["PacketsReceivedPersec"];
      $results[$dev_name]['rx_errs'] = $device["PacketsReceivedErrors"];
      $results[$dev_name]['rx_drop'] = $device["PacketsReceivedDiscarded"];

      $results[$dev_name]['tx_bytes'] = $device["BytesSentPersec"];
      $results[$dev_name]['tx_packets'] = $device["PacketsSentPersec"];

      $results[$dev_name]['errs'] = $device["PacketsReceivedErrors"];
      $results[$dev_name]['drop'] = $device["PacketsReceivedDiscarded"];
    }
    return $results;
  } 

  function memory ()
  {
    $buffer = $this->_GetWMI( "Win32_LogicalMemoryConfiguration", array( "TotalPhysicalMemory" ) );
    $results['ram']['total'] = $buffer[0]["TotalPhysicalMemory"];

    $buffer = $this->_GetWMI( "Win32_PerfRawData_PerfOS_Memory", array( "AvailableKBytes" ) );
    $results['ram']['free'] = $buffer[0]["AvailableKBytes"];

    $results['ram']['used'] = $results['ram']['total'] - $results['ram']['free'];
    $results['ram']['t_used'] = $results['ram']['used'];
    $results['ram']['t_free'] = $results['ram']['total'] - $results['ram']['t_used'];
    $results['ram']['percent'] = ceil( ( $results['ram']['t_used'] * 100 ) / $results['ram']['total'] );
    $results['swap']['total'] = 0;
    $results['swap']['used'] = 0;
    $results['swap']['free'] = 0;

    $buffer = $this->_GetWMI( "Win32_PageFileUsage" ); // no need to filter, using nearly everything from output
    $k = 0;
    foreach ($buffer as $swapdevice) {
      $results['devswap'][$k]['dev'] = $swapdevice["Name"];
      $results['devswap'][$k]['total'] = $swapdevice["AllocatedBaseSize"] * 1024;
      $results['devswap'][$k]['used'] = $swapdevice["CurrentUsage"] * 1024;
      $results['devswap'][$k]['free'] = ( $swapdevice["AllocatedBaseSize"] - $swapdevice["CurrentUsage"] ) * 1024;
      $results['devswap'][$k]['percent'] = ceil( $swapdevice["CurrentUsage"] / $swapdevice["AllocatedBaseSize"] );

      $results['swap']['total'] += $results['devswap'][$k]['total'];
      $results['swap']['used'] += $results['devswap'][$k]['used'];
      $results['swap']['free'] += $results['devswap'][$k]['free'];
      $k += 1;
    } 
    $results['swap']['percent'] = ceil( $results['swap']['used'] / $results['swap']['total'] * 100 );
    return $results;
  } 

  // get the filesystem informations
  function filesystems ()
  {
     $typearray = array("Unknown", "No Root Directory", "Removeable Disk",
        "Local Disk", "Network Drive", "Compact Disc", "RAM Disk");
     $floppyarray = array("Unknown", "5 1/4 in.", "3 1/2 in.", "3 1/2 in.",
        "3 1/2 in.", "3 1/2 in.", "5 1/4 in.", "5 1/4 in.", "5 1/4 in.",
        "5 1/4 in.", "5 1/4 in.", "Other", "HD", "3 1/2 in.", "3 1/2 in.",
        "5 1/4 in.", "5 1/4 in.", "3 1/2 in.", "3 1/2 in.", "5 1/4 in.",
        "3 1/2 in.", "3 1/2 in.", "8 in.");

    $buffer = $this->_GetWMI( "Win32_LogicalDisk" , array( "Name", "Size", "FreeSpace", "FileSystem", "DriveType", "MediaType" ) );

    $k = 0;
    foreach ( $buffer as $filesystem ) {
      if ( hide_mount( $filesystem["Name"] ) ) {
        continue;
      }
      $results[$k]['mount'] = $filesystem["Name"];
      $results[$k]['size'] = $filesystem["Size"] / 1024;
      $results[$k]['used'] = ( $filesystem["Size"] - $filesystem["FreeSpace"] ) / 1024;
      $results[$k]['free'] = $filesystem["FreeSpace"] / 1024;
      @$results[$k]['percent'] = ceil( $results[$k]['used'] / $results[$k]['size'] * 100 );  // silence this line, nobody is having a floppy in the drive everytime
      $results[$k]['fstype'] = $filesystem["FileSystem"];
      $results[$k]['disk'] = $typearray[$filesystem["DriveType"]];
      if ( $filesystem["DriveType"] == 2 ) $results[$k]['disk'] .= " (" . $floppyarray[$filesystem["MediaType"]] . ")";
      $k += 1;
    } 
    return $results;
  } 

  function distro ()
  {
    $buffer = $this->_GetWMI( "Win32_OperatingSystem", array( "Caption" ) );
    return $buffer[0]["Caption"];
  } 

  function distroicon ()
  {
    return 'xp.gif';
  } 
} 

?>
