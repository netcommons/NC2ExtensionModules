<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジューラ登録カレンダ画面の表示アクションクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_View_Main_Calendar extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	var $calendarDate = null;

	// 使用コンポーネントを受け取るため
	var $schedulerView = null;

	// validatorから受け取るため
	var $schedule = null;

	// 値をセットするため
	var $sessionCalendars = null;
	var $dayOfWeeks = null;
	var $calendarStart = null;
	var $calendarEnd = null;
	var $holidays = null;

	/**
	 * スケジューラ登録カレンダ画面の表示アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		$this->calendarDate = intval($this->calendarDate);
		$this->schedulerView->setCalendarValues($this);

		return 'success';
	}
}
?>