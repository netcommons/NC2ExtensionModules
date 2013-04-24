<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * バナー表示順変更画面表示アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_View_Edit_Sequence extends Action
{
	// 使用コンポーネントを受け取るため
	var $bannerView = null;

	// 値をセットするため
	var $banners = null;

	/**
	 * バナー表示順変更画面表示アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		$this->banners = $this->bannerView->getBanners();
		if ($this->banners === false) {
			return 'error';
		}

		return 'success';
	}
}
?>