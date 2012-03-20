<?php
/*
 * $Header: /cvsroot/nocc/nocc/webmail/conf.php.dist,v 1.150 2006/11/29 19:58:51 goddess_skuld Exp $
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 * Copyright 2002 Mike Rylander <mrylander@mail.com>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 */

// ################### This is the main configuration for NOCC ########## //

// ==> Required parameters

// Will be checked by html/*.php file. If it's not available, these files won't
// be loaded.
$conf->loaded = true;

// Default smtp server and smtp_port (default is 25)
// If a domain has no smtp server, this one will be used
// If no smtp server is provided, Nocc will default to the mail() function,
// and try to use Sendmail or any other MTA (Postfix)
$conf->default_smtp_server = 'smtp';
$conf->default_smtp_port = 25;

// List of domains people can log in
// You can have as many domains as you need

// $conf->domains[$i]->domain = 'sourceforge.net';
//  domain name e.g 'sourceforge.net'. This field is used when sending message
//
// $conf->domains[$i]->in = 'mail.sourceforge.net:110/pop3';
//  imap or pop3 server name + port + protocol (only if not imap)
//  [server_name]:[port number]/[protocol]/[options]
//  ex for an imap server : mail.sourceforge.net:143
//  ex for an imap server with explicit TLS/SSL negociation desactivated : mail.sourceforge.net:143/notls (may be useful for some courier-imap installation).
	//(may be useful for some courier-imap installation).
//  ex for an ssl imap server : mail.sourceforge.net:993/ssl
//  ex for an ssl imap server with a self-signed certificate : mail.sourceforge.net:993/ssl/novalidate-cert
//  ex for a pop3 server  : mail.sourceforge.net:110/pop3
//  ex for a pop3 server with explicit TLS/SSL negociation desactivated : mail.sourceforge.net:110/pop3/notls (may be useful for some courier-imap installation).
//  ex for an ssl pop3 server : mail.sourceforge.net:995/pop3/ssl
//  ex for an ssl pop3 server with a self-signed certificate : mail.sourceforge.net:995/pop3/ssl/novalidate-cert
//  protocol can only be pop3
//
// $conf->domains[$i]->smtp = 'smtp.isp.com';
//  Optional: smtp server name or IP address
//  Leave empty to send mail via sendmail
//
// $conf->domains[$i]->smtp_port = 25;
//  Port number to connect to smtp server (usually 25)

$i = 0;

$conf->domains[$i]->domain = '';
$conf->domains[$i]->in ='' ;
//$conf->domains[$i]->in = '';
$conf->domains[$i]->smtp = 'smtp';
$conf->domains[$i]->smtp_port = 25;
// Uncomment for 'user<char>domain.com' style logins
//$conf->domains[$i]->login_with_domain = 1;
// Uncomment and select character to use for login_with_domain option
//$conf->domains[$i]->login_with_domain_character = '@';
// Fill in if you require login suffixes for your mail server
$conf->domains[$i]->login_suffix = '';
// Uncomment for login aliases and use the following syntax:
// login_aliases = array('alias1' => 'real_login_1','alias2' => 'real_login_2');
// If you want to use an external file, use the following syntax:
// login_aliases = '@/path/to/file/';
// See login_alias.sample file for example.
//$conf->domains[$i]->login_aliases = array();
// Uncomment for allowed logins and use the following syntax:
// login_allowed = array('login_1' => '', 'login_2' => '');
// If you want to use an external file, use the following syntax:
// login_allowed = '@/path/to/file/';
// See login_allowed.sample file for example.
//$conf->domains[$i]->login_allowed = array();
// Select SMTP AUTH method.
// Supported AUTH methods are :
// '' : no authentification method
// 'PLAIN' : AUTH PLAIN method
// 'LOGIN' : AUTH LOGIN method
$conf->domains[$i]->smtp_auth_method = '';
// Select IMAP Namespace
$conf->domains[$i]->imap_namespace = "INBOX.";

// If you want to add more domains, uncomment the following
// lines and fill them in

//$i++;
//$conf->domains[$i]->domain = '';
//$conf->domains[$i]->in = '';
//$conf->domains[$i]->smtp = '';
//$conf->domains[$i]->smtp_port = 25;
//$conf->domains[$i]->login_with_domain = 1;
//$conf->domains[$i]->login_suffix = '';
//$conf->domains[$i]->login_aliases = array();
//$conf->domains[$i]->login_allowed = array();
//$conf->domains[$i]->smtp_auth_method = '';
//$conf->domains[$i]->imap_namespace = "INBOX.";

//$i++;
//$conf->domains[$i]->domain = '';
//$conf->domains[$i]->in = '';
//$conf->domains[$i]->smtp = '';
//$conf->domains[$i]->smtp_port = 25;
//$conf->domains[$i]->login_with_domain = 1;
//$conf->domains[$i]->login_suffix = '';
//$conf->domains[$i]->login_aliases = array();
//$conf->domains[$i]->login_allowed = array();
//$conf->domains[$i]->smtp_auth_method = '';
//$conf->domains[$i]->imap_namespace = "INBOX.";

// If you use many mail domains, the one used will be we one of the HTTP host,
// and the user won't be asked for the domain to connect.
// Set to true to enable.
$conf->vhost_domain_login = false;

// Is the user allowed to change his "From:" address? (true/false)
$conf->allow_address_change = true;

// Default tmp directory (where to store temporary uploaded files)
// This should be something like '/tmp' on Unix System
// And 'c:\\temp' on Win32 (note that we must escape "\")
$conf->tmpdir = '/tmp';

// Preferences and contacts data directory
// IMPORTANT: This directory must exist and be writable by the user
// the webserver is running as (e.g. 'apache', or 'nobody'). For
// Apache, see the User directive in the httpd.conf file.
// See README for more about this.
// This should be something like 'profiles/' on Unix System
// or 'prefs\\' on Win32 (note that we must escape "\").
// You should not use a subfolder within your Nocc installation, as it will
// be readable by everybody, and will contain sensible information as email
// addresses and names.
// If left empty, preferences, contacts and session saving will be disabled.
$conf->prefs_dir = '.';

// Master key for session password encryption. Longer is better.
// It must not be left empty.
$conf->master_key = 'abc';

// Default folder to go first
$conf->default_folder = 'INBOX';

// ===> End of required parameters

// The following parameters can be changed but it's not necessary to
// get a working version of nocc

// if browser has no preferred language, we use the default language
// This is only needed for browsers that don't send any preferred
// language such as W3 Amaya
$conf->default_lang = 'en';

// force default language to be set, rather than browser prefered language
$conf->force_default_lang = false;

// How many messages to display in the inbox (devel only)
$conf->max_msg_num = 1;

// let user see the header of a message
$conf->use_verbose = true;

// the user can logout or not (if nocc is used within your website
// enter 'false' here else leave 'true')
$conf->enable_logout = true;

// the user can change their 'reply leadin' string
$conf->enable_reply_leadin = false;

// Whether or not to display attachment part number
$conf->display_part_no = true;

// Whether or not to display the Message/RFC822 into the attachments
// (the attachments of that part are still available even if false is set
$conf->display_rfc822 = true;

// If you don't want to display images (GIF, JPEG and PNG) sent as attachements
// set it to 'false'
$conf->display_img_attach = true;

// If you don't want to display text/plain attachments set it to 'false'
$conf->display_text_attach = true;

// By default the messages are sorted by date 
$conf->default_sort = '1';

// By default the most recent is in top ('1' --> sorting top to bottom,
// '0' --> bottom to top)
$conf->default_sortdir = '1';

// For old UCB POP server, change this setting to 1 to enable
// new mail detection. Recommended: leave it to 0 for any other POP or
// IMAP server.
// See FAQ for more details.
$conf->have_ucb_pop_server = false;

// If you wanna make your own theme and force people to use that one, 
// set $conf->use_theme to false and fill in the $conf->default_theme to 
// the theme name you want to use
// Theme handling: allows users to choose a theme on the login page
$conf->use_theme = true;

// Default theme
$conf->default_theme = 'standard';

// Error reporting
// Display all errors (including IMAP connection errors, such as
// 'host not found' or 'invalid login')
//$conf->debug_level = E_ALL & ~E_NOTICE; // Leave the debug level to PHP configuration

// Base URL where NOCC is hosted (only needed for Xitami servers, see #463390)
// (NOTE: should end in a slash). Leave blank to detect it automagically.
//$conf->base_url = 'http://www.yoursite.com/webmail/';
$conf->base_url = '';

// Another tip for Xitami users, whose $_SERVER['PHP_SELF'] is broken
// (see http://sourceforge.net/tracker/index.php?func=detail&aid=505194&group_id=12177&atid=112177)
//$_SERVER['PHP_SELF'] = 'action.php';

// Use old-style forwarding (quote original message, and attach original attachments).
// This is discouraged, because it mangles the original message, removing important headers etc.
$conf->broken_forwarding = false;

// This sets the number of messages per page to display from a imap folder or pop mailbox
$conf->msg_per_page = '25';

// Set this to '1' to enable the status line for folders at the bottom of the inbox page.
// If you get slow page loads, set it to '0' to disable this (rather slow) function.
$conf->status_line = '1';

//Uncomment this to allow secure typed domain logins
//$conf->typed_domain_login = '1';

// ################### Messages Signature  ################### //

// This message is added to every message, the user cannot delete it
// Be careful if you modify this, do not forget to write '\r\n' to switch
// to the next line !
$conf->ad = "___________________________________\r\nNOCC, http://nocc.sourceforge.net";

// PHP error reporting for this application
error_reporting($conf->debug_level);

// Prevent mangling of uploaded attachments
set_magic_quotes_runtime(0);

// Delay between 2 mail send (in second)
$conf->send_delay = 30;

// Number of contacts per user, 0 to disable contacts list
$conf->contact_number_max = 10;

// Allow more memory than default setting in order to handle correctly
// large mails attachments. Try to find correct setting (about 2.5x total
// attachment size)
$conf->memory_limit="20M";

// Allow only specified characters for login. The format of this configuration
// variable is any valid regular expression.
// Example: '^[a-zA-Z0-9_]+$' : login only with letters (upper and lower case),
// numbers and '_' character
// Set to '' to disable
$conf->allowed_char='';

// Select the CRLF to use.
// According to rfc-822 CRLF is "\r\n"
// OS independent, this is a MTA problem
// not ours.
$conf->crlf = "\r\n";

// Enable quota checks.
// Works only with c-client2000 or more recent, and IMAP inbox
$conf->quota_enable=false;

// Quota types.
// Possible values are STORAGE or MESSAGE
$conf->quota_type="STORAGE";

// Default encoding charset to use to display email which does not include one.
$conf->default_charset = 'UTF-8';                                                                                
/*
###################     End of Configuration     ####################
*/

?>
