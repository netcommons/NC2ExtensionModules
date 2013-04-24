<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * メール送信チェックバリデータクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Validator_SendMail extends Validator
{
	/**
	 * メール送信チェックバリデータ
	 *
	 * @param mixed $attributes チェックする値
	 * @param string $errStr エラー文字列
	 * @param array $params オプション引数
	 * @return string エラー文字列(エラーの場合)
	 * @access public
	 */
	function validate($attributes, $errStr, $params)
	{
		$container =& DIContainerFactory::getContainer();
		$session =& $container->getComponent('Session');

		$scheduleId = $session->getParameter('scheduler_mail_schedule_id');
		$scheduleId = intval($scheduleId);

		$schedulerView =& $container->getComponent('schedulerView');
		$scheduler =& $schedulerView->getScheduler();
		if (empty($scheduler)) {
			return $errStr;
		}
		if ($scheduler['mail_send'] != _ON) {
			return $errStr;
		}

		$request =& $container->getComponent('Request');
		$request->setParameter('schedule_id', $scheduleId);
		$request->setParameter('scheduler', $scheduler);

		return;
	}
}
?>