<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * URL項目チェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_Validator_UrlRequired extends Validator
{
	/**
	 * URL項目チェックバリデータ
	 *
	 * @param   mixed   $attributes チェックする値
	 * @param   string  $errStr     エラー文字列
	 * @param   array   $params     オプション引数
	 * @return  string  エラー文字列(エラーの場合)
	 * @access  public
	 */
	function validate($attributes, $errStr, $params)
	{
		if (array_key_exists('link_url', $attributes)
			&& $attributes['banner_type'] != BANNER_TYPE_SOURCE_VALUE
			&& (empty($attributes['link_url'])
				|| $attributes['link_url'] == BANNER_DEFAULT_URL)) {
			return $errStr;
		}

		if (array_key_exists('image_url', $attributes)
			&& $attributes['banner_type'] == BANNER_TYPE_URL_VALUE
			&& (empty($attributes['image_url'])
				|| $attributes['image_url'] == BANNER_DEFAULT_URL)) {
			return $errStr;
		}

		return;
	}
}
?>