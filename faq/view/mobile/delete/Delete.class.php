<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 削除確認画面表示
 *
 * @package     NetCommons
 * @author      Toshihide Hashimoto, Rika Fujiwara
 * @copyright   2010 AllCreator Co., Ltd.
 * @project     NC Support Project, provided by AllCreator Co., Ltd.
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @access      public
 */

class Faq_View_Mobile_Delete extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	var $faq_id = null;
	var $question_id = null;

	/**
	 * execute実行
	 *
	 * @access  public
	 */
	function execute()
	{
		return 'success';
	}
}
?>
