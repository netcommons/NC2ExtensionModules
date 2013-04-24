<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * バナー参照権限チェック
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_Validator_BannerView extends Validator
{
	/**
	 * バナー参照権限チェック
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
		$bannerView =& $container->getComponent('bannerView');
		$actionChain =& $container->getComponent('ActionChain');
		$actionName = $actionChain->getCurActionName();

		if (empty($attributes['banner_id'])
			&& $actionName == 'banner_action_edit_entry') {
			return;
		}
		if (empty($attributes['banner_id'])
			&& $actionName == 'banner_view_edit_entry') {
			$banner = $bannerView->getDefaultBanner();
		} else {
			$banner = $bannerView->getBanner();
		}
		if (empty($banner)) {
			return $errStr;
		}

		$request =& $container->getComponent('Request');
		$request->setParameter('banner', $banner);

		return;
	}
}
?>