<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジュール登録チェックバリデータクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Validator_EntrySchedule extends Validator
{
	/**
	 * スケジュール登録チェックバリデータ
	 *
	 * @param mixed $attributes チェックする値
	 * @param string $errStr エラー文字列
	 * @param array $params オプション引数
	 * @return string エラー文字列(エラーの場合)
	 * @access public
	 */
	function validate($attributes, $errStr, $params)
	{
		$scheduler = $attributes['scheduler'];
		if (empty($scheduler['createAuthority'])) {
			return $errStr;
		}

		$schedule = $attributes['schedule'];
		$container =& DIContainerFactory::getContainer();
		$actionChain =& $container->getComponent('ActionChain');
		$actionName = $actionChain->getCurActionName();
		if (empty($schedule)
			&& ($actionName == 'scheduler_action_main_entry'
				|| $actionName == 'scheduler_view_main_entry'
				|| $actionName == 'scheduler_view_main_calendar')) {
			return;
		}

		if (empty($schedule['editAuthority'])) {
			return $errStr;
		}

		return;
	}
}
?>