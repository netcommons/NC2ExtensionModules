<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画配信初期処理チェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Validator_Initialize extends Validator
{
	/**
	 * 動画配信初期処理チェックバリデータ
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
		$multimediaView =& $container->getComponent('multimediaView');
		$request =& $container->getComponent('Request');

		$multimediaCount =& $multimediaView->getMultimediaCount();
		if (empty($multimediaCount)
			&& !empty($attributes['multimedia_id'])) {
			return $errStr;
		}
		
		$request->setParameter("multimediaCount", $multimediaCount);

		return;
	}
}
?>