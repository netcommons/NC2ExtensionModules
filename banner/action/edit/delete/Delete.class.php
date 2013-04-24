<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * バナー削除アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_Action_Edit_Delete extends Action
{
	// 使用コンポーネントを受け取るため
	var $bannerAction = null;

	/**
	 * バナー削除アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		if (!$this->bannerAction->deleteBanner()) {
			return 'error';
		}

		return 'success';
	}
}
?>