<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジューラ設定画面表示アクションクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_View_Edit_Entry extends Action
{
	// 使用コンポーネントを受け取るため
	var $schedulerView = null;

	// 値をセットするため
	var $scheduler = null;

	/**
	 * スケジューラ設定画面表示アクション
	 *
	 * @access public
	 */
	function execute()
	{
		$this->scheduler =& $this->schedulerView->getScheduler();
		if (empty($this->scheduler)) {
			$this->scheduler =& $this->schedulerView->getDefaultScheduler();
		}

		return 'success';
	}
}
?>