<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * カテゴリ一覧画面表示アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_View_Edit_Category_List extends Action
{
	// 使用コンポーネントを受け取るため
	var $bannerView = null;

	// 値をセットするため
	var $categories = null;

	/**
	 * カテゴリ一覧画面表示アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		$this->categories = $this->bannerView->getCategories();
		if($this->categories === false) {
			return 'error';
		}

		return 'success';
	}
}
?>
