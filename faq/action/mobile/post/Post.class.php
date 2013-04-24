<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 携帯・スマホからのFAQ項目登録アクションクラス
 *
 * @package     NetCommons
 * @author      Toshihide Hashimoto, Rika Fujiwara
 * @copyright   2010 AllCreator Co., Ltd.
 * @project     NC Support Project, provided by AllCreator Co., Ltd.
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @access      public
 */

class Faq_Action_Mobile_Post extends Action
{
	// リクエストパラメータを受け取るため
	var $regist = null;

	/**
	 * 携帯・スマホからのFAQ項目登録アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		if (isset($this->regist)) {
			return 'regist';
		} else {
			return 'cancel';
		}
	}
}
?>