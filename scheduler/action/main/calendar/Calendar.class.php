<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * カレンダ日時選択アクションクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Action_Main_Calendar extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	var $date_id = null;
	var $allday_flag = null;
	var $calendarStartDate = null;
	var $deleteFlag = null;

	// 使用コンポーネントを受け取るため
	var $session = null;

	/**
	 * カレンダ日時選択アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		$sessionCalendars = $this->session->getParameter(SCHEDULER_CALENDAR_SESSION_KEY. $this->block_id);
		if (empty($sessionCalendars)) {
			$sessionCalendars = array();
		}

		if (!is_array($this->date_id)) {
			$this->date_id = array($this->date_id);
			$this->allday_flag = array($this->allday_flag);
			$this->calendarStartDate = array($this->calendarStartDate);
			$this->deleteFlag = array($this->deleteFlag);
		}

		foreach ($this->date_id as $iteration => $dateId) {
			$calendarStartDate = $this->calendarStartDate[$iteration];

			if (!empty($this->deleteFlag[$iteration])
				&& array_key_exists($calendarStartDate, $sessionCalendars)) {
				unset($sessionCalendars[$calendarStartDate]);
				continue;
			}

			if (empty($this->deleteFlag[$iteration])
				&& !array_key_exists($calendarStartDate, $sessionCalendars)) {
				$sessionCalendar = array(
					'date_id' => $dateId,
					'allday_flag' => $this->allday_flag[$iteration]
				);
				$sessionCalendars[$calendarStartDate] = $sessionCalendar;
			}
		}
		$this->session->setParameter(SCHEDULER_CALENDAR_SESSION_KEY . $this->block_id, $sessionCalendars);

		return 'success';
	}
}
?>