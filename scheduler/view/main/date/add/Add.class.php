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
class Scheduler_View_Main_Date_Add extends Action
{
	// リクエストパラメータを受け取るため
	var $dateIteration = null;

	// 値をセットするため
	var $scheduleDates = null;

	/**
	 * ケジューラ予定日入力画面表示アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		$scheduleDate = array();
		$this->scheduleDates[] = $scheduleDate;

		return 'success';
	}
}
?>