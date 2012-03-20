<?php
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
/**
 ** index.php
 **
 ** This file simply takes any attempt to view source vtiger_files
 ** and sends those people to the login screen. At this
 ** point no attempt is made to see if the person is logged
 ** or not.
 **/

header("Location:../index.php");

/** pretty impressive huh? **/
?>
