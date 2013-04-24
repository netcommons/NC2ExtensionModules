<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジューラ初期表示アクションクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_View_Main_Init extends Action
{
	// 使用コンポーネントを受け取るため
	var $schedulerView = null;
	var $request = null;

	// 値をセットするため
	var $scheduler = null;

	/**
	 * スケジューラ初期表示アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		$this->scheduler =& $this->schedulerView->getCurrentScheduler();
		if (empty($this->scheduler)) {
			return 'error';
		}
		$this->request->setParameter('scheduler', $this->scheduler);

		if ($this->scheduler['display'] == SCHEDULER_DISPLAY_SELECT
			&& !empty($this->scheduler['schedule_id'])) {
			$this->request->setParameter('schedule_id', $this->scheduler['schedule_id']);

			return 'reply';
		}

		return 'list';
	}
}
?>