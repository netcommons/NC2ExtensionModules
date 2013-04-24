<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * コメント画面表示アクションクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_View_Main_Comment extends Action
{
	// リクエストパラメータを受け取るため
	var $date_id = null;
	var $reply_user_id = null;
	var $reply_comment = null;

	// 使用コンポーネントを受け取るため
	var $session = null;
	var $schedulerView = null;

	/**
	 * コメント画面表示アクション
	 *
	 * @return string アクション文字列
	 * @access  public
	 */
	function execute()
	{
		$userId = $this->session->getParameter('_user_id');
		if ($this->reply_user_id != $userId) {
			$this->reply_comment = $this->schedulerView->getOthersComment();
			return 'other';
		}

		return 'own';
	}
}
?>