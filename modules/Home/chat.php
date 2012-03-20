<?php
/*
    Copyright 2005 Rolando Gonzalez (rolosworld@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**** config  *****/

/**
 * MySQL server configuration
 */
include_once('config.php');
require_once('include/utils/utils.php');

$db = array();

$db['host'] = $dbconfig['db_server']."".$dbconfig['db_port'];
$db['user'] = $dbconfig['db_username'];
$db['pass'] = $dbconfig['db_password'];
$db['database'] = $dbconfig['db_name'];

/**
 * Constants for the chat
 */
$chat_conf = array();
$chat_conf['alive_time'] = "30"; // time users should report to be online, in seconds.
$chat_conf['msg_limit'] = "10"; // maximum msg's to send in one request.

/*************************************************************/
/*** YOU SHOULD NOT NEED TO EDIT ANYTHING ELSE BELOW THIS. ***/
/*************************************************************/

session_name("AjaxPopupChat");
//session_save_path("sessions");
session_start();

$dbh = mysql_connect($db['host'], $db['user'],$db['pass']) or die ('I cannot connect to the database');

mysql_select_db($db['database']);

function mysqlQuery($query)
{
  $result = mysql_query($query);
  if(!$result)
    {
      die("DB Error.<br />\n".mysql_error()."<br />\n".$query);
    }
  return $result;
}

/**** handler *****/
/**
 * Chat object
 */
class Chat
{
  // stores the string to be returned
  var $json;
  
  function Chat()
  {
	global $adb;
    $this->json = '';
    
    // las message id received by user
    if(!isset($_SESSION["mlid"]))
      {
	$res = $adb->pquery("show table status like 'vtiger_chat_msg'", array());
	$line = $adb->fetch_array($res);
	if(intval($line['Auto_increment']) == 0)
	  $_SESSION["mlid"] = 0;
	else
	  $_SESSION["mlid"] = intval($line['Auto_increment']) - 1;
      }
    
    // when the las user list was sended.
    if(!isset($_SESSION["lul"]))
      {
	$_SESSION["lul"] = 0;
      }

    // check if user is active.
    if(!isset($_SESSION['chat_user']))
      {
	$this->setUserNick();
      }
    else
      {
	$res = $adb->pquery("update vtiger_chat_users set ping=now() where session=?", array(session_id()));
	if($adb->getAffectedRowCount($res) == 0)
	  {
	    $this->setUserNick();
	  }
      }
    
    switch($_POST['submode'])
      {
	// request all the json data at once.
      case 'get_all':
	global $chat_conf;
	$this->lastMsgId();
	
	$this->json = '[%s]';
	$this->getAllPVChat();
	$pvchat = $this->json;

	$this->json = '[%s]';
	$this->getPubChat();
	$pchat = $this->json;

	$this->json = '';
	if(time() - $_SESSION["lul"] > $chat_conf['alive_time'])
	  {
	    $_SESSION["lul"] = time();
	    $this->json = '[%s]';
	    $this->getUserList();
	  }
	$ulist = $this->json;
	
	$tmp = array();
	$this->json = '{%s}';
	if(strlen($ulist) > 0)
	  $tmp[] = '"ulist":'.$ulist;
	
	if(strlen($pvchat) > 0)
	  $tmp[] = '"pvchat":'.$pvchat;
	
	if(strlen($pchat) > 0)
	  $tmp[] = '"pchat":'.$pchat;
	
	$this->json = sprintf($this->json, implode(',',$tmp));
	break;

	// user is submiting a msg
      case 'submit':
	$this->submit($_POST['msg'],intval($_POST['to']));
	break;

	// user closed a private chat
      case 'pvclose':
	$this->pvClose(intval($_POST['to']));
	break;

      default:
	break;
      }
  }
  
  /**
   * returns the JSON created
   */
  function getAJAX()
  {
    return $this->json;
  }
  
  /**
   * Sets the user initial nickname.
   */
  function setUserNick()
  {
	global $current_user, $adb; 
    $res = $adb->pquery("select id from vtiger_chat_users where session=?", array(session_id()));
    if($adb->num_rows($res) > 0)
      {
	$line = $adb->fetch_array($res);
	$_SESSION['chat_user'] = $line['id'];
	return;
      }
    
    $res = $adb->pquery("show table status like 'vtiger_chat_users'", array());
    $line = $adb->fetch_array($res);
    if(intval($line['Auto_increment']) == 0)
      $line['Auto_increment'] = 1;
    
    $_SESSION['chat_user'] = $line['Auto_increment'];
    
	
    $sql = "insert into vtiger_chat_users(nick,session,ping,ip) values (?,?, now(), ?)";
    $params = array($current_user->user_name, session_id(), $_SERVER['REMOTE_ADDR']);
	$res = $adb->pquery($sql, $params);
  }
  
  /**
   * generate the available users list
   */
  function getUserList()
  {
    global $chat_conf, $adb;
    $tmp = '';
    $sql = "delete from vtiger_chat_users where ((unix_timestamp(now())-unix_timestamp(ping))>?)";
	$params = array($chat_conf['alive_time']);
	$res = $adb->pquery($sql, $params);
	
    $res = $adb->pquery("select id,nick from vtiger_chat_users", array());
    if($adb->num_rows($res)==0)
      {
	$this->json = '';
	return;
      }

    while($line = $adb->fetch_array($res))
    {
		if($line['id'] != $_SESSION['chat_user'])
	  		$tmp .= '{"uid":'.$line['id'].',"nick":"'.$line['nick'].'"},';
    }
    $tmp = trim($tmp,',');
    $this->json = sprintf($this->json,$tmp);
  }

  /**
   * Sets user last post received.
   */
  function lastMsgId()
  {
    if(isset($_POST['mlid']) && intval($_POST['mlid']) > $_SESSION["mlid"])
      $_SESSION["mlid"] = intval($_POST['mlid']);
  }

  /**
   * generates the private chat data
   */
  function getAllPVChat()
  {
    global $chat_conf, $adb;
    $format = '{"mlid":%s,"chat":%s,"from":"%s","msg":"%s"},';
	$sql ="select ms.id mid,ms.chat_from mfrom,ms.chat_to mto,pv.id id,us.nick `chat_from`,ms.msg msg from vtiger_chat_users us,vtiger_chat_pvchat pv,vtiger_chat_msg ms where pv.msg=ms.id and us.id=ms.chat_from and ms.id>? and ((ms.chat_from=? and ms.chat_to>0) or (ms.chat_to=? and ms.chat_from>0)) order by ms.born limit 0, " . $chat_conf['msg_limit'];
    $params = array($_SESSION['mlid'], $_SESSION['chat_user'], $_SESSION['chat_user']);
	$res = $adb->pquery($sql, $params);
	
	if($adb->num_rows($res)==0)
      {
	$this->json = '';
	return;
      }

    $tmp = '';
    while($line = $adb->fetch_array($res))
      {
	if($line['mfrom'] == $_SESSION['chat_user'])
	  $cid = $line['mto'];
	else
	  $cid = $line['mfrom'];

	$tmp .= sprintf($format,$line['mid'],$cid,$line['chat_from'],addslashes($line['msg']));
      }
    $tmp = trim($tmp,',');
    $this->json = sprintf($this->json,$tmp);
  }


  /**
   * generates the public chat data
   * NOTE: this is alpha
   */
  function getPubChat()
  {
    global $chat_conf, $adb;
    $format = '{"mlid":%s,"from":"%s","msg":"%s"},';
    $sql = "select ms.id mid,ms.chat_from mfrom,ms.chat_to mto,p.id id,us.nick `chat_from`,ms.msg msg from vtiger_chat_users us,vtiger_chat_pchat p,vtiger_chat_msg ms where p.msg=ms.id and us.id=ms.chat_from and ms.id>? and ms.chat_to=0 order by ms.born limit 0," . $chat_conf['msg_limit'];
    $params = array($_SESSION['mlid']);
	$res = $adb->pquery($sql, $params);
	
	if($adb->num_rows($res)==0)
      {
	$this->json = '';
	return;
      }

    $tmp = '';
    while($line = $adb->fetch_array($res))
      {
	$tmp .= sprintf($format,$line['mid'],$line['chat_from'],addslashes($line['msg']));
      }
    $tmp = trim($tmp,',');
    $this->json = sprintf($this->json,$tmp);
  }

  /**
   * Check for special commands on message.
   */
  function msgParse($msg)
  {
	global $adb;
    if(strlen($msg) == 0) return '';
    $msg = stripslashes($msg);

    if($msg[0] == '\\')
      {
	$today_date = getdate();
		  
	$words = explode(" ",$msg);
	switch($words[0])
	  {
	  case '\nick':
	    if(isset($words[1]) && strlen($words[1]) > 3)
	      {
			$res = $adb->pquery("select nick from vtiger_chat_users where id=?", array($_SESSION['chat_user']));
			$line = $adb->fetch_array($res);
			$res = $adb->pquery("update vtiger_chat_users set nick=? where id=?", array($words[1], $_SESSION['chat_user']));
			$msg = '\sys <span class="sysb">'.$line['nick'].'</span> changed nick to <span class="sysb">'.$words[1].'</span>';
	      }
	    break;
	    
	  case '\help':
		$msg = '\sys <br><span class="sysb">\\\\nick "nickname" </span> - change nick<br><span class="sysb">\\\\date </span> - date<br><span class="sysb">\\\\time </span> - time<br><span class="sysb">\\\\month </span> - month<br><span class="sysb">\\\\day </span> - weekday';
	   break;
	  case '\date':
       		$msg = '\sys Today is <span class="sysb">'.date('d-m-Y').'</span>';		  
	   break;	
	   case '\time':
       		$msg = '\sys The Current time is <span class="sysb">'.$today_date["hours"].':'.$today_date["minutes"].':'.$today_date["hours"].'</span>';		 break;	
	case '\month':
       		$msg = '\sys <span class="sysb">'.$today_date["month"].'</span>';		 
	break;
	case '\day':
       		$msg = '\sys <span class="sysb">'.$today_date["weekday"].'</span>';		 
	break;		
	 default:
		  
	    $msg = '\sys Bad command: '.$words[0];
	    break;
	  }
      }
    return $msg;    
  }

  /**
   * process a submited msg
   */
  function submit($msg, $to=0)
  {
	global $adb;
    //UTF-8 support added - ding
    $msg = utf8RawUrlDecode($msg);
    $msg = $this->msgParse($msg);
    $msg = htmlentities($msg);
    if(strlen($msg) == 0) return;
	
	//$sql = "insert into vtiger_chat_msg set chat_from=?, chat_to=?, born=now(), msg=?";
    $sql = "insert into vtiger_chat_msg(chat_from, chat_to, born, msg) values (?,?, now(), ?)";
    $params = array($_SESSION['chat_user'], $to, $msg);
	$res = $adb->pquery($sql, $params);
	
    $chat = "p";
    if($to != 0)
      $chat .= "v";
    
    $res = $adb->pquery("insert into vtiger_chat_".$chat."chat set msg=LAST_INSERT_ID()", array());
  }

  /**
   * removes the private conversation msg's because someone closed it
   */
  function pvClose($to)
  {
	global $adb;
    $sql = "delete from vtiger_chat_msg where (`chat_from`=? and `chat_to`=?) or (`chat_from`=? and `chat_to`=?)";
	$params = array($to, $_SESSION['chat_user'], $_SESSION['chat_user'], $to);
	$res = $adb->pquery($sql, $params);  
  }
}

/**** caller ****/
$chat = new Chat();
echo $chat->getAJAX();
?>
