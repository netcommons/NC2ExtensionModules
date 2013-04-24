<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジュール選択アクションクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Action_Main_Select extends Action
{
	// 使用コンポーネントを受け取るため
	var $schedulerAction = null;

	/**
	 * スケジュール選択アクション
	 *
	 * @access public
	 */
	function execute()
	{
		if (!$this->schedulerAction->selectSchedule()) {
			return 'error';
		}

		return 'success';
	}
}
?>