<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * FAQ１件詳細表示画面
 *
 * @package     NetCommons
 * @author      Toshihide Hashimoto, Rika Fujiwara
 * @copyright   2010 AllCreator Co., Ltd.
 * @project     NC Support Project, provided by AllCreator Co., Ltd.
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @access      public
 */

class Faq_View_Mobile_Detail extends Action
{
	// validatorから受け取るため
	var $question = null;

	/**
	 * 指定されたIDのFAQ項目を取得する
	 *
	 * @access  public
	 */
	function execute()
	{
		return 'success';
	}

}
?>
