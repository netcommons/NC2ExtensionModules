<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * カテゴリ登録アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_Action_Edit_Category_Entry extends Action
{
	// リクエストパラメータを受け取るため
	var $prefix_id_name = null;

	// 使用コンポーネントを受け取るため
	var $bannerAction = null;
	var $bannerView = null;

	// 値をセットするため
	var $categories = null;

	/**
	 * カテゴリ登録アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		if (!$this->bannerAction->setCategory()) {
			return 'error';
		}

		if ($this->prefix_id_name == 'banner_entry') {
			$this->categories = $this->bannerView->getCategories();
			if($this->categories === false) {
				return 'error';
			}

			return 'select';
		}

		return 'success';
	}
}
?>