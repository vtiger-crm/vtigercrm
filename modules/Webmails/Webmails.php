<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is FOSS Labs.
 * Portions created by FOSS Labs are Copyright (C) FOSS Labs.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
include_once('config.php');
require_once('include/logging.php');
require_once('modules/Webmails/conf.php');
require_once('modules/Webmails/functions.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/CommonUtils.php');
require_once('data/CRMEntity.php');
class result
{
	  var $text = "";
	    var $charset = "";
}



class Webmails extends CRMEntity {
        var $log;
        var $db;

	var $headers;
  	var $mailid;
        var $to = array();
        var $to_name = array();
        var $from;
        var $fromname;
        var $fromaddr;
	var $reply_to = array();
	var $reply_to_name = array();
	var $cc_list = array();
	var $cc_list_name = array();
	var $subject;
	var $date;
	var $body_type;
	var $body;
	var $attachments = array();
	var $inline = array();
	var $attachtab = array();
	var $mbox;
	var $email;
	var $relationship = array();
	var $replyToInformation = array();
	var $has_attachments = false;


 	function Webmails($mbox='',$mailid='') {

		$this->db = PearDatabase::getInstance();
		$this->db->println("Entering Webmail($mbox,$mailid)");
		$this->log = &LoggerManager::getLogger('WEBMAILS');
		$this->mbox=$mbox;
		$this->mailid=$mailid;

		$this->headers = $this->load_headers();

		$this->to = $this->headers["theader"]["to"];
		$this->to_name = $this->headers["theader"]["to_name"];
		$this->db->println("Webmail TO:");
		$this->db->println($this->to);

		$this->from = $this->headers["theader"]["from"];
		$this->fromname = $this->headers["theader"]["from_name"];
		$this->fromaddr = $this->headers["theader"]["fromaddr"];

		$this->reply_to = $this->headers["theader"]["reply_to"];
		$this->reply_to_name = $this->headers["theader"]["reply_to_name"];

		$this->cc_list = $this->headers["cc_list"];
		$this->cc_list_name = $this->headers["cc_list_name"];

		$this->subject = $this->headers["theader"]["subject"];
		$this->date = $this->headers["theader"]["date"];

		$this->has_attachments = $this->get_attachments();
		$this->db->println("Exiting Webmail($mbox,$mailid)");

		$this->relationship = $this->find_relationships(); // Added by Puneeth for 5231
		$this->replyToInformation = null;
        }

	public function getReplyToInformation() {
		if(empty($this->replyToInformation)) {
			$this->replyToInformation = $this->findRelationshipsForReplyToSender();
		}
		return $this->replyToInformation;
	}
	
	function delete() {
		imap_delete($this->mbox, $this->mailid);
	}

	function loadMail($attach_tab) {
		
		$this->email = $this->load_mail($attach_tab);
		$this->body = $this->email["body"];
		$this->attachtab = $this->email["attachtab"];
		$this->att= $this->email["att"];
	}

	function replyBody() {
		$tmpvar = "<br><br><p style='font-weight:bold'>".$mod_strings['IN_REPLY_TO_THE_MESSAGE'].$this->reply_name." on ".$this->date."</p>";
		$tmpvar .= "<blockquote style='border-left:1px solid blue;padding-left:5px'>".$this->body."</blockquote>";
		return $tmpvar;
	}

	function unDeleteMsg() {
		imap_undelete($this->mbox, $this->mailid);
	}

	function setFlag() {
		$status=imap_setflag_full($this->mbox,$this->mailid,"\\Flagged");
	}

	function delFlag() {
		$status=imap_clearflag_full($this->mbox,$this->mailid,"\\Flagged");
	}

	function getBodyType() {
		return $this->body_type;
	}

	function downloadInlineAttachments() {
		return $this->dl_inline();
	}

	function downloadAttachments() {
		return $this->dl_attachments();
	}

    function load_headers() {
	// get the header info
	$mailHeader=Array();
	$theader = @imap_headerinfo($this->mbox, $this->mailid);
	$tmpvar = imap_mime_header_decode($theader->fromaddress);

	for($p=0;$p<count($theader->to);$p++) {
		$mailHeader['to'][] = $theader->to[$p]->mailbox.'@'.$theader->to[$p]->host;
		$mailHeader['to_name'][] = $theader->to[$p]->personal;
	}
	$mailHeader['from'] = $theader->from[0]->mailbox.'@'.$theader->from[0]->host;	
	$mailHeader['from_name'] = $theader->from[0]->personal;
	$mailHeader['fromaddr'] = $theader->fromaddress;

	$mailHeader['subject'] = strip_tags($theader->subject);
	$mailHeader['date'] = $theader->date;

	for($p=0;$p<count($theader->reply_to);$p++) {
		$mailHeader['reply_to'][] = $theader->reply_to[$p]->mailbox.'@'.$theader->reply_to[$p]->host;
		$mailHeader['reply_to_name'][] = $theader->reply_to[$p]->personal;
	}
	for($p=0;$p<count($theader->cc);$p++) {
		$mailHeader['cc_list'][] = $theader->cc[$p]->mailbox.'@'.$theader->cc[$p]->host;
		$mailHeader['cc_list_name'][] = $theader->cc[$p]->personal;
	}
    	return $ret = Array("theader"=>$mailHeader);
    }

    function get_attachments() {
       $struct = @imap_fetchstructure($this->mbox, $this->mailid);
       $parts = $struct->parts;

        $done="false";
        $i = 0;
        if (!$parts)
                return false; // simple message
        else  {
        $stack = array();
        $inline = array();

        $endwhile = false;

        while (!$endwhile) {
           if (!$parts[$i]) {
             if (count($stack) > 0) {
               $parts = $stack[count($stack)-1]["p"];
               $i    = $stack[count($stack)-1]["i"] + 1;
               array_pop($stack);
             } else {
               $endwhile = true;
             }
        }
           if (!$endwhile) {

             $partstring = "";
             foreach ($stack as $s) {
               $partstring .= ($s["i"]+1) . ".";
             }
             $partstring .= ($i+1);

             if (strtoupper($parts[$i]->disposition) == "INLINE" || strtoupper($parts[$i]->disposition) == "ATTACHMENT")
                        return true;
             }
           if ($parts[$i]->parts) {
             $stack[] = array("p" => $parts, "i" => $i);
             $parts = $parts[$i]->parts;
             $i = 0;
           } else {
             $i++;
           }
         }
       }
        return false;
    }

    function find_relationships() {
	// leads search
	$sql = "SELECT * from vtiger_leaddetails left join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_leaddetails.leadid where vtiger_leaddetails.email = ? AND vtiger_crmentity.deleted='0' and converted=0";
	$res = $this->db->pquery($sql,array(trim($this->from)),true,"Error: "."<BR>$query");
	$numRows = $this->db->num_rows($res);
	if($numRows > 0)
		return array('type'=>"Leads",'id'=>$this->db->query_result($res,0,"leadid"),'name'=>$this->db->query_result($res,0,"firstname")." ".$this->db->query_result($res,0,"lastname"));

	// contacts search
	$sql = "SELECT * from vtiger_contactdetails left join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid where vtiger_contactdetails.email = ?  AND vtiger_crmentity.deleted='0'";
	$res = $this->db->pquery($sql,array(trim($this->from)),true,"Error: "."<BR>$query");
	$numRows = $this->db->num_rows($res);
	if($numRows > 0)
		return array('type'=>"Contacts",'id'=>$this->db->query_result($res,0,"contactid"),'name'=>$this->db->query_result($res,0,"firstname")." ".$this->db->query_result($res,0,"lastname"));

	// vtiger_accounts search
	$sql = "SELECT * from vtiger_account left join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_account.accountid where vtiger_account.email1 = ? OR vtiger_account.email2=?  AND vtiger_crmentity.deleted='0'";
	$res = $this->db->pquery($sql, array(trim($this->from), trim($this->from)), true,"Error: "."<BR>$query");
	$numRows = $this->db->num_rows($res);
	if($numRows > 0)
		return array('type'=>"Accounts",'id'=>$this->db->query_result($res,0,"accountid"),'name'=>$this->db->query_result($res,0,"accountname"));

	return array();
    }


	public function searchModule($module) { 
		global $current_user; 
		$queryGenerator = new QueryGenerator($module, $current_user); 
		$queryGenerator->initForGlobalSearchByType('email', trim($this->reply_to[0]), 'e'); 
		$query = $queryGenerator->getQuery(); 
		$res = $this->db->pquery($query,array(),true,"Error: "."<BR>$query"); 
		$meta = $queryGenerator->getMeta($module); 
		$fieldList = $meta->getFieldListByType('email'); 
		$found = false; 
		$fieldId = null; 
		$numRows = $this->db->num_rows($res); 
		if($numRows > 0) { 
				foreach ($fieldList as $field) { 
						$value = from_html($this->db->query_result($res,0,$field->getColumnName())); 
						if($value == trim($this->reply_to[0])) { 
								$found = true; 
								$fieldId = $field->getFieldId(); 
								break; 
						} 
				} 
				$nameListFields = explode(',', $meta->getNameFields()); 
				$name = ''; 
				foreach ($nameListFields as $nameColumn) { 
						$name .= $this->db->query_result($res,0,$nameColumn); 
				} 
				if($found) { 
						return array('type'=>$module,'fieldId'=>$fieldId ,'id'=>$this->db->query_result($res,0, 
										$meta->getIdColumn()),'name'=>$name); 
				} 
		} 
		return null; 
	} 

	function findRelationshipsForReplyToSender() { 
		$result = $this->searchModule('Contacts'); 
		if(empty($result)) { 
				$result = $this->searchModule('Leads'); 
		} 
		if(empty($result)) { 
				$result = $this->searchModule('Accounts'); 
		} 
		if(empty($result)) { 
				$result = array(); 
		} 
		return $result; 
	} 
    
	function dl_inline()
	{
		$struct = @imap_fetchstructure($this->mbox, $this->mailid);
		$parts = $struct->parts;

		$i = 0;
		if (!$parts)
			return;
		else
		{
			$stack = array();
			$inline = array();

			$endwhile = false;

			while (!$endwhile)
			{
				if (!$parts[$i])
				{
					if (count($stack) > 0)
					{
						$parts = $stack[count($stack)-1]["p"];
						$i = $stack[count($stack)-1]["i"] + 1;
						array_pop($stack);
					}
					else
					{
						$endwhile = true;
					}
				}
				if (!$endwhile)
				{
					$partstring = "";
					foreach ($stack as $s)
					{
						$partstring .= ($s["i"]+1) . ".";
					}
					$partstring .= ($i+1);

					if (strtoupper($parts[$i]->disposition) == "INLINE")
					{
						//if the type is JPEG or GIF then call mail_fetchpart else fetchbody
						if($parts[$i]->subtype == "JPEG" || $parts[$i]->subtype == "GIF")
							$filedata = $this->mail_fetchpart($partstring);
						else
							$filedata = imap_fetchbody($this->mbox, $this->mailid, $partstring);

						//Added to get the UTF-8 string - 30-11-06 - Mickie
						$parts[$i]->dparameters[0]->value = utf8_decode(imap_utf8($parts[$i]->dparameters[0]->value));

						//Added to get the UTF-8 string - 02-02-06 - Mickie
						$filedata = utf8_decode(imap_utf8($filedata));

						$inline[] = array("filename" => $parts[$i]->dparameters[0]->value,"filedata"=>$filedata,"subtype"=>$parts[$i]->subtype,"filesize"=>$parts[$i]->bytes);
					}
				}
				if ($parts[$i]->parts)
				{
					$stack[] = array("p" => $parts, "i" => $i);
					$parts = $parts[$i]->parts;
					$i = 0;
				}
				else
				{
					$i++;
				}
			}
		}
		return $inline;
	}

	function dl_attachments()
	{

		$struct = @imap_fetchstructure($this->mbox, $this->mailid);
		$parts = $struct->parts;

		$i = 0;
		if (!$parts)
			return;
		else
		{
			$stack = array();
			$attachment = array();

			$endwhile = false;

			while (!$endwhile)
			{
				if (!$parts[$i])
				{
					if (count($stack) > 0)
					{
						$parts = $stack[count($stack)-1]["p"];
						$i = $stack[count($stack)-1]["i"] + 1;
						array_pop($stack);
					}
					else
					{
						$endwhile = true;
					}
				}
				if (!$endwhile)
				{
					$partstring = "";
					foreach ($stack as $s)
					{
						$partstring .= ($s["i"]+1) . ".";
					}
					$partstring .= ($i+1);

					if (strtoupper($parts[$i]->disposition) == "ATTACHMENT")
					{
						$filedata = imap_fetchbody($this->mbox, $this->mailid, $partstring);
						$attachment[] = array("filename" => $parts[$i]->dparameters[0]->value,"filedata"=>$filedata,"subtype"=>$parts[$i]->subtype,"filesize"=>$parts[$i]->bytes);
					}
				} 
				if ($parts[$i]->parts)
				{
					$stack[] = array("p" => $parts, "i" => $i);
					$parts = $parts[$i]->parts;
					$i = 0;
				}
				else
				{
					$i++;
				}
			}
		}
		return $attachment;
	}





	function graphicalsmilies($body) {
		$user_prefs = $_SESSION['nocc_user_prefs'];
		if (isset($user_prefs->graphical_smilies) && $user_prefs->graphical_smilies) {
			$body = preg_replace("/\;-?\)/","<img src=\"themes/" . $_SESSION['nocc_theme'] . "/img/smilies/wink.png\" alt=\"wink\"/>", $body);
			$body = preg_replace("/\;-?D/","<img src=\"themes/" . $_SESSION['nocc_theme'] . "/img/smilies/grin.png\" alt=\"grin\"/>", $body);
			$body = preg_replace("/:\'\(?/","<img src=\"themes/" . $_SESSION['nocc_theme'] . "/img/smilies/cry.png\" alt=\"cry\"/>", $body);
			$body = preg_replace("/:-?[xX]/","<img src=\"themes/" . $_SESSION['nocc_theme'] . "/img/smilies/confused.png\" alt=\"confused\"/>", $body);
			$body = preg_replace("/:-?\[\)/","<img src=\"themes/" . $_SESSION['nocc_theme'] . "/img/smilies/embarassed.png\" alt=\"embarassed\"/>", $body);
			$body = preg_replace("/:-?\*/","<img src=\"themes/" . $_SESSION['nocc_theme'] . "/img/smilies/love.png\" alt=\"love\"/>", $body);
			$body = preg_replace("/:-?[pP]/","<img src=\"themes/" . $_SESSION['nocc_theme'] . "/img/smilies/tongue.png\" alt=\"tongue\"/>", $body);
			$body = preg_replace("/:-?\)/","<img src=\"themes/" . $_SESSION['nocc_theme'] . "/img/smilies/happy.png\" alt=\"happy\"/>", $body);
			$body = preg_replace("/:-?\(/","<img src=\"themes/" . $_SESSION['nocc_theme'] . "/img/smilies/unhappy.png\" alt=\"unhappy\"/>", $body);
			$body = preg_replace("/:-[oO]/","<img src=\"themes/" . $_SESSION['nocc_theme'] . "/img/smilies/surprised.png\" alt=\"surprised\"/>", $body);
			$body = preg_replace("/8-?\)/","<img src=\"themes/" . $_SESSION['nocc_theme'] . "/img/smilies/cool.png\" alt=\"cool\"/>", $body);
		}
		return ($body);
	}

// based on a function from matt@bonneau.net
function GetPart(&$attach_tab, &$this_part, $part_no, &$display_rfc822)
{
    $att_name = '[unknown]';
    if ($this_part->ifdescription == true)
    {
	    $att_name = $this_part->description;
    }
    for ($i = 0; $i < count($this_part->parameters); $i++)
    {
        // PHP 5.x doesn't allow to convert a stdClass object to an array
	// We sometimes have this issue with Mailer daemon reports
	if (!(get_class($this_part->parameters) == "stdClass") &&
		!(get_class($this_part->parameters) == "stdclass")) 
	{ 
            $param = $this_part->parameters[$i];
            if ((($param->attribute == 'NAME') || ($param->attribute == 'name')) && ($param->value != ''))
            {
                $att_name = $param->value;
                break;
            }
	}
    }
    if (isset($this_part->type))
    {
        switch ($this_part->type)
        {
            case 0:
                $mime_type = 'text';
                break;
            case 1:
		    $mime_type = 'multipart';
                for ($i = 0; $i < count($this_part->parts); $i++)
	{
                    if ($part_no != ''){
			$len = strlen($part_no);
			if(!strpos($part_no,'.',($len-1)))
                     	   $part_no = $part_no . '.';
		    }
                    // if it's an alternative, we skip the text part to only keep the HTML part
                    if ($this_part->subtype == 'ALTERNATIVE')// && $read == true)
                        $this->GetPart($attach_tab, $this_part->parts[++$i], $part_no . ($i + 1), $display_rfc822);
                    else 
                        $this->GetPart($attach_tab, $this_part->parts[$i], $part_no . ($i + 1), $display_rfc822);
                }
                break;
            case 2:
                $mime_type = 'message';
                // well it's a message we have to parse it to find attachments or text message
		if(isset($this_part->parts[0]->parts)) 
		{
                    $num_parts = count($this_part->parts[0]->parts);
		    for ($i = 0; $i < $num_parts; $i++)
		{
			    $this->GetPart($attach_tab, $this_part->parts[0]->parts[$i], $part_no . '.' . ($i + 1), $display_rfc822);
		    }
                }
                break;
            // Maybe we can do something with the mime types later ??
            case 3:
                $mime_type = 'application';
                break;
            case 4:
                $mime_type = 'audio';
                break;
            case 5:
                $mime_type = 'image';
                break;
            case 6:
                $mime_type = 'video';
                break;
            case 7:
                $mime_type = 'other';
                break;
            default:
                $mime_type = 'unknown';
        }
    }
			else 
    {	
		    $mime_type = 'text';
    }
	$full_mime_type = $mime_type . '/' . $this_part->subtype;
    if (isset($this_part->encoding))
    {
        switch ($this_part->encoding)
        {
            case 0:
                $encoding = '7BIT';
                break;
            case 1:
                $encoding = '8BIT';
                break;
            case 2:
                $encoding = 'BINARY';
                break;
            case 3:
                $encoding = 'BASE64';
                break;
            case 4:
                $encoding = 'QUOTED-PRINTABLE';
                break;
            case 5:
                $encoding = 'OTHER';
                break;
            default:
                $encoding = 'none';
                break;
        }
		}
		else
		{
	    $encoding = '7BIT';
    }

    if (($full_mime_type == 'message/RFC822' && $display_rfc822 == true) || ($mime_type != 'multipart' && $full_mime_type != 'message/RFC822'))
    {
        $charset = '';
        if ($this_part->ifparameters)
            while ($obj = array_pop($this_part->parameters))
                if (strtolower($obj->attribute) == 'charset')
                {
                    $charset = $obj->value;
                    break;
                }
        $tmp = Array(
            'number' => ($part_no != '' ? $part_no : 1),
            'id' => $this_part->ifid ? $this_part->id : 0,
            'name' => $att_name,
            'mime' => $full_mime_type,
            'transfer' => $encoding,
            'disposition' => $this_part->ifdisposition ? $this_part->disposition : '',
            'charset' => $charset,
            'size' => ($this_part->bytes > 1000) ? ceil($this_part->bytes / 1000) : 1
        );

        array_unshift($attach_tab, $tmp);
    }
}

function GetCodeScoreAll($Data,$beg_charset) {
	global $cad_StatsTableWin, $cad_StatsTableKoi;
	$PairSize = 2;

	$Data=substr($Data,$beg_charset,100);
	$Data=preg_replace('/[\n\r]/','',$Data);
	setlocale(LC_CTYPE,'ru_RU.KOI8-R');

	$Mark_koi=0;
	$Mark_win=0;
	$cnt=0;
	$max_detect_limit=10;

	$sp=preg_split('/[\.\,\-\s\:\;\?\!\'\"\(\)\d<>]+/',$Data);
	while ( list($key2,$val2) = each($sp) ) {
		$rc=preg_match("/(.*)([\x7F-\xFF]+)/x",$val2);
		if($rc == 0) {
			continue;
		}

		if($cnt > $max_detect_limit) {
			break;
		} else {
			$cnt++;
		}
		$dlina=strlen($val2)-$PairSize;
		if($dlina < 1) {$cnt--; continue;}
		$val3=strtolower($val2);
		if (ucfirst($val3) == $val2) {
			$scaleK=2;
		} else {
			$scaleK=1;
		}
		if(substr($val3,0,1).strtoupper(substr($val2,1,strlen($val2))) == $val2) {
			$scaleW=2;
		} else {
			$scaleW=1;
		}
		$Cur_mark_koi=0;
		$Cur_mark_win=0;
		for ($i=0; $i<$dlina; $i++ ) {
			$pp=substr ($val3, $i, $PairSize);
			if (isset($cad_StatsTableKoi[$pp])) {
				$Cur_mark_koi += $cad_StatsTableKoi[$pp];
			}
			if (isset($cad_StatsTableWin[$pp])) {
				$Cur_mark_win += $cad_StatsTableWin[$pp];
			}
		}
		$Mark_koi+=$Cur_mark_koi*$scaleK;
		$Mark_win+=$Cur_mark_win*$scaleW;
	}
	$Mark_list=array($Mark_koi,$Mark_win);
	//setlocale(LC_CTYPE,$old_locale);
	return $Mark_list;
}

/* lxnt:  patched to return charset names that iconv() understands*/
function detect_charset($Data,$dbg_fl = 0) {
	/* for many small pices of text -  list of sender/subject*/
	$rc=preg_match("/(.*)([\x7F-\xFF]+)/xU",$Data,$tst_ar);
	if($rc == 0) {
		return 'US-ASCII';
	} else {
		$beg_charset=strpos($Data,$tst_ar[2]);
	}
	list($KoiMark,$WinMark) = GetCodeScoreAll($Data,$beg_charset);
	$Ratio['koi8-r'] =  $KoiMark/($WinMark + 1);
	$Ratio['windows-1251'] =  $WinMark/($KoiMark + 1);

	list($MaxRation,$MaxRatioKey)=max_from_ratio($Ratio);
	return $MaxRatioKey;
}







function mime_header_decode(&$header)
{
	$output_charset = $GLOBALS['charset'];
	$source = imap_mime_header_decode($header);
	$result[] = new result;
	$result[0]->text='';
	$result[0]->charset='UTF-8';
	for ($j = 0; $j < count($source); $j++ )
       	{
	$element_charset =  ($source[$j]->charset == "default") ? $this->detect_charset($source[$j]->text) : $source[$j]->charset;
		if ($element_charset == 'x-unknown')
			$element_charset = 'UTF-8';

		if(empty($output_charset)) $output_charset = $default_charset;	
		$element_converted = function_exists(iconv) ? @iconv( $element_charset, $output_charset, $source[$j]->text): $source[$j]->text ;
		$result[$j]->text = $element_converted;
		$result[$j]->charset = $output_charset;
	}
	return $result;
}




function link_att(&$mail, $attach_tab, &$display_part_no,$ev)
{
	sort($attach_tab);
	$link = '';
	$ct = 0;
	while ($tmp = array_shift($attach_tab))
		if (!empty($tmp['name']))
			{
			$mime = str_replace('/', '-', $tmp['mime']);
			if ($display_part_no == true)
				//$link .= $tmp['number']-1 . '&nbsp;&nbsp;';
			unset($att_name);
			$att_name_array = imap_mime_header_decode($tmp['name']);
			for ($i=0; $i<count($att_name_array); $i++) {
				$att_name .= $att_name_array[$i]->text;
			}
			if(!preg_match("/unknown/",$att_name))
				$this->attname[$ct] = $att_name;	
			$att_name_dl = $att_name;
			$att_name = $this->convertLang2Html($att_name);
			if(!preg_match("/unknown/",$att_name)){	
				$link .= ($ct+1).'. <a href="index.php?module=Webmails&action=download&part=' . $tmp['number'] . '&mailid='.$ev.'&transfer=' . $tmp['transfer'] . '&filename=' . base64_encode($att_name_dl) . '&mime=' . $mime . '">' . $att_name . '</a>&nbsp;&nbsp;' . $tmp['mime'] . '&nbsp;&nbsp;' . $tmp['size'] . '<br/>';
				$this->anchor_arr[$ct] = '<a href="index.php?module=Webmails&action=download&part=' . $tmp['number'] . '&mailid='.$ev.'&transfer=' . $tmp['transfer'] . '&filename=' . base64_encode($att_name_dl) . '&mime=' . $mime . '">';
				$this->att_details[$ct]['name'] = $att_name;
				$this->att_details[$ct]['size'] = $tmp['size'];
				$this->att_details[$ct]['type'] = $tmp['mime'];
				$this->att_details[$ct]['part'] = $tmp['number'];
				$this->att_details[$ct]['transfer'] = $tmp['transfer'];
				$ct++;
			}
		}
	return ($link);
}

// Convert mail data (from, to, ...) to HTML
function convertMailData2Html($maildata, $cutafter = 0)
				{
	if (($cutafter > 0) && (strlen($maildata) > $cutafter)) 
					{
       		return htmlspecialchars(substr($maildata, 0, $cutafter)) . '&hellip;';
					}
					else
					{
             return htmlspecialchars($maildata);
					}
				}

	// Convert a language string to HTML
	function convertLang2Html($langstring) {
		global $charset;
		return htmlentities($langstring, 2, $charset);
	}

	function load_mail($attach_tab)
	{
		// parse the message
		global $default_charset;
		$ref_contenu_message =  @imap_headerinfo($this->mbox, $this->mailid);
		$struct_msg = @imap_fetchstructure($this->mbox, $this->mailid);
		$mail = $this->mbox;
		$ev = $this->mailid;
		$conf->display_rfc822 = true;
		if ($struct_msg->type == 3 || (isset($struct_msg->parts) && (sizeof($struct_msg->parts) > 0)))
		{
			$this->GetPart($attach_tab, $struct_msg, NULL, $conf->display_rfc822);
		}
		else
		{
			$pop_fetchheader_mail_ev = @imap_fetchheader($mail, $ev);
			$pop_body_mail_ev = @imap_body($mail, $ev);
			GetSinglePart($attach_tab, $struct_msg, $pop_fetchheader_mail_ev, $pop_body_mail_ev);
		}
		$conf->use_verbose = true;
		$header = "";
		if (($verbose == 1) && ($conf->use_verbose == true)) {
			$header = imap_fetchheader($mail, $ev);
		}

		$tmpvar = array_pop($attach_tab);
		if ($struct_msg->type == 3)
		{
			$body = '';
		}
		else
		{
			$body = @imap_fetchbody($mail,$ev,$tmpvar['number']);

		}



		if (preg_match("/text\/html/i", $tmpvar['mime']) || preg_match("/text\/plain/i", $tmpvar['mime']))
		{
			if ($tmpvar['transfer'] == 'QUOTED-PRINTABLE')
				$body = imap_qprint($body);
			if ($tmpvar['transfer'] == 'BASE64')
				$body = base64_decode($body);
			$body = remove_stuff($body, $tmpvar['mime']);
			$body_charset =  ($tmpvar['charset'] == "default") ? $this->detect_charset($body) : $tmpvar['charset'];


			if (strtolower($body_charset) == "us-ascii") {
				$body_charset = "UTF-8";
			}

			if ($body_charset == "" || $body_charset == null) {
				if (isset($conf->default_charset) && $conf->default_charset != "") {
					$body_charset = $conf->default_charset;
				} else {
					$body_charset = "UTF-8";
				}
			}

			if (isset($_REQUEST['user_charset']) && $_REQUEST['user_charset'] != '') {
				$body_charset = $_REQUEST['user_charset'];
			}
			$this->charsets = $body_charset;
			if(empty($GLOBALS['charset'])) $GLOBALS['charset'] = $default_charset;
			$body_converted = function_exists(iconv) ? @iconv( $body_charset, $GLOBALS['charset'], $body) : $body;
			$body = ($body_converted===FALSE) ? $body : $body_converted;
			$tmpvar['charset'] = ($body_converted===FALSE) ? $body_charset : $GLOBALS['charset'];
		}
		else
		{
			array_push($attach_tab, $tmpvar);
		}
		$link_att = '';
		$att_links = '';//variable added to display the attachments in full email view
		$conf->display_part_no = true;
		if ($struct_msg->subtype != 'ALTERNATIVE' || $struct_msg->subtype != 'RELATED')
		{
			switch (sizeof($attach_tab))
			{
			case 0:
				$link_att = '<span id="webmail_cont" style="display:none;"><tr><th class="mailHeaderLabel right"></th><td class="mailHeaderData"></td></tr></span>';
				break;
			case 1:
				$link_att = '<span id="webmail_cont" style="display:none;"><tr><th class="mailHeaderLabel right">' . $html_att . ':</th><td class="mailHeaderData">' . $this->link_att($mail, $attach_tab, $conf->display_part_no,$ev) . '</td></tr></span>';
				$this->att_links .= $this->link_att($mail, $attach_tab, $conf->display_part_no,$ev)."</br>";
				break;
			default:
				$link_att = '<span id="webmail_cont" style="display:none;"><tr><th class="mailHeaderLabel right">' . $html_atts . ':</th><td class="mailHeaderData">' . $this->link_att($mail, $attach_tab, $conf->display_part_no,$ev) . '</td></tr></span>';
				$this->att_links .= $this->link_att($mail, $attach_tab, $conf->display_part_no,$ev)."</br>";
				break;
			} 
		}else
			{
				$link_att = '<span id="webmail_cont" style="display:none;"><tr><th class="mailHeaderLabel right"></th><td class="mailHeaderData"></td></tr></span>';
			} 

		$struct_msg = @imap_fetchstructure($mail, $ev);
		$msg_charset = '';
		if ($struct_msg->ifparameters) {
			while ($obj = array_pop($struct_msg->parameters)) {
				if (strtolower($obj->attribute) == 'charset') {
					$msg_charset = $obj->value;
					break;
				}
			}
		}
		if ($msg_charset == '') {
			$msg_charset = 'UTF-8';
		}


		$subject_header = str_replace('x-unknown', $msg_charset, $ref_contenu_message->subject);
		$subject_array = $this->mime_header_decode($subject_header);
		for ($j = 0; $j < count($subject_array); $j++)
			$subject .= $subject_array[$j]->text;
		
		$from_header = str_replace('x-unknown', $msg_charset, $ref_contenu_message->fromaddress);
		$from_array = $this->mime_header_decode($from_header);
		for ($j = 0; $j < count($from_array); $j++)
			$from .= $from_array[$j]->text;
		//fixed the issue #3235
		$toheader = @imap_fetchheader($this->mbox, $this->mailid);
	        $to_arr = explode("To:",$toheader);
	        if(!stripos($to_arr[1],'mime')){
	                $to_add = stripos($to_arr[1],"CC:")?explode("CC:",$to_arr[1]):explode("Subject:",$to_arr[1]);
	                $to_header = trim($to_add[0]);
		}
		else
			$to_header = str_replace('x-unknown', $msg_charset, $ref_contenu_message->toaddress);
		$to_array = $this->mime_header_decode($to_header);
		for ($j = 0; $j < count($to_array); $j++)
			$to .= $to_array[$j]->text;
		$to = str_replace(',', ', ', $to);
		$this->to_header = $to_header;
		$cc_header = isset($ref_contenu_message->ccaddress) ? $ref_contenu_message->ccaddress : '';
		$cc_header = str_replace('x-unknown', $msg_charset, $cc_header);
		$cc_array = isset($ref_contenu_message->ccaddress) ? imap_mime_header_decode($cc_header) :0;
		if ($cc_array != 0) {
			for ($j = 0; $j < count($cc_array); $j++)
				$cc .= $cc_array[$j]->text;
		}
		$cc = str_replace(',', ', ', $cc);
		$this->cc_header = $cc_header;
		$reply_to_header = isset($ref_contenu_message->reply_toaddress) ? $ref_contenu_message->reply_toaddress : '';
		$reply_to_header = str_replace('x-unknown', $msg_charset, $reply_to_header);
		$reply_to_array = isset($ref_contenu_message->reply_toaddress) ? imap_mime_header_decode($reply_to_header) : 0;
		if ($reply_to_array != 0) {
			for ($j = 0; $j < count($reply_to_array); $j++)
				$reply_to .= $reply_to_array[$j]->text;
		}

		$timestamp = chop($ref_contenu_message->udate);
		$date = format_date($timestamp, $lang);
		$time = format_time($timestamp, $lang);
		$content = Array(
			'from' => $from,
			'to' => $to,
			'cc' => $cc,
			'reply_to' => $reply_to,
			'subject' => $subject,
			'date' => $date,
			'time' => $time,
			'complete_date' => $date,
			'att' => $link_att,
			'body' => $this->graphicalsmilies($body),
			'body_mime' => $this->convertLang2Html($tmpvar['mime']),
			'body_transfer' => $this->convertLang2Html($tmpvar['transfer']),
			'header' => $header,
			'verbose' => $verbose,
			'prev' => $prev_msg,
			'next' => $next_msg,
			'msgnum' => $mail,
			'attachtab' => $attach_tab,
			'charset' => $body_charset
		);
		return ($content);
	}

	// get the body of a part of a message according to the
	// string in $part
	function mail_fetchpart($part)
	{
		$parts = $this->mail_fetchparts();

		$partNos = explode(".", $part);

		$currentPart = $parts;
		while(list ($key, $val) = each($partNos))
		{
			$currentPart = $currentPart[$val];
		}

		if ($currentPart != "") return $currentPart;
		else return false;
	}

	// splits a message given in the body if it is
	// a mulitpart mime message and returns the parts,
	// if no parts are found, returns false
	function mail_mimesplit($header, $body)
	{
		$parts = array();

		$PN_EREG_BOUNDARY = "/Content-Type:(.*)boundary=\"([^\"]+)\"/i";

		if (preg_match ($PN_EREG_BOUNDARY, $header, $regs))
		{
			$boundary = $regs[2];

			$delimiterReg = "/([^\r\n]*)$boundary([^\r\n]*)/i";
			if (preg_match ($delimiterReg, $body, $results))
			{
				$delimiter = $results[0];
				$parts = explode($delimiter, $body);
				$parts = array_slice ($parts, 1, -1);
			}

			return $parts;
		}
		else
		{
			return false;
		}   
	}

	// returns an array with all parts that are
	// subparts of the given part
	// if no subparts are found, return the body of
	// the current part
	function mail_mimesub($part)
	{
		$i = 1;
		$headDelimiter = "\r\n\r\n";
		$delLength = strlen($headDelimiter);

		// get head & body of the current part
		$endOfHead = strpos( $part, $headDelimiter);
		$head = substr($part, 0, $endOfHead);
		$body = substr($part, $endOfHead + $delLength, strlen($part));

		// check whether it is a message according to rfc822
		if (stristr($head, "Content-Type: message/rfc822"))
		{
			$part = substr($part, $endOfHead + $delLength, strlen($part));
			$returnParts[1] = $this->mail_mimesub($part);
			return $returnParts;
			// if no message, get subparts and call function recursively
		}
		elseif ($subParts = $this->mail_mimesplit($head, $body))
		{
			// got more subparts
			while (list ($key, $val) = each($subParts))
			{
				$returnParts[$i] = $this->mail_mimesub($val);
				$i++;
			}           
			return $returnParts;
			}
			else
			{
				return $body;
			}
	}

	// get an array with the bodies all parts of an email
	// the structure of the array corresponds to the
	// structure that is available with imap_fetchstructure
	function mail_fetchparts()
	{
		$parts = array();
		$header = imap_fetchheader($this->mbox, $this->mailid);
		$body = imap_body($this->mbox, $this->mailid, FT_INTERNAL);

		$i = 1;

		if ($newParts = $this->mail_mimesplit($header, $body))
		{
			while (list ($key, $val) = each($newParts))
			{
				$parts[$i] = $this->mail_mimesub($val);
				$i++;               
			}
		}
		else
		{
			$parts[$i] = $body;
		}
		return $parts;
	}





    
}
function decode_header($string)
{
        $elements = imap_mime_header_decode($string);
        for ($i=0; $i<count($elements); $i++) {
                $result .= $elements[$i]->text;
        }
        return $result;
}
?>
