<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 表示方法登録アクションクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Action_Edit_Display extends Action
{
	// 使用コンポーネントを受け取るため
	var $schedulerAction = null;

	/**
	 * 表示方法登録示アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		if (!$this->schedulerAction->setBlock()) {
			return 'error';
		}

		return 'success';
	}
}
?>