<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジューラ登録画面の表示
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_View_Main_Entry extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	var $calendarDate = null;
	var $entry_type = null;

	// 使用コンポーネントを受け取るため
	var $schedulerView = null;
	var $session = null;

	// validatorから受け取るため
	var $schedule = null;

	// 値をセットするため
	var $scheduler = null;
	var $scheduleDates = null;
	var $sessionCalendars = null;
	var $calendarStart = null;
	var $calendarEnd = null;
	var $holidays = null;
	var $dayOfWeeks = null;

	/**
	 * execute実行
	 *
	 * @access  public
	 */
	function execute()
	{
		$this->scheduler =& $this->schedulerView->getCurrentScheduler();
		if (empty($this->scheduler)) {
			return 'error';
		}

		if (empty($this->schedule['entry_type'])) {
			$this->schedule['entry_type'] = SCHEDULER_ENTRY_CALENDAR;
		}

		$this->session->removeParameter(SCHEDULER_CALENDAR_SESSION_KEY . $this->block_id);
		$this->scheduleDates =& $this->schedulerView->getScheduleDates();
		if ($this->schedule['entry_type'] == SCHEDULER_ENTRY_INPUT) {
			if (empty($this->scheduleDates)) {
				$this->scheduleDates = array();
			}

			return 'success';
		}

		if (empty($this->scheduleDates)) {
			$this->calendarDate = time();
		}else {
			$this->calendarDate = $this->scheduleDates[0]['startOffsetUnixTimestamp'];

			$dateIds = array();
			$startDates = array();
			foreach ($this->scheduleDates as $scheduleDate) {
				$sessionCalendar['date_id'] = $scheduleDate['date_id'];
				$sessionCalendar['allday_flag'] = $scheduleDate['allday_flag'];
				if ($sessionCalendar['allday_flag'] == _ON) {
					$calendarStartDate = date('Ymd', $scheduleDate['startOffsetUnixTimestamp']);
				} else {
					$calendarStartDate = date('YmdHi', $scheduleDate['startOffsetUnixTimestamp']);
				}

				$sessionCalendars[$calendarStartDate] = $sessionCalendar;
				unset($sessionCalendar);
			}
			$this->session->setParameter(SCHEDULER_CALENDAR_SESSION_KEY . $this->block_id, $sessionCalendars);
		}
		$this->schedulerView->setCalendarValues($this);

		return 'success';
	}
}
?>