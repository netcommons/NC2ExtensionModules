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
		) {
			if (empty($attributes['link_url'])
				|| $attributes['link_url'] == BANNER_DEFAULT_URL
			) {
				return $errStr;
			}
			if (!$this->_validateProtocol($attributes['link_url'])) {
				$container =& DIContainerFactory::getContainer();
				$filterChain =& $container->getComponent("FilterChain");
				$smartyAssign =& $filterChain->getFilterByName("SmartyAssign");
				return sprintf(_FORMAT_WRONG_ERROR, $smartyAssign->getLang('banner_link_url'));
			}
		}

		if (array_key_exists('image_url', $attributes)
			&& $attributes['banner_type'] == BANNER_TYPE_URL_VALUE
		) {
			if (empty($attributes['image_url'])
				|| $attributes['image_url'] == BANNER_DEFAULT_URL
			) {
				return $errStr;
			}
			if (!$this->_validateProtocol($attributes['image_url'])) {
				$container =& DIContainerFactory::getContainer();
				$filterChain =& $container->getComponent("FilterChain");
				$smartyAssign =& $filterChain->getFilterByName("SmartyAssign");
				return sprintf(_FORMAT_WRONG_ERROR, $smartyAssign->getLang('banner_image_url'));
			}
		}

		return;
	}

	/**
	 * プロトコルチェック
	 *
	 * @param   string $url
	 * @return  boolean
	 * @access  private
	 */
	function _validateProtocol($url)
	{
		$container =& DIContainerFactory::getContainer();
		$db =& $container->getComponent("DbObject");
		$sql = "SELECT protocol FROM {textarea_protocol}";
		$protocolArr = $db->execute($sql);
		if ($protocolArr === false) {
			return false;
		}

		if (preg_match("/^\.\//", $url) || preg_match("/^\.\.\//", $url)) {
			return true;
		}
		foreach ($protocolArr as $i=>$protocol) {
			if (preg_match("/^" . $protocol["protocol"] . "/", $url)) {
				return true;
			}
		}
		return false;
	}
}
?>