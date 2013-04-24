<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * メール送信アクションクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Action_Main_Mail extends Action
{
	// リクエストパラメータを受け取るため
	var $room_id = null;
	var $block_id = null;

	// validatorから受け取るため
	var $schedule_id = null;
	var $scheduler = null;
	var $schedule = null;

	// 使用コンポーネントを受け取るため
	var $session = null;
	var $mailMain = null;
	var $usersView = null;

	/**
	 * メール送信アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		if (empty($this->schedule_id)) {
			return 'success';
		}

		$this->mailMain->setSubject($this->scheduler['mail_subject']);
		$this->mailMain->setBody($this->scheduler['mail_body']);

		$tags['X-SUMMARY'] = htmlspecialchars($this->schedule['summary']);
		$tags['X-DESCRIPTION'] = $this->schedule['description'];
		$tags['X-USER'] = htmlspecialchars($this->schedule['insert_user_name']);
		$tags['X-TO_DATE'] = $this->schedule['insert_time'];
		$tags['X-URL'] = BASE_URL. INDEX_FILE_NAME
						. '?action='. DEFAULT_ACTION
						. '&active_action=scheduler_view_main_reply'
						. '&schedule_id=' . $this->schedule_id
						. '&block_id='. $this->block_id
						. '#' . $this->session->getParameter('_id');
		$this->mailMain->assign($tags);

		$users = $this->usersView->getSendMailUsers($this->room_id, $this->scheduler['mail_authority']);
		$this->mailMain->setToUsers($users);
		$this->mailMain->send();
		$this->session->removeParameter('scheduler_mail_schedule_id');

		return 'success';
	}
}
?>
