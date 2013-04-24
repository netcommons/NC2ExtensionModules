<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジューラ登録アクションクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Action_Edit_Entry extends Action
{
	// 使用コンポーネントを受け取るため
	var $schedulerAction = null;
	var $request = null;

	/**
	 * スケジューラ登録アクション
	 *
	 * @access public
	 */
	function execute()
	{
		$authorityIds = $this->request->getParameter('authority');
		$authorityId = min(array_keys($authorityIds));
		$this->request->setParameter('authority', $authorityId);

		$authorityIds = $this->request->getParameter('mail_authority');
		$authorityId = min(array_keys($authorityIds));
		$this->request->setParameter('mail_authority', $authorityId);

		if (!$this->schedulerAction->setScheduler()) {
			return 'error';
		}

		return 'success';
	}
}
?>