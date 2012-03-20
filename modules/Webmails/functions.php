<?php
/*
 * $Header: /cvsroot/nocc/nocc/webmail/functions.php,v 1.225 2006/12/10 08:47:44 goddess_skuld Exp $ 
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 * Copyright 2002 Mike Rylander <mrylander@mail.com>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 */


/* ----------------------------------------------------- */

function inbox(&$pop, $skip = 0, &$ev)
{
    global $conf;
    global $charset;

    $user_prefs = $_SESSION['nocc_user_prefs'];

    $msg_list = array();

    $lang = $_SESSION['nocc_lang'];
    $sort = $_SESSION['nocc_sort'];
    $sortdir = $_SESSION['nocc_sortdir'];

    $num_msg = $pop->num_msg();
    $per_page = get_per_page();

    $start_msg = $skip * $per_page;
    $end_msg = $start_msg + $per_page;

    $sorted = $pop->sort($sort, $sortdir, $ev, true);
    if(NoccException::isException($ev)) return;

    $end_msg = ($num_msg > $end_msg) ? $end_msg : $num_msg;
    if ($start_msg > $num_msg) {
        return $msg_list;
    }

    for ($i = $start_msg; $i < $end_msg; $i++)
    {
        $subject = $from = $to = '';
        $msgnum = $sorted[$i];
        $pop_msgno_msgnum = $pop->msgno($msgnum);
        $ref_contenu_message = $pop->headerinfo($pop_msgno_msgnum, $ev);
        if(NoccException::isException($ev)) return;
        $struct_msg = $pop->fetchstructure($pop_msgno_msgnum, $ev);
        if(NoccException::isException($ev)) return;

        // Get message charset
	$msg_charset = '';
	if ($struct_msg->ifparameters) {
	  while ($obj = array_pop($struct_msg->parameters))
	    if (strtolower($obj->attribute) == 'charset') {
	      $msg_charset = $obj->value;
	      break;
	    }
	}
	if ($msg_charset == '') {
	  $msg_charset = 'UTF-8';
	}

	// Get subject
        $subject_header = str_replace('x-unknown', $msg_charset, $ref_contenu_message->subject);
        $subject_array = nocc_imap::mime_header_decode($subject_header);
        
	for ($j = 0; $j < count($subject_array); $j++)
            $subject .= $subject_array[$j]->text;

	// Get from
	$from_header = str_replace('x-unknown', $msg_charset, $ref_contenu_message->fromaddress);
        $from_array = nocc_imap::mime_header_decode($from_header);
        for ($j = 0; $j < count($from_array); $j++)
            $from .= $from_array[$j]->text;

	// Get to
	$to_header = str_replace('x-unknown', $msg_charset, $ref_contenu_message->toaddress);
        $to_array = nocc_imap::mime_header_decode($to_header);
        for ($j = 0; $j < count($to_array); $j++) {
            $to = $to . $to_array[$j]->text . ", ";
        }
        $to = substr($to, 0, strlen($to)-2);
        $msg_size = 0;
        if ($pop->is_imap())
            $msg_size = get_mail_size($struct_msg);
        else
            if(isset($struct_msg->bytes))
                $msg_size = ($struct_msg->bytes > 1000) ? ceil($struct_msg->bytes / 1000) : 1;
        if (isset($struct_msg->type) && ( $struct_msg->type == 1 || $struct_msg->type == 3))
        {
            if ($struct_msg->subtype == 'ALTERNATIVE' || $struct_msg->subtype == 'RELATED')
                $attach = '&nbsp;';
            else
                $attach = '<img src="themes/' . $_SESSION['nocc_theme'] . '/img/attach.png" alt="" />';
        }
        else
            $attach = '&nbsp;';
        // Check Status Line with UCB POP Server to
        // see if this is a new message. This is a
        // non-RFC standard line header.
        // Set this in conf.php
        if ($conf->have_ucb_pop_server)
        {
            $header_msg = $pop->fetchheader($pop->msgno($msgnum), $ev);
            if(NoccException::isException($ev)) return;
            $header_lines = explode("\r\n", $header_msg);
            while (list($k, $v) = each($header_lines))
            {
                list ($header_field, $header_value) = explode(':', $v);
                if ($header_field == 'Status') 
                    $new_mail_from_header = $header_value;
            }
        }
        else
        {
            if (($ref_contenu_message->Unseen == 'U') || ($ref_contenu_message->Recent == 'N'))
                $new_mail_from_header = '';
            else
                $new_mail_from_header = '&nbsp;';
        }
        if ($new_mail_from_header == '')
            $newmail = '<img src="themes/' . $_SESSION['nocc_theme'] . '/img/new.png" alt=""/>';
        else
            $newmail = '&nbsp;';
        $timestamp = chop($ref_contenu_message->udate);
        $date = format_date($timestamp, $lang);
        $time = format_time($timestamp, $lang);
        $msg_list[$i] =  Array(
                'new' => $newmail, 
                'number' => $pop->msgno($msgnum),
                'attach' => $attach,
                'to' => $to,
                'from' => $from,
                'subject' => $subject, 
                'date' => $date,
                'time' => $time,
                'complete_date' => $date, 
                'size' => $msg_size,
                'sort' => $sort,
                'sortdir' => $sortdir);
    }
    return ($msg_list);
}


/* ----------------------------------------------------- */

// BUG: returns text/plain when Content-Type: application/x-zip (e.g.)

function GetSinglePart(&$attach_tab, &$this_part, &$header, &$body)
{
    if (preg_match("/text\/html/i", $header))
        $full_mime_type = 'text/html';
    else
	    $full_mime_type = 'text/plain';

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
    $charset = '';
    if ($this_part->ifparameters)
        while ($obj = array_pop($this_part->parameters))
            if (strtolower($obj->attribute) == 'charset')
            {
                $charset = $obj->value;
                break;
            }
            $tmpvar = Array(
                'number' => 1,
                'id' => $this_part->ifid ? $this_part->id : 0,
                'name' => '',
                'mime' => $full_mime_type,
                'transfer' => $encoding,
                'disposition' => $this_part->ifdisposition ? $this_part->disposition : '',
                'charset' => $charset
            );
	    if(isset($this_part->bytes))
		    $tmpvar['size'] = ($this_part->bytes > 1000) ? ceil($this_part->bytes / 1000) : 1;

            array_unshift($attach_tab, $tmpvar);
}

/* ----------------------------------------------------- */

function remove_stuff(&$body, &$mime)
{
    $PHP_SELF = $_SERVER['PHP_SELF'];

    $lang = $_SESSION['nocc_lang'];

    if (preg_match("/html/i", $mime))
    {
        $to_removed_array = array (
            "'<html>'si",
            "'</html>'si",
            "'<body[^>]*>'si",
            "'</body>'si",
            "'<head[^>]*>.*?</head>'si",
            "'<style[^>]*>.*?</style>'si",
            "'<script[^>]*>.*?</script>'si",
            "'<object[^>]*>.*?</object>'si",
            "'<embed[^>]*>.*?</embed>'si",
            "'<applet[^>]*>.*?</applet>'si",
            "'<mocha[^>]*>.*?</mocha>'si",
            "'<meta[^>]*>'si"
        );
	$body = preg_replace($to_removed_array, '', $body);
	//this line is not needed, commented to fix #3245
	//$body=preg_replace("/(http:\/\/|ftp:\/\/)([^\s,]*)/i","<a href='$1$2'>$1$2</a> target=_blank",$body    );
        $body = preg_replace("|href=\"(.*)script:|i", 'href="nocc_removed_script:', $body);
        $body = preg_replace("|<([^>]*)java|i", '<nocc_removed_java_tag', $body);
        $body = preg_replace("|<([^>]*)&{.*}([^>]*)>|i", "<&{;}\\3>", $body);
		$body = preg_replace("/href=\"mailto:([a-zA-Z0-9+-=%&:_.~?@]+[#a-zA-Z0-9+]*)\"/i","HREF=\"mailto:\\1\"", $body);
        $body = preg_replace("/href=mailto:([a-zA-Z0-9+-=%&:_.~?@]+[#a-zA-Z0-9+]*)/i","HREF=\"$PHP_SELF?action=write&amp;mail_to=\\1\"", $body);
        $body = preg_replace("/href=\"([a-zA-Z0-9+-=%&:_.~?]+[#a-zA-Z0-9+]*)\"/i","href=\"javascript:void(0);\" onclick=\"window.open('\\1');\"", $body);
        $body = preg_replace("/href=([a-zA-Z0-9+-=%&:_.~?]+[#a-zA-Z0-9+]*)/i","href=\"javascript:void(0);\" onclick=\"window.open('\\1');\"", $body);
    }
    elseif (preg_match("/plain/i", $mime))
    {
        $user_prefs = $_SESSION['nocc_user_prefs'];
        $body = htmlspecialchars($body);
        $body = preg_replace("/(http|https|ftp):\/\/([a-zA-Z0-9+-=%&:_.~?]+[#a-zA-Z0-9+]*)/","<a href=\"javascript:void(0);\" onclick=\"window.open('\\1://\\2');\">\\1://\\2</a>", $body);
        // Bug #511302: Comment out following line if you have the 'Invalid Range End' problem
        // New rewritten preg_replace should fix the problem, bug #522389
	$body = preg_replace("/([0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-.]?[0-9a-zA-Z])*\.[a-zA-Z]{2,})/", "<a href=\"mailto:\\1\">\\1</a>", $body);
        if ( !isset($user_prefs->colored_quotes) || (isset($user_prefs->colored_quotes) && $user_prefs->colored_quotes)) {
          $body = preg_replace('/^(&gt; *&gt; *&gt; *&gt; *&gt;)(.*?)(\r?\n)/m', '<span class="quoteLevel5">\\1\\2</span>\\3', $body);
          $body = preg_replace('/^(&gt; *&gt; *&gt; *&gt;)(.*?)(\r?\n)/m', '<span class="quoteLevel4">\\1\\2</span>\\3', $body);
          $body = preg_replace('/^(&gt; *&gt; *&gt;)(.*?)(\r?\n)/m', '<span class="quoteLevel3">\\1\\2</span>\\3', $body);
          $body = preg_replace('/^(&gt; *&gt;)(.*?)(\r?\n)/m', '<span class="quoteLevel2">\\1\\2</span>\\3', $body);
          $body = preg_replace('/^(&gt;)(.*?)(\r?\n)/m', '<span class="quoteLevel1">\\1\\2</span>\\3', $body);
        }
        if (isset($user_prefs->display_struct) && $user_prefs->display_struct) {
          $body = preg_replace('/(\s)\+\/-/', '\\1&plusmn;', $body); // +/-
          $body = preg_replace('/(\w|\))\^([0-9]+)/', '\\1<sup>\\2</sup>', $body); // 10^6, a^2, (a+b)^2
          $body = preg_replace('/(\s)(\*)([^\s\*]+[^\*\r\n]+)(\*)/', '\\1<strong>\\2\\3\\4</strong>', $body); // *strong*
          $body = preg_replace('/(\s)(\/)([^\s\/]+[^\/\r\n<>]+)(\/)/', '\\1<em>\\2\\3\\4</em>', $body); // /emphasis/
          $body = preg_replace('/(\s)(_)([^\s_]+[^_\r\n]+)(_)/', '\\1<span style="text-decoration:underline">\\2\\3\\4</span>', $body); // _underline_
          $body = preg_replace('/(\s)(\|)([^\s\|]+[^\|\r\n]+)(\|)/', '\\1<code>\\2\\3\\4</code>', $body); // |code|

        }
        $body = nl2br($body);
        if (function_exists('wordwrap'))
            $body = wordwrap($body, 80, "\n");
    }    
    return ($body);
}

/* ----------------------------------------------------- */

function link_att(&$mail, $attach_tab, &$display_part_no)
{
    sort($attach_tab);
    $link = '';
    while ($tmp = array_shift($attach_tab))
        if (!empty($tmp['name']))
        {
            $mime = str_replace('/', '-', $tmp['mime']);
            if ($display_part_no == true)
                $link .= $tmp['number'] . '&nbsp;&nbsp;';
            unset($att_name);
            $att_name_array = imap_mime_header_decode($tmp['name']);
            for ($i=0; $i<count($att_name_array); $i++) {
              $att_name .= $att_name_array[$i]->text;
            }
            $att_name_dl = $att_name;
            $att_name = convertLang2Html($att_name);
            $link .= '<a href="download.php?mail=' . $mail . '&amp;part=' . $tmp['number'] . '&transfer=' . $tmp['transfer'] . '&filename=' . base64_encode($att_name_dl) . '&mime=' . $mime . '">' . $att_name . '</a>&nbsp;&nbsp;' . $tmp['mime'] . '&nbsp;&nbsp;' . $tmp['size'] . '<br/>';
        }
    return ($link);
}

/* ----------------------------------------------------- */
// Return date formatted as a string, according to locale

function format_date(&$date, &$lang)
{
    global $default_date_format;
    global $lang_locale;
    global $no_locale_date_format;

    // handle bad inputs
    if (empty($date))
        return '';

    // if locale can't be set, use default for no locale
    if (!setlocale (LC_TIME, $lang_locale))
        $default_date_format = $no_locale_date_format;

    // format dates
    return strftime($default_date_format, $date); 
}

function format_time(&$time, &$lang)
{
    global $default_time_format;
    global $lang_locale;

    // handle bad inputs
    if (empty($time))
        return '';

    // if locale can't be set, use default for no locale
    setlocale (LC_TIME, $lang_locale);

    // format dates
    return strftime($default_time_format, $time); 
}


/* ----------------------------------------------------- */

// We have to figure out the entire mail size
function get_mail_size(&$this_part)
{
    $size = (isset($this_part->bytes) ? $this_part->bytes : 0);
    if (isset($this_part->parts))
        for ($i = 0; $i < count($this_part->parts); $i++)
            $size += (isset($this_part->parts[$i]->bytes) ? $this_part->parts[$i]->bytes : 0);
    $size = ($size > 1000) ? ceil($size / 1000) : 1;
    return ($size);
}

/* ----------------------------------------------------- */

// this function build an array with all the recipients of the message for later reply or reply all 
function get_reply_all(&$from, &$to, &$cc)
{
    $login = $_SESSION['nocc_login'];
    $domain = $_SESSION['nocc_domain'];
    if (!preg_match("/$login@$domain/i", $from))
        $rcpt = $from.'; ';
    $tab = explode(',', $to);
    while ($tmpvar = array_shift($tab))
        if (!preg_match("/$login@$domain/i", $tmpvar))
            $rcpt .= $tmpvar.'; ';
    $tab = explode(',', $cc);
    while ($tmpvar = array_shift($tab))
        if (!preg_match("/$login@$domain/i", $tmpvar))
            $rcpt .= $tmpvar.'; ';
    $rcpt = isset($rcpt) ? substr($rcpt, 0, strlen($rcpt) - 2) : $from;
    return ($rcpt);
}

/* ----------------------------------------------------- */

// We need that to build a correct list of all the recipient when we send a message
function cut_address(&$addr, &$charset)
{
    global $charset;
    // Strip slashes from input
    $addr = safestrip($addr);

    // Break address line into individual addresses, taking
    // quoted addresses into account
    $addresses = array();
    $token = '';
    $quote_esc = false;
    for ($i = 0; $i < strlen($addr); $i++) {
        $c = substr($addr, $i, 1);

        // Are we entering/leaving escaped mode
        if($c == '"') {
            $quote_esc = !$quote_esc;
        }

        // Is this an address seperator (comma/semicolon)
        if($c == ',' || $c == ';') {
            if(!$quote_esc) {
                $token = trim($token);
                if($token != '') {
                    $addresses[] = $token;
                }
                $token = '';
                continue;
            }
        }

        $token .= $c;
    }
    if(!$quote_esc) {
        $token = trim($token);
        if($token != '') {
            $addresses[] = $token;
        }
    }

    /* old way
    // Replace commas with semicolons as address seperator
    $addr = str_replace(',', ';', $addr);

    // Break address line into individual addresses
    $addresses = explode(';', $addr);
    */

    // Loop through addresses
    for ($i = 0; $i < sizeof($addresses); $i++)
    {
        // Wrap address in brackets, if not already
        $pos = strrpos($addresses[$i], '<');
        if (!is_int($pos))
            $addresses[$i] = '<'.$addresses[$i].'>';

        else
        {
            $name = '';
            if ($pos != 0)
                $name = '=?'.$charset.'?B?'.base64_encode(substr($addresses[$i], 0, $pos - 1)).'?= ';
            $addr = substr($addresses[$i], $pos);
            $addresses[$i] = '"'.$name.'" '.$addr.'';
        }
    }
    return ($addresses);
}

/* ----------------------------------------------------- */

function view_part(&$pop, &$mail, $part_no, &$transfer, &$msg_charset, &$charset)
{
    if(NoccException::isException($ev)) {
        return "<p class=\"error\">".$ev->getMessage."</p>";
    }
    $text = $pop->fetchbody($mail, $part_no, $ev);
    if(NoccException::isException($ev)) {
        return "<p class=\"error\">".$ev->getMessage."</p>";
    }
    if ($transfer == 'BASE64')
        $str = nl2br(nocc_imap::base64($text));
    elseif($transfer == 'QUOTED-PRINTABLE')
        $str = nl2br(quoted_printable_decode($text));
    else
        $str = nl2br($text);
    return ($str);
}

/* ----------------------------------------------------- */

function encode_mime(&$string, &$charset)
{ 
    /*$text = '=?' . $charset . '?Q?'; 
    for($i = 0; $i < strlen($string); $i++ )
    { 
        $val = ord($string[$i]); 
        $val = dechex($val); 
        $text .= '=' . $val; 
    } 
    $text .= '?='; 
    return ($text);
    */
    $string = rawurlencode($string);
    $string = str_replace('%', '=', $string);
    $string = '=?' . $charset . '?Q?' . $string . '?=';
    return ($string);
} 

/* ----------------------------------------------------- */

// This function removes temporary attachment files and
// removes any attachment information from the session
function clear_attachments()
{
    global $conf;
    if (isset($_SESSION['nocc_attach_array']) && is_array($_SESSION['nocc_attach_array']))
        while ($tmpvar = array_shift($_SESSION['nocc_attach_array']))
            @unlink($conf->tmpdir.'/'.$tmpvar->tmp_file);
    unset($_SESSION['nocc_attach_array']);
}

/* ----------------------------------------------------- */

// This function chops the <mail@domain.com> bit from a 
// full 'Blah Blah <mail@domain.com>' address, or not
// depending on the 'hide_addresses' preference.
function display_address(&$address)
{
    global $html_att_unknown;
    // Check for null
    if($address == '')
        return $html_att_unknown;

    // Get preference
    $user_prefs = $_SESSION['nocc_user_prefs'];

    // If not set, return full address.
    if(!isset($user_prefs->hide_addresses))
        return $address;

    if($user_prefs->hide_addresses!=1 && $user_prefs->hide_addresses!="on")
         return $address; 

    // If no '<', return full address.
    $bracketpos = strpos($address, "<");
    if($bracketpos === false)
        return $address;

    // Return up to the first '<', or end of string if not found
    //return substr($address, 0, $bracketpos - 1);
    $formatted_address = '';
    while (!($bracketpos === false)) {
      $formatted_address = substr($address, 0, $bracketpos - 1);
      $formatted_address .= substr($address, strpos($address, ">")+1);
      $address = $formatted_address;
      $bracketpos = strpos($address, "<");
    }
    return $address;
}

/* ----------------------------------------------------- */

function mailquote(&$body, &$from, $html_wrote)
{
    $user_prefs = $_SESSION['nocc_user_prefs'];

  $crlf = "\r\n";
  $from = ucwords(trim(preg_replace("/&lt;.*&gt;/", "", str_replace("\"", "", $from))));

  if (isset($user_prefs->wrap_msg)) {
    $wrap_msg = $user_prefs->wrap_msg;
  } else {
    $wrap_msg = 0;
  }
  // If we must wrap the message
  if ($wrap_msg)
    {
      $msg = '';
      //Break message in table with "\r\n" as separator
      $tbl = explode ("\r\n", $body);
      // For each line
      for ($i = 0, $buffer = ''; $i < count ($tbl); ++$i)
	{
	  unset($buffer);
	  // Number of "> "
	  $q = substr_count($tbl[$i], "> ");

	  $tbl[$i] = rtrim ($tbl[$i]);
	  // Erase the "> "
	  $tbl[$i] = str_replace ("> ", "", $tbl[$i]);
	  // Erase the break line
	  $tbl[$i] = str_replace ("\n", " ", $tbl[$i]);
	  // length of "> > ...."
	  $length = ($q + 1) * strlen ("> ");
	  // Add the quote if ligne is not to long
	  if (strlen ($tbl[$i]) + $length <= $wrap_msg)
	    $msg .= str_pad($tbl[$i], strlen ($tbl[$i]) + $length, "> ", STR_PAD_LEFT) . $crlf;
	  // If line is to long, create new line
	  else
	    {
	      $words = explode (" ", $tbl[$i]);
	      for ($j = 0; $j < count ($words); ++$j)
		{
		  if (strlen ($buffer) + strlen ($words[$j]) + $length <= $wrap_msg)
		    $buffer .= $words[$j] . " ";
		  else
		    {
		      $msg .=  str_pad(rtrim ($buffer), strlen (rtrim ($buffer)) + $length, "> ", STR_PAD_LEFT) . $crlf;
		      $buffer = $words[$j] . " ";
		    }
		}
	      //if ($q != substr_count($tbl[$i + 1], "> "))
		$msg .= str_pad(rtrim ($buffer), strlen (rtrim ($buffer)) + $length, "> ", STR_PAD_LEFT) . $crlf;
	    }
	}
      $body = $msg;
    }
  else
    $body = "> " . preg_replace("/\n/", "\n> ", trim($body));
  return($from . ' ' . $html_wrote . " :\n\n" . $body);

}
/* ----------------------------------------------------- */

// If running with magic_quotes_gpc (get/post/cookie) set
// in php.ini, we will need to strip slashes from every
// field we receive from a get/post operation.
function safestrip(&$string)
{
    if(get_magic_quotes_gpc())
        $string = stripslashes($string);
    return $string;
}


// Wrap outgoing messages to
function wrap_outgoing_msg ($txt, $length, $newline)
{
  $msg = '';
  // cut message in segment
  $tbl = explode ("\r\n", $txt);
  // Clean the end of the line
  for ($i = 0, $buffer = ''; $i < count ($tbl); ++$i)
    {
      $tbl[$i] = rtrim ($tbl[$i]);
      if (strlen ($tbl[$i]) <= $length)
	$msg .= $tbl[$i] . $newline;
      else
	{
          unset( $buffer);
	  $words = explode (" ", $tbl[$i]);
	  for ($j = 0; $j < count ($words); ++$j)
	    {
	      if ((strlen ($buffer) + strlen ($words[$j])) <= $length)
		$buffer .= $words[$j] . " ";
	      else
		{
		  $msg .= rtrim ($buffer) . $newline;
		  $buffer = $words[$j] . " ";
		}
	    }
	  $msg .= rtrim ($buffer) . $newline;
	}
    }
  return $msg;
}

function strip_tags2(&$string, $allow)
{
    $string = preg_replace('/<</', '<nocc_less_than_tag><', $string);
    $string = preg_replace('/>>/', '><nocc_greater_than_tag>;', $string);
    $string = strip_tags($string, $allow . '<nocc_less_than_tag><nocc_greater_than_tag>');
    $string = preg_replace('/<nocc_less_than_tag>/', '<', $string);
    return preg_replace('/<nocc_greater_than_tag>/', '>', $string);
}

/* ----------------------------------------------------- */

// Check e-mail address and return TRUE if it looks valid.
function valid_email($email)
{
    /* Regex of valid characters */
    $regexp = "/^[A-Za-z0-9\._-]+@([A-Za-z0-9][A-Za-z0-9-]{1,62})(\.[A-Za-z0-9][A-Za-z0-9-]{1,62})+$/";
    if(!preg_match($regexp, $email))
        return FALSE;
    return TRUE;
}

function get_per_page() {
    global $conf;
    $user_prefs = $_SESSION['nocc_user_prefs'];
    $msg_per_page = 0;
    if (isset($conf->msg_per_page))
            $msg_per_page = $conf->msg_per_page;
    if (isset($user_prefs->msg_per_page))
            $msg_per_page = $user_prefs->msg_per_page;
    // Failsafe
    if($msg_per_page < 1)
            $msg_per_page = 25;
    return $msg_per_page;
}

// ============================ Contact List ==================================

function load_list ($path)
{
   $fp = @fopen($path, "r");
   if (!$fp)
     return array();
   // Create the contact list
   $contacts = array ();
   // Load the contact list
   while(!feof ($fp))
     {
       $buffer = trim(fgets($fp, 4096));
       if ($buffer != "")
	 array_push ($contacts, $buffer);
     }

   fclose($fp);
   // return the list
   return $contacts;
}


function save_list ($path, $contacts, $conf, &$ev)
{
  include ('lang/' . $_SESSION['nocc_lang'] . '.php');
  if(file_exists($path) && !is_writable($path)){
     $ev = new NoccException($html_err_file_contacts);
     return;
  }
  if (!is_writeable($conf->prefs_dir)) {
      $ev = new NoccException($html_err_file_contacts);
      return;
  }
  $fp = fopen($path, "w");

  for ($i = 0; $i < count ($contacts); ++$i)
  {
      if (trim($contacts[$i]) != "")
          fwrite ($fp, $contacts[$i]."\n");
  }

  fclose($fp);
}

// Convert html entities to normal characters
function unhtmlentities ($string)
{
   $trans_tbl = get_html_translation_table (HTML_ENTITIES);
   $trans_tbl = array_flip ($trans_tbl);
   return strtr ($string, $trans_tbl);
}

// Convert mail data (from, to, ...) to HTML
function convertMailData2Html($maildata, $cutafter = 0) {
  if (($cutafter > 0) && (strlen($maildata) > $cutafter)) {
    return htmlspecialchars(substr($maildata, 0, $cutafter)) . '&hellip;';
  } else {
    return htmlspecialchars($maildata);
  }
}

// Save session informations.
function saveSession(&$ev)
{
  global $conf;
  if (!empty($conf->prefs_dir)) {
    // generate string with session information
    unset ($cookie_string);
    $cookie_string = $_SESSION['nocc_user'];
    $cookie_string .= " " . $_SESSION['nocc_passwd'];
    $cookie_string .= " " . $_SESSION['nocc_lang'];
    $cookie_string .= " " . $_SESSION['nocc_smtp_server'];
    $cookie_string .= " " . $_SESSION['nocc_smtp_port'];
    $cookie_string .= " " . $_SESSION['nocc_theme'];
    $cookie_string .= " " . $_SESSION['nocc_domain'];
    $cookie_string .= " " . $_SESSION['imap_namespace'];
    $cookie_string .= " " . $_SESSION['nocc_servr'];
    $cookie_string .= " " . $_SESSION['nocc_folder'];
    $cookie_string .= " " . $_SESSION['smtp_auth'];

    // encode cookie string to base64
    $cookie_string = base64_encode($cookie_string);

    // save string to file
    $filename = $conf->prefs_dir . '/' . $_SESSION['nocc_user'].'@'.$_SESSION['nocc_domain'] . '.session';
    if (file_exists($filename) && !is_writable($filename)) {
      $ev = new NoccException($html_session_file_error);
      return;
    }
    if (!is_writable($conf->prefs_dir)) {
      $ev = new NoccException($html_session_file_error);
      return;
    }
    $file = fopen($filename, 'w');
    if (!$file) {
      $ev = new NoccException($html_session_file_error);
      return;
    }
    fwrite ($file, $cookie_string . "\n");
    fclose ($file);
  }
}

// Restore session informations.
function loadSession(&$ev, &$key)
{
  global $conf;

  if (empty($conf->prefs_dir)) {
    return '';
  }

  $filename = $conf->prefs_dir . '/' . $key . '.session';
  if (!file_exists($filename)) {
    return '';
  }

  $file = fopen($filename, 'r');
  if (!$file) {
    $ev = new NoccException("Could not open $filename for reading user session");
    return '';
  }

  $line = trim(fgets($file, 1024));
  return $line;
}

// Convert a language string to HTML
function convertLang2Html($langstring) {
  global $charset;
  return htmlentities($langstring, ENT_COMPAT, $charset);
}
?>
