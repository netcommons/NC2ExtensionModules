<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジュール削除アクションクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Action_Main_Delete extends Action
{
	// リクエストパラメータを受け取るため
	var $schedule_id = null;

	// 使用コンポーネントを受け取るため
	var $schedulerAction = null;

	/**
	 * タスク登録アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		if (!$this->schedulerAction->deleteSchedule()) {
			return 'error';
		}
		return 'success';
	}
}
?>