<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * カテゴリシーケンス番号変更アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_Action_Edit_Category_Sequence extends Action
{
	// 使用コンポーネントを受け取るため
	var $bannerAction = null;

	/**
	 * カテゴリシーケンス番号変更アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		if (!$this->bannerAction->updateCategorySequence()) {
			return 'error';
		}

		return 'success';
	}
}
?>