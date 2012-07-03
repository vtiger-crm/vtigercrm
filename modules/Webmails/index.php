<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the 
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Emails/index.php,v 1.3 2005/03/17 20:01:10 samk Exp $
 * Description: TODO:  To be written.
 ********************************************************************************/

global $theme, $currentModule;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$parentTab = getParentTab();

echo "<br/><b>". getTranslatedString('LBL_INSTEAD_OF_WEBMAILS_USE', $currentModule).
		" <a href='index.php?module=MailManager&action=index&parenttab=".$parentTab."'>".getTranslatedString('MailManager', 'MailManager')."</a>.<br/>".
		getTranslatedString('LBL_PLEASE_CLICK_HERE_TO_GO_TO', $currentModule) .
		" <a href='index.php?module=Emails&action=index&parenttab=".$parentTab."'>".getTranslatedString('Emails', 'Emails')."</a> ".
		getTranslatedString('LBL_MODULE', $currentModule) ."</b>";

?>
