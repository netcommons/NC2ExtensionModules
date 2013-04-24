<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * カテゴリ削除アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_Action_Edit_Category_Delete extends Action
{
	// 使用コンポーネントを受け取るため
	var $bannerAction = null;

	/**
	 * カテゴリ削除アクション
	 *
	 * @access  public
	 */
		function execute()
	{
		$result = $this->bannerAction->deleteCategory();
		if ($result === false) {
			return 'error';
		}

		return 'success';
	}
}
?>