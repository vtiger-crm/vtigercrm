<?php
/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

$idstring = rtrim($_REQUEST['idstring'],";");

if($_REQUEST['export_record'] == true)
{
	//This conditions user can select includesearch & (all |currentpage|selecteddata) but not search---STARTS>>>>>>>>.
	if(($_REQUEST['search_type'] == 'includesearch' && $_REQUEST['export_data'] == 'all') && $_SESSION['export_where'] == '')
   	{
		echo'NOT_SEARCH_WITHSEARCH_ALL';
		exit();
	}
	elseif(($_REQUEST['search_type'] == 'includesearch' && $_REQUEST['export_data'] == 'currentpage') && $_SESSION['export_where'] == '')
	{
		echo'NOT_SEARCH_WITHSEARCH_CURRENTPAGE';
		exit();
	}	
	elseif(($_REQUEST['search_type'] == 'includesearch' && $_REQUEST['export_data'] == 'selecteddata') && $idstring == '')
	{
		echo'NO_DATA_SELECTED';
		exit();
	}
	//This conditions user can select includesearch & (all |currentpage|selecteddata) but not search----ENDS<<<<<<<<.
	//This conditions user can select withoutsearch & (all |currentpage|selecteddata) but  search---STARTS>>>>>>>>.
	elseif(($_REQUEST['search_type'] == 'withoutsearch' && $_REQUEST['export_data'] == 'all') && $_SESSION['export_where'] != '')
        {
                echo'SEARCH_WITHOUTSEARCH_ALL';
                exit();
        }
	elseif(($_REQUEST['search_type'] == 'withoutsearch' && $_REQUEST['export_data'] == 'currentpage') && $_SESSION['export_where'] != '')
	{
                echo'SEARCH_WITHOUTSEARCH_CURRENTPAGE';
                exit();
	}
	elseif(($_REQUEST['search_type'] == 'withoutsearch' && $_REQUEST['export_data'] == 'selecteddata') && $idstring == '')
	{
		echo'NO_DATA_SELECTED';
		exit();
	}
	//This conditions user can select withoutsearch & (all |currentpage|selecteddata) but  search---ENDS<<<<<<<<<<<<
}
?>
