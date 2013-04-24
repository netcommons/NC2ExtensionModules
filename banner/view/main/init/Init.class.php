<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * バナー画面表示アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_View_Main_Init extends Action
{
	// 使用コンポーネントを受け取るため
	var $bannerView = null;

	// 値をセットするため
	var $banners = null;
	var $banner = null;

	/**
	 * バナー画面表示アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		$bannerDisplay = $this->bannerView->getBannerDisplay();
		if ($bannerDisplay === false) {
			return 'error';
		}

		if ($bannerDisplay['display_type'] == BANNER_DISPLAY_LIST_VALUE) {
			$this->banners = $this->bannerView->getCheckedBanners();
			if ($this->banners === false) {
				return 'error';
			}

			if (!empty($this->banners)) {
				return 'list';
			}

			return 'noBanner';
		}

		if ($bannerDisplay['display_type'] == BANNER_DISPLAY_SEQUENCE_VALUE) {
			$this->banner = $this->bannerView->getSequenceBanner();
		} else {
			$this->banner = $this->bannerView->getRandomBanner();
		}
		if ($this->banner === false) {
			return 'error';
		}

		if (!empty($this->banner)) {
			return 'single';
		}

		return 'noBanner';
	}
}
?>