<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * バナー種別の値チェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_Validator_BannerType extends Validator
{
	/**
	 * バナー種別の値チェック
	 *
	 * @param mixed $attributes チェックする値
	 * @param string $errStr エラー文字列
	 * @param array $params オプション引数
	 * @return string エラー文字列(エラーの場合)
	 * @access public
	 */
	function validate($attributes, $errStr, $params)
	{
		if ($attributes['banner_type'] != BANNER_TYPE_LINK_VALUE
			&& $attributes['banner_type'] != BANNER_TYPE_URL_VALUE
			&& $attributes['banner_type'] != BANNER_TYPE_IMAGE_VALUE
			&& $attributes['banner_type'] != BANNER_TYPE_SOURCE_VALUE) {
			return $errStr;
		}
 
		return;
	}
}
?>