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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Home/LastViewed.php,v 1.1 2004/08/17 15:05:06 gjayakrishnan Exp $
 * Description: TODO:  To be written.
 ********************************************************************************/

require_once("data/Tracker.php");

global $theme;
global $current_user;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

// Create the head of the vtiger_table.
?>
		<table cellpadding="2" cellspacing="0" border="0">
		<tbody>
<?php
$current_row=1;
$tracker = new Tracker();
$history = $tracker->get_recently_viewed($current_user->id);

foreach($history as $row)
{
    echo <<< EOQ
        <tr>
          <td vAlign="top"><IMG width="20" alt="{$row['module_name']}" src="$image_path{$row['module_name']}.gif" border="0"></td>
          <td noWrap><A title="[Alt+$current_row]" accessKey="$current_row" href="index.php?module=$row[module_name]&action=DetailView&record=$row[item_id]">$row[item_summary]</A></td>
        </tr>
EOQ;
}
?>
</tbody></table>
