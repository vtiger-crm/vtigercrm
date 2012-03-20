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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Users/Error.php,v 1.1 2004/08/17 15:06:40 gjayakrishnan Exp $
 * Description: TODO:  To be written.
 ********************************************************************************/
global $app_strings;
?>
<br><br>
<font class='error'><?php if (isset($_REQUEST['error_string'])) echo vtlib_purify($_REQUEST['error_string']); ?>
<br><br>
<?php echo $app_strings['NTC_CLICK_BACK']; ?>
</font>

