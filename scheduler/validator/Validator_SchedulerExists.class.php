<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジューラ存在チェックバリデータクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Validator_SchedulerExists extends Validator
{
	/**
	 * スケジューラ存在チェックバリデータ
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
		$actionChain =& $container->getComponent('ActionChain');
		$actionName = $actionChain->getCurActionName();
		$schedulerView =& $container->getComponent('schedulerView');
		$request =& $container->getComponent('Request');

		if (empty($attributes['scheduler_id'])) {
			$attributes['scheduler_id'] =& $schedulerView->getCurrentSchedulerId();
			$request->setParameter('scheduler_id', $attributes['scheduler_id']);
		}

		if (empty($attributes['scheduler_id'])) {
			$attributes['scheduler_id'] =& $schedulerView->getFirstSchedulerId();
			$request->setParameter('scheduler_id', $attributes['scheduler_id']);
		}

		if (empty($attributes['scheduler_id'])
			&& strpos($actionName, 'scheduler_view_edit') === 0) {
			return;
		}

		if (empty($attributes['scheduler_id'])
			&& $actionName != 'scheduler_action_edit_initialize') {
			return $errStr;
		}

		if (empty($attributes['block_id'])) {
			$block =& $schedulerView->getBlock();
			if ($attributes['room_id'] != $block['room_id']) {
				return $errStr;
			}

			$attributes['block_id'] = $block['block_id'];
			$request->setParameter('block_id', $attributes['block_id']);
		}

		if ($actionName == 'scheduler_action_edit_initialize') {
			return;
		}

		if (!$schedulerView->schedulerExists()) {
			return $errStr;
		}

		return;
	}
}
?>