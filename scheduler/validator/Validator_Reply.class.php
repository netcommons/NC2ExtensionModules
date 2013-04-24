<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジュール提出チェックバリデータクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Validator_Reply extends Validator
{
	/**
	 * スケジュール提出チェックバリデータ
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
		$filterChain =& $container->getComponent('FilterChain');
		$smartyAssign =& $filterChain->getFilterByName('SmartyAssign');
		$replyComment = $attributes['reply_comment'];

		foreach ($attributes['reply'] as $dateId => $reply) {
			if ($reply != SCHEDULER_REPLY_NONE
				&& $reply != SCHEDULER_REPLY_OK
				&& $reply != SCHEDULER_REPLY_NG
				&& $reply != SCHEDULER_REPLY_FINE) {
				return $errStr;
			}

			if ($reply == SCHEDULER_REPLY_FINE
				&& empty($replyComment[$dateId])) {
				$errStr = $smartyAssign->getLang('scheduler_reply_required');
				return $errStr;
			}
		}

		return;
	}
}
?>