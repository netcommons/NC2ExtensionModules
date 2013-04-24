<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * バナープレビュー画面表示アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_View_Edit_Preview extends Action
{
	// バリデートによりセット
	var $banner = null;

	/**
	 * バナープレビュー画面表示アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		return 'success';
	}
}
?>