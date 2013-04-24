<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジュール登録アクションクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Action_Main_Entry extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	
	// 使用コンポーネントを受け取るため
	var $schedulerAction = null;
	var $session = null;

	/**
	 * スケジュール登録アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		if (!$this->schedulerAction->setSchedule()) {
			return 'error';
		}
		$this->session->removeParameter(SCHEDULER_CALENDAR_SESSION_KEY . $this->block_id);

		return 'success';
	}
}
?>