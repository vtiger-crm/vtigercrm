<?php
/*+********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* Portions created by FOSS Labs are Copyright (C) FOSS Labs.
* All Rights Reserved.
************************************************************************************/
//Modified By Krem on  30/05/2008  - Details  at http://creadev.net/Webmails-vTiger504
  
include_once('config.php');
require_once('include/logging.php');
require_once('include/utils/utils.php');

class MailBox {

	var $mbox;
	var $db;
	var $boxinfo;
	var $readonly='false';
	var $enabled;

	var $login_username;
	var $secretkey;
	var $imapServerAddress;
	var $ssltype;
	var $sslmeth;
	var $box_refresh;
	var $mails_per_page;
	var $mail_protocol;
	var $account_name;
	var $display_name;
	var $mailbox;
	var $mailList;

	function MailBox($mailbox = '',$p='',$s='') {
		global $current_user;
		require_once('include/utils/encryption.php');
		$oencrypt = new Encryption();
	
		$this->db = PearDatabase::getInstance();
		$this->db->println("Entering MailBox($mailbox)");

		$this->mailbox = $mailbox;
		$tmp = getMailServerInfo($current_user);

		if($this->db->num_rows($tmp) < 1)
			$this->enabled = 'false';
		else
			$this->enabled = 'true';

		$this->boxinfo = $this->db->fetch_array($tmp);

		$this->login_username=trim($this->boxinfo["mail_username"]); 
		$this->secretkey=$oencrypt->decrypt(trim($this->boxinfo["mail_password"])); 
		$this->imapServerAddress=gethostbyname(trim($this->boxinfo["mail_servername"])); 
		$this->mail_protocol=$this->boxinfo["mail_protocol"]; 
		$this->ssltype=$this->boxinfo["ssltype"]; 
		$this->sslmeth=$this->boxinfo["sslmeth"]; 

		$this->box_refresh=trim($this->boxinfo["box_refresh"]);
		$this->mails_per_page=trim($this->boxinfo["mails_per_page"]);
		if($this->mails_per_page < 1)
        		$this->mails_per_page=20;

		$this->account_name=$this->boxinfo["account_name"];
		$this->display_name=$this->boxinfo["display_name"];
		//$this->imapServerAddress=$this->boxinfo["mail_servername"];

		$this->db->println("Setting Mailbox Name");
		if($this->mailbox != "") 
			$this->mailbox=$mailbox;

		$this->db->println("Opening Mailbox");
		if(!$this->mbox && $this->mailbox != "")
			$this->getImapMbox();

		$this->db->println("Loading mail list");
		$pa=$p;
		$se=$s;
		if($this->mbox){
			if ($se != ""){$this->mailList = $this->searchMailList($se,$pa);}		
			else if ($pa == ""){$this->mailList = $this->customMailList(0);}
			else {$this->mailList = $this->customMailList($pa);}
		}

		$this->db->println("Exiting MailBox($mailbox)");
	}

	function customMailList($page) {
		$info = imap_check($this->mbox);
		$numEmails = $info->Nmsgs;
		$current_mails = ceil($page*$this->mails_per_page);
		$current_mails = $numEmails - $current_mails;
		$start =$current_mails-$this->mails_per_page+1;
		if ($start<=0) $start=1;
		if ($current_mails<=0)$current_mails=0;

		$mailOverviews = @imap_fetch_overview($this->mbox, "$start:$current_mails", 0);
		$out = array("overview"=>$mailOverviews,"count"=>$numEmails);
		return $out;
	}

	function searchMailList($searchstring,$page) {	
		$search="";		
		$searchlist = Array();

		$searchlist = imap_search($this->mbox,$searchstring);
		if ($searchlist==false) return $out;
		$num_searches = count($searchlist);
		if ($num_searches < $this->mails_per_page){
			$current_mails = $num_searches -1;
		}else{
			$current_mails = ceil($page*$this->mails_per_page);
			$current_mails = $num_searches - $current_mails-1;
		}
		$start = $current_mails-$this->mails_per_page;
		if ($start < 0)$start=0;
		$j=0;
		for($i=$current_mails; $i >= $start ; $i--){	
			if($i==$current_mails){			
				$search=$searchlist[$i];
			}else $search=$search.",".$searchlist[$i];			
			$j++;			
		}
		if ($search!="")
			$result = @imap_fetch_overview($this->mbox, "$search",0);
		$out = array("overview"=>$result,"count"=>count($searchlist));
		return $out;
	}
	
	function fullMailList() {
		$mailHeaders = @imap_headers($this->mbox);
		$numEmails = sizeof($mailHeaders);
		$mailOverviews = @imap_fetch_overview($this->mbox, "1:$numEmails", 0);
		$out = array("headers"=>$mailHeaders,"overview"=>$mailOverviews,"count"=>$numEmails);
		return $out;
	}

	function isBase64($iVal){
		$_tmp=preg_replace("/[^A-Z0-9\+\/\=]/i","",$iVal);
		return (strlen($_tmp) % 4 == 0 ) ? "y" : "n";
	}

	function getImapMbox() {
		$this->db->println("Entering getImapMbox()");
		$mods = parsePHPModules();
		$this->db->println("Parsing PHP Modules");
	 	 
		// first we will try a regular old IMAP connection: 
		if($this->ssltype == "") {$this->ssltype = "notls";} 
		if($this->sslmeth == "") {$this->sslmeth = "novalidate-cert";} 

		if($this->mail_protocol == "pop3")
			$port = "110";
		else
		{
	    		if($mods["imap"]["SSL Support"] == "enabled" && ($this->ssltype == "tls" || $this->ssltype == "ssl"))
				$port = "993";
			else
				$port = "143";
		}

		$this->db->println("Building connection string");
                if(preg_match("/@/",$this->login_username)) 
		{
                        $mailparts = split("@",$this->login_username);
                        $user="".trim($mailparts[0])."";
                        $domain="".trim($mailparts[1])."";

			// This section added to fix a bug when connecting as user@domain.com
			if($this->readonly == "true") 
			{
	    			if($mods["imap"]["SSL Support"] == "enabled")
                                	$connectString = "/".$this->ssltype."/".$this->sslmeth."/user={$user}@{$domain}/readonly";
				else
                                	$connectString = "/notls/novalidate-cert/user={$user}@{$domain}/readonly";
			}
			else
			{
	    			if($mods["imap"]["SSL Support"] == "enabled")
                                	$connectString = "/".$this->ssltype."/".$this->sslmeth."/user={$user}@{$domain}";
				else
                                	$connectString = "/notls/novalidate-cert/user={$user}@{$domain}";
			}
		}
		else
		{
			if($this->readonly == "true")
			{
	    			if($mods["imap"]["SSL Support"] == "enabled")
					$connectString = "/".$this->ssltype."/".$this->sslmeth."/readonly";
	    			else
					$connectString = "/notls/novalidate-cert/readonly";
			}
			else
			{
	    			if($mods["imap"]["SSL Support"] == "enabled")
					$connectString = "/".$this->ssltype."/".$this->sslmeth;
	    			else
					$connectString = "/notls/novalidate-cert";
			}
		}

		//$connectString = "{".$this->imapServerAddress."/".$this->mail_protocol.":".$port.$connectString."}".$this->mailbox;
		$connectString = "{".$this->imapServerAddress.":".$port."/".$this->mail_protocol.$connectString."}".$this->mailbox;
		//Reference - http://forums.vtiger.com/viewtopic.php?p=33478#33478 - which has no tls or validate-cert
		$connectString1 = "{".$this->imapServerAddress."/".$this->mail_protocol.":".$port."}".$this->mailbox; 

		$this->db->println("Done Building Connection String.. $connectString  Connecting to box");
		//checking the imap support in php
		if(!function_exists('imap_open'))
		{
			echo "<strong>".$mod_strings['LBL_ENABLE_IMAP_SUPPORT']."</strong>";
			exit();
		}
			
		if(!$this->mbox = @imap_open($connectString, $this->login_username, $this->secretkey))
		{
			//try second string which has no tls or validate-cert
			if(!$this->mbox = @imap_open($connectString1, $this->login_username, $this->secretkey))
			{
				global $current_user,$mod_strings;
				$this->db->println("CONNECTION ERROR - Could not be connected to the server using imap_open function through the connection strings $connectString and $connectString1");
				echo "<br>&nbsp;<b>".$mod_strings['LBL_MAIL_CONNECT_ERROR']."<a href='index.php?module=Users&action=AddMailAccount&return_module=Webmails&return_action=index&record=".$current_user->id."'> ".$mod_strings['LBL_HERE']."</a>. ".$mod_strings['LBL_PLEASE']." <a href='index.php?module=Emails&action=index&parenttab=".vtlib_purify($_REQUEST['parenttab'])."'>".$mod_strings['LBL_CLICK_HERE']."</a>".$mod_strings['LBL_GOTO_EMAILS_MODULE']." </b>";
				exit;
			}
		}

		$this->db->println("Done connecting to box");
	}
} // END CLASS


function parsePHPModules() {
 ob_start();
 phpinfo(INFO_MODULES);
 $s = ob_get_contents();
 ob_end_clean();

 $s = strip_tags($s,'<h2><th><td>');
 $s = preg_replace('/<th[^>]*>([^<]+)<\/th>/',"<info>\\1</info>",$s);
 $s = preg_replace('/<td[^>]*>([^<]+)<\/td>/',"<info>\\1</info>",$s);
 $vTmp = preg_split('/(<h2>[^<]+<\/h2>)/',$s,-1,PREG_SPLIT_DELIM_CAPTURE);
 $vModules = array();
 for ($i=1;$i<count($vTmp);$i++) {
  if (preg_match('/<h2>([^<]+)<\/h2>/',$vTmp[$i],$vMat)) {
   $vName = trim($vMat[1]);
   $vTmp2 = explode("\n",$vTmp[$i+1]);
   foreach ($vTmp2 AS $vOne) {
   $vPat = '<info>([^<]+)<\/info>';
   $vPat3 = "/$vPat\s*$vPat\s*$vPat/";
   $vPat2 = "/$vPat\s*$vPat/";
   if (preg_match($vPat3,$vOne,$vMat)) { // 3cols
     $vModules[$vName][trim($vMat[1])] = array(trim($vMat[2]),trim($vMat[3]));
   } elseif (preg_match($vPat2,$vOne,$vMat)) { // 2cols
     $vModules[$vName][trim($vMat[1])] = trim($vMat[2]);
   }
   }
  }
 }
 return $vModules;
}
?>
