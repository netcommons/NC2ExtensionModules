<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * バナーシーケンス番号チェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_Validator_BannerSequence extends Validator
{
	/**
	 * バナーシーケンス番号チェック
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
		$sequences = $bannerView->getBannerSequence();
		if (!$sequences) {
			return $errStr;
		}

		$dragBannerID = $attributes['drag_banner_id'];
		$dropBannerID = $attributes['drop_banner_id'];

		if ($attributes['position'] == 'top') {
			$sequences[$dropBannerID]--;
		}

		$request =& $container->getComponent('Request');
		$request->setParameter('drag_sequence', $sequences[$dragBannerID]);
		$request->setParameter('drop_sequence', $sequences[$dropBannerID]);

		return;
	}
}
?>