<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジューラ初期登録アクションクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Action_Edit_Initialize extends Action
{
	// リクエストパラメータを受け取るため
	var $scheduler_id = null;

	// 使用コンポーネントを受け取るため
	var $schedulerView = null;
	var $schedulerAction = null;
	var $request = null;

	/**
	 * スケジューラ初期登録アクション
	 *
	 * @access public
	 */
	function execute()
	{
		$scheduler =& $this->schedulerView->getDefaultScheduler();
		$this->request->setParameter('display', $scheduler['display']);
		$this->request->setParameter('visible_row', $scheduler['visible_row']);
		$this->request->setParameter('new_period', $scheduler['new_period']);

		$schedulerCount =& $this->schedulerView->getSchedulerCount();
		if (empty($schedulerCount)) {
			$this->request->setParameter('authority', $scheduler['authority']);
			$this->request->setParameter('mail_send', $scheduler['mail_send']);
			$this->request->setParameter('mail_authority', $scheduler['mail_authority']);
			$this->request->setParameter('mail_subject', $scheduler['mail_subject']);
			$this->request->setParameter('mail_body', $scheduler['mail_body']);
			if (!$this->schedulerAction->setScheduler()) {
				return 'error';
			}

			return 'success';
		}

		if (!$this->schedulerAction->setBlock()) {
			return 'error';
		}

		return 'success';
	}
}
?>