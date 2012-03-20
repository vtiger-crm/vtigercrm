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
 * Class to handle repeating events
 */
class Calendar_RepeatEvents {

	/**
	 * Get timing using YYYY-MM-DD HH:MM:SS input string.
	 */
	static function mktime($fulldateString) {
		$splitpart = self::splittime($fulldateString);
		$datepart = split('-', $splitpart[0]);
		$timepart = split(':', $splitpart[1]);
		return mktime($timepart[0], $timepart[1], 0, $datepart[1], $datepart[2], $datepart[0]);
	}
	/**
	 * Increment the time by interval and return value in YYYY-MM-DD HH:MM format.
	 */
	static function nexttime($basetiming, $interval) {
		return date('Y-m-d H:i', strtotime($interval, $basetiming));
	}
	/**
	 * Based on user time format convert the YYYY-MM-DD HH:MM value.
	 */
	static function formattime($timeInYMDHIS) {
		global $current_user;
		$format_string = 'Y-m-d H:i';
		switch($current_user->date_format) {
			case 'dd-mm-yyyy': $format_string = 'd-m-Y H:i'; break;
			case 'mm-dd-yyyy': $format_string = 'm-d-Y H:i'; break;
			case 'yyyy-mm-dd': $format_string = 'Y-m-d H:i'; break;
		}
		return date($format_string, self::mktime($timeInYMDHIS));
	}
	/**
	 * Split full timing into date and time part.
	 */
	static function splittime($fulltiming) {
		return split(' ', $fulltiming);
	}
	/**
	 * Calculate the time interval to create repeated event entries.
	 */
	static function getRepeatInterval($type, $frequency, $recurringInfo, $start_date, $limit_date) {
		$repeatInterval = Array();
		$starting = self::mktime($start_date);
		$limiting = self::mktime($limit_date);

		if($type == 'Daily') {	
			$count = 0;
			while(true) {
				++$count;
				$interval = ($count * $frequency);
				if(self::mktime(self::nexttime($starting, "+$interval days")) > $limiting) {
					break;
				}
				$repeatInterval[] = $interval;
			}
		} else if($type == 'Weekly') {
			if($recurringInfo->dayofweek_to_rpt == null) {
				$count = 0;
				$weekcount = 7;
				while(true) {
					++$count;
					$interval = $count * $weekcount;
					if(self::mktime(self::nexttime($starting, "+$interval days")) > $limiting) {
						break;
					}
					$repeatInterval[] = $interval;
				}
			} else {
				$count = 0;
				while(true) {
					++$count;
					$interval = $count;
					$new_timing = self::mktime(self::nexttime($starting, "+$interval days"));
					$new_timing_dayofweek = date('N', $new_timing);
					if($new_timing > $limiting) {
						break;
					}
					if(in_array($new_timing_dayofweek-1, $recurringInfo->dayofweek_to_rpt)) {
						$repeatInterval[] = $interval;
					}
				}
			}
		} else if($type == 'Monthly') {
			$count = 0;
			$avg_monthcount = 30; // TODO: We need to handle month increments precisely!
			while(true) {
				++$count;
				$interval = $count * $avg_monthcount;
				if(self::mktime(self::nexttime($starting, "+$interval days")) > $limiting) {
					break;
				}
				$repeatInterval[] = $interval;
			}
		} else if($type == 'Yearly') {
			$count = 0;
			$avg_monthcount = 30;
				while(true) {
					++$count;
					$interval = $count * $avg_monthcount;
					if(self::mktime(self::nexttime($starting, "+$interval days")) > $limiting) {
						break;
				}
				$repeatInterval[] = $interval;
			}
		}
		return $repeatInterval;
	}

	/**
	 * Repeat Activity instance till given limit.
	 */
	static function repeat($focus) {

		global $log;
		$repeat = getrecurringObjValue();
		$frequency = $repeat->recur_freq;
		$repeattype= $repeat->recur_type;
	
		$base_focus = new Activity();
		$base_focus->retrieve_entity_info($focus->id,'Events');
		$base_focus->id = $_REQUEST['record'];

		$base_focus_start = $base_focus->column_fields['date_start'].' '.$base_focus->column_fields['time_start'];
		$base_focus_end = $base_focus->column_fields['due_date'].' '.$base_focus->column_fields['time_end'];

		$repeat_limit = getDBInsertDateValue($_REQUEST['calendar_repeat_limit_date']).' '.$base_focus->column_fields['time_start'];

		$repeatIntervals = self::getRepeatInterval($repeattype, $frequency, $repeat, $base_focus_start, $repeat_limit);

		$base_focus_start = self::mktime($base_focus_start);
		$base_focus_end   = self::mktime($base_focus_end);

		$skip_focus_fields = Array ('record_id', 'createdtime', 'modifiedtime', 'recurringtype');

		/** Create instance before and reuse */
		$new_focus = new Activity();

		$numberOfRepeats = count($repeatIntervals);
		foreach($repeatIntervals as $index => $interval) {
			$new_focus_start_timing = self::nexttime($base_focus_start, "+$interval days");
			$new_focus_start_timing = self::splittime(self::formattime($new_focus_start_timing));

			$new_focus_end_timing = self::nexttime($base_focus_end, "+$interval days");
			$new_focus_end_timing = self::splittime(self::formattime($new_focus_end_timing));

			// Reset the new_focus and prepare for reuse
			if(isset($new_focus->id)) unset($new_focus->id);
			$new_focus->column_fields = array();

			foreach($base_focus->column_fields as $key=>$value) {
				if(in_array($key, $skip_focus_fields)) {
					// skip copying few fields
				} else if($key == 'date_start') {
					$new_focus->column_fields['date_start'] = $new_focus_start_timing[0];				
				} else if($key == 'time_start') {
					$new_focus->column_fields['time_start'] = $new_focus_start_timing[1];				
				} else if($key == 'time_end') {
					$new_focus->column_fields['time_end']   = $new_focus_end_timing[1];				
				} else if($key == 'due_date') {
					$new_focus->column_fields['due_date']   = $new_focus_end_timing[0];				
				} else {
					$new_focus->column_fields[$key]         = $value;
				}
			}
			if($numberOfRepeats > 10 && $index > 10) {
				unset($new_focus->column_fields['sendnotification']);
			}
			$new_focus->save('Calendar');
		}
	}
}

?>
