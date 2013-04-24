<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジュール詳細画面表示アクションクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_View_Main_Detail extends Action
{
	// 使用コンポーネントを受け取るため
	var $schedulerView = null;

	// validatorから受け取るため
	var $schedule = null;

	// 値をセットするため
	var $scheduleDates = null;

	/**
	 * スケジュール詳細画面表示アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		$this->scheduleDates =& $this->schedulerView->getScheduleDates(true);
		if (empty($this->scheduleDates)) {
			return 'error';
		}
		$this->schedulerView->setHolidays($this->scheduleDates);

		return 'success';
	}
}
?>