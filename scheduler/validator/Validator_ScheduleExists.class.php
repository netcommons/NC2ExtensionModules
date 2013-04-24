<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジュール存在チェックバリデータクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Validator_ScheduleExists extends Validator
{
	/**
	 * スケジュール存在チェックバリデータ
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
		$schedulerView =& $container->getComponent('schedulerView');
		$request =& $container->getComponent('Request');

		if (empty($attributes['scheduler'])) {
			$attributes['scheduler'] =& $schedulerView->getCurrentScheduler();
			$request->setParameter('scheduler', $attributes['scheduler']);
		}
		if (empty($attributes['scheduler'])) {
			return $errStr;
		}

		$scheduler = $attributes['scheduler'];
		$actionChain =& $container->getComponent('ActionChain');
		$actionName = $actionChain->getCurActionName();
		$attributes['schedule_id'] = intval($attributes['schedule_id']);
		if (empty($attributes['schedule_id'])
			&& ($actionName == 'scheduler_action_main_entry'
				|| $actionName == 'scheduler_action_main_mail'
				|| $actionName == 'scheduler_view_main_entry'
				|| $actionName == 'scheduler_view_main_calendar')) {
			return;
		}

		$schedule =& $schedulerView->getSchedule();
		if (empty($schedule)) {
			return $errStr;
		}

		if ($scheduler['scheduler_id'] != $schedule['scheduler_id']) {
			return $errStr;
		}
		$request->setParameter('schedule', $schedule);

		return;
	}
}
?>