<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * カレンダ用日程チェックバリデータクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Validator_CalendarDate extends Validator
{
	/**
	 * カレンダ用日程チェックバリデータ
	 *
	 * @param   mixed   $attributes チェックする値
	 * @param   string  $errStr	 エラー文字列
	 * @param   array   $params	 オプション引数
	 * @return  string  エラー文字列(エラーの場合)
	 * @access  public
	 */
	function validate($attributes, $errStr, $params)
	{
		if ($attributes['entry_type'] == SCHEDULER_ENTRY_INPUT) {
			return;
		}

		if (empty($attributes['block_id'])) {
			return $errStr;
		}

		$container =& DIContainerFactory::getContainer();
		$request =& $container->getComponent('Request');
		$session =& $container->getComponent('Session');

		$sessionCalendars = $session->getParameter(SCHEDULER_CALENDAR_SESSION_KEY . $attributes['block_id']);
		if (empty($sessionCalendars)) {
			$filterChain =& $container->getComponent('FilterChain');
			$smartyAssign =& $filterChain->getFilterByName('SmartyAssign');
			$errStr = $smartyAssign->getLang('scheduler_calendar_date_required');
			return $errStr;
		}

		$container =& DIContainerFactory::getContainer();
		$filterChain =& $container->getComponent('FilterChain');
		$smartyAssign =& $filterChain->getFilterByName('SmartyAssign');
		$entryDateFormat = $smartyAssign->getLang('_input_date_format');
		foreach ($sessionCalendars as $calendarStartDate => $sessionCalendar) {
			
			$dsateIds[] = $sessionCalendar['date_id'];
			$alldayFlags[] = $sessionCalendar['allday_flag'];

			$startUnixTimestamp = mktime(intval(substr($calendarStartDate, 8, 2)),
											intval(substr($calendarStartDate, 10, 2)),
											0,
											intval(substr($calendarStartDate, 4, 2)),
											intval(substr($calendarStartDate, 6, 2)),
											intval(substr($calendarStartDate, 0, 4))
										);

			$startDates[] = date($entryDateFormat, $startUnixTimestamp);
			$startHours[] = date('H', $startUnixTimestamp);
			$startMinutes[] = date('i', $startUnixTimestamp);

			$endDates[] = date($entryDateFormat, $startUnixTimestamp);
			if (date('Hi', $startUnixTimestamp) == SCHEDULER_CALENDAR_MORNING_START) {
				$endHours[] = substr(SCHEDULER_CALENDAR_MORNING_END, 0, 2);
				$endMinutes[] = substr(SCHEDULER_CALENDAR_MORNING_END, 2, 2);
			} elseif (date('Hi', $startUnixTimestamp) == SCHEDULER_CALENDAR_AFTERNOON_START) {
				$endHours[] = substr(SCHEDULER_CALENDAR_AFTERNOON_END, 0, 2);
				$endMinutes[] = substr(SCHEDULER_CALENDAR_AFTERNOON_END, 2, 2);
			} elseif (date('Hi', $startUnixTimestamp) == SCHEDULER_CALENDAR_EVENING_START) {
				$endHours[] = substr(SCHEDULER_CALENDAR_EVENING_END, 0, 2);
				$endMinutes[] = substr(SCHEDULER_CALENDAR_EVENING_END, 2, 2);
			} elseif ($sessionCalendar['allday_flag'] == _ON) {
				$endHours[] = '00';
				$endMinutes[] = '00';
			} else {
				return $errStr;
			}

			$timezoneOffsets[] = $session->getParameter('_timezone_offset');
		}

		$request->setParameter('date_id', $dsateIds);
		$request->setParameter('allday_flag', $alldayFlags);
		$request->setParameter('start_date', $startDates);
		$request->setParameter('start_hour', $startHours);
		$request->setParameter('start_minute', $startMinutes);
		$request->setParameter('end_date', $endDates);
		$request->setParameter('end_hour', $endHours);
		$request->setParameter('end_minute', $endMinutes);
		$request->setParameter('timezone_offset', $timezoneOffsets);

		return;
	}
}
?>