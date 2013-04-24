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
class Scheduler_View_Edit_Initialize extends Action
{
	// 使用コンポーネントを受け取るため
	var $schedulerView = null;

	/**
	 * スケジューラ初期表示アクション
	 *
	 * @access public
	 */
	function execute()
	{
		$schedulerCount =& $this->schedulerView->getSchedulerCount();
		if (empty($schedulerCount)) {
			return 'entry';
		}

		return 'display';
	}
}
?>