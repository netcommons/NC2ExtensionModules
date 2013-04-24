<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * カテゴリ存在チェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_Validator_CategoryExists extends Validator
{
	/**
	 * カテゴリ存在チェック
	 *
	 * @param mixed $attributes チェックする値
	 * @param string $errStr エラー文字列
	 * @param array $params オプション引数
	 * @return string エラー文字列(エラーの場合)
	 * @access public
	 */
	function validate($attributes, $errStr, $params)
	{
		if (empty($attributes['category_id'])) {
			return;
		}

		$container =& DIContainerFactory::getContainer();
		$bannerView =& $container->getComponent('bannerView');
		if (!$bannerView->categoryExists()) {
			return $errStr;
		}

		return;
	}
}
?>