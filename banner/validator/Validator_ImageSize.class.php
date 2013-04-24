<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * サイズの値チェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_Validator_ImageSize extends Validator
{
	/**
	 * サイズの値チェック
	 *
	 * @param mixed $attributes チェックする値
	 * @param string $errStr エラー文字列
	 * @param array $params オプション引数
	 * @return string エラー文字列(エラーの場合)
	 * @access public
	 */
	function validate($attributes, $errStr, $params)
	{
		if ($attributes['size_flag'] != _ON) {
			return;
		}

		$min = $params[0];
		$max = $params[1];

		$container =& DIContainerFactory::getContainer();
		$filterChain =& $container->getComponent('FilterChain');
		$smartyAssign =& $filterChain->getFilterByName('SmartyAssign');
		$errors = array();
		if ($attributes['display_width'] < $min
				|| $attributes['display_width'] > $max) {
			$errors[] = sprintf($smartyAssign->getLang('_number_error'), $smartyAssign->getLang('banner_display_width'), $min, $max);
		}

		if ($attributes['display_height'] < $min
				|| $attributes['display_height'] > $max) {
			$errors[] = sprintf($smartyAssign->getLang('_number_error'), $smartyAssign->getLang('banner_display_height'), $min, $max);
		}

		if (empty($errors)) {
			return;
		}

		$errorStr = implode('<br />', $errors);
		return $errorStr;
	}
}
?>