<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジュール提出画面表示アクションクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_View_Main_Reply extends Action
{
	// リクエストパラメータを受け取るため
	var $schedule_id = null;

	// 使用コンポーネントを受け取るため
	var $schedulerView = null;

	// validatorから受け取るため
	var $schedule = null;

	// 値をセットするため
	var $scheduler = null;
	var $scheduleDates = null;
	var $ownReplies = null;
	var $replyUsers = null;

	/**
	 * スケジュール提出画面表示アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		$this->scheduler =& $this->schedulerView->getCurrentScheduler();
		if (empty($this->scheduler)) {
			return 'error';
		}

		$this->scheduleDates =& $this->schedulerView->getScheduleDates(true);
		if (empty($this->scheduleDates)) {
			return 'error';
		}
		$this->schedulerView->setHolidays($this->scheduleDates);

		$this->ownReplies =& $this->schedulerView->getOwnReplies();
		if ($this->ownReplies === false) {
			return 'error';
		}

		$this->replyUsers =& $this->schedulerView->getReplyUsers();
		if ($this->replyUsers === false) {
			return 'error';
		}

		return 'success';
	}
}
?>