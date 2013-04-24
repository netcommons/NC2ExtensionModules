<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジューラ初期処理チェックバリデータクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Validator_Initialize extends Validator
{
	/**
	 * スケジューラ初期処理チェックバリデータ
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

		$schedulerCount =& $schedulerView->getSchedulerCount();
		if (empty($schedulerCount)
			&& !empty($attributes['scheduler_id'])) {
			return $errStr;
		}

		return;
	}
}
?>