<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * バナー一覧画面表示アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_View_Edit_List extends Action
{
	// リクエストパラメータを受け取るため
	var $category_id = null;
	var $module_id = null;
	var $scroll = null;

	// 使用コンポーネントを受け取るため
	var $bannerView = null;
	var $configView = null;
	var $request = null;
	var $filterChain = null;
	var $session = null;

	// 値をセットするため
	var $displayRow = null;
	var $categories = null;
	var $banners = null;
	var $bannerCount = null;
	var $isInitialize = null;

	/**
	 * バナー一覧画面表示アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		if ($this->scroll != _ON) {
			$config = $this->configView->getConfigByConfname($this->module_id, 'banner_list_row_count');
			if ($config === false) {
				return 'error';
			}

			$this->displayRow = $config['conf_value'];
			$this->request->setParameter('limit', $this->displayRow);

			$this->bannerCount = $this->bannerView->getNarrowBannerCount();

			$this->categories = $this->bannerView->getCategories();
			if ($this->categories === false) {
				return 'error';
			}
		}

		$this->banners = $this->bannerView->getBanners();
		if ($this->banners === false) {
			return 'error';
		}

		$mainActionName = $this->session->getParameter('_main_action_name');
		if ($mainActionName == 'banner_action_edit_initialize') {
			$this->isInitialize = true;
		}

		if ($this->scroll == _ON) {
			$view =& $this->filterChain->getFilterByName('View');
			$view->setAttribute('define:theme', 0);
			return 'scroll';
		}

		return 'screen';
	}
}
?>