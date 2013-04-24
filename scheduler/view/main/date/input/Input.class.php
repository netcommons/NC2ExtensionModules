<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジューラ予定日入力画面表示アクションクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_View_Main_Date_Input extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;

	// 使用コンポーネントを受け取るため
	var $session = null;
	var $filterChain = null;

	// 値をセットするため
	var $scheduleDates = null;

	/**
	 * ケジューラ予定日入力画面表示アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		$sessionCalendars = $this->session->getParameter(SCHEDULER_CALENDAR_SESSION_KEY . $this->block_id);
		if (empty($sessionCalendars)) {
			$scheduleDate = array();
			$this->scheduleDates[] = $scheduleDate;

			return 'success';
		}

		$smartyAssign =& $this->filterChain->getFilterByName('SmartyAssign');
		$entryDateFormat = $smartyAssign->getLang('_input_date_format');
		foreach ($sessionCalendars as $calendarStartDate => $sessionCalendar) {
			$startUnixTimestamp = mktime(intval(substr($calendarStartDate, 8, 2)),
											intval(substr($calendarStartDate, 10, 2)),
											0,
											intval(substr($calendarStartDate, 4, 2)),
											intval(substr($calendarStartDate, 6, 2)),
											intval(substr($calendarStartDate, 0, 4))
										);

			if (date('Hi', $startUnixTimestamp) == SCHEDULER_CALENDAR_MORNING_START) {
				$endHour = substr(SCHEDULER_CALENDAR_MORNING_END, 0, 2);
				$endMinute = substr(SCHEDULER_CALENDAR_MORNING_END, 2, 2);
			} elseif (date('Hi', $startUnixTimestamp) == SCHEDULER_CALENDAR_AFTERNOON_START) {
				$endHour = substr(SCHEDULER_CALENDAR_AFTERNOON_END, 0, 2);
				$endMinute = substr(SCHEDULER_CALENDAR_AFTERNOON_END, 2, 2);
			} elseif (date('Hi', $startUnixTimestamp) == SCHEDULER_CALENDAR_EVENING_START) {
				$endHour = substr(SCHEDULER_CALENDAR_EVENING_END, 0, 2);
				$endMinute = substr(SCHEDULER_CALENDAR_EVENING_END, 2, 2);
			} elseif ($sessionCalendar['allday_flag'] == _ON) {
				$endHour = '00';
				$endMinute = '00';
			} else {
				return 'error';
			}
			$endUnixTimestamp = mktime(intval($endHour),
											intval($endMinute),
											0,
											intval(substr($calendarStartDate, 4, 2)),
											intval(substr($calendarStartDate, 6, 2)),
											intval(substr($calendarStartDate, 0, 4))
										);

			$scheduleDate['date_id'] = $sessionCalendar['date_id'];
			$scheduleDate['allday_flag'] = $sessionCalendar['allday_flag'];
			$scheduleDate['startEntryDate'] = date($entryDateFormat, $startUnixTimestamp);
			$scheduleDate['startOffsetUnixTimestamp'] = $startUnixTimestamp;
			$scheduleDate['endEntryDate'] = date($entryDateFormat, $endUnixTimestamp);
			$scheduleDate['endOffsetUnixTimestamp'] = $endUnixTimestamp;

			$this->scheduleDates[] = $scheduleDate;
		}

		return 'success';
	}
}
?>