<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('class_http/class_http.php');
/** Function to get data from the external site
  * @param $url -- url:: Type string
  * @param $variable -- variable:: Type string
  * @returns $desc -- desc:: Type string array
  *
 */
function getComdata($url,$variable="")
{

	$h = new http();
	$desc = array();
	$h->dir = "class_http_dir/";
	if (!$h->fetch($url, 2)) {
	  echo "<h2>There is a problem with the http request!</h2>";
	  echo $h->log;
	  exit();
	}
        if($variable != "")
        {	
		$msft_stats = http::table_into_array($h->body, 'Find Symbol', 0, null);
	        if($msft_stats != '')
        	{
			$desc=$msft_stats[0];
			$data=getQuoteData($variable);
			if(is_array($data))
			{
				foreach($data as $key=>$value)
					array_push($desc,$value);
				return $desc;
			}
			else
			{
				die;
			}
		}
		else
		        return "Information on ".$variable." is not available or '".$variable."' is not a valid ticker symbol.";
	}
	else
	{
		$headlines = array();
		$news = http::table_into_array($h->body, 'HEADLINES',0, null);
		if(is_array($news))
		{
			$headlines[] = $news[35];
			$headlines[] = $news[37];
			$headlines[] = $news[39];
			$headlines[] = $news[41];
			return $headlines;
		}
		else
			return "No headlines available";
	}
}

/** Function to get company quotes from external site
  * @param $var -- var:: Type string(company trickersymbol)
  * @returns $quote_data -- quote_data:: Type string array
  *
 */
function getQuoteData($var)
{
	$url = "http://finance.yahoo.com/q?s=".$var;
	$h = new http();
        $h->dir = "class_http_dir/";
        if (!$h->fetch($url, 2)) {
          echo "<h2>There is a problem with the http request!</h2>";
          echo $h->log;
          exit();
        }
	$res_arr=array();
	$quote_data = http::table_into_array($h->body, 'Delayed quote data', 0, null);
        if(is_array($quote_data))
        {
                array_shift($quote_data);
                array_shift($quote_data);
                if($quote_data[0][0]!= 'Last Trade:')
                        array_shift($quote_data);
        }
	else
	{
		die;
	}
        for($i=0;$i<16;$i++)
        {
                if($quote_data !='')
 	                $res_arr[]=$quote_data[$i];
        }
	return $res_arr;
}
?>
