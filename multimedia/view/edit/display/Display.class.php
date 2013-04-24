<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 表示方法変更画面表示アクションクラス
 *
 * @package	 NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license	 http://www.netcommons.org/license.txt  NetCommons License
 * @project	 NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Multimedia_View_Edit_Display extends Action
{
	// 使用コンポーネントを受け取るため
	var $multimediaView = null;

	// 値をセットするため
	var $multimedia = null;

	/**
	 * 表示方法変更画面表示アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		$this->multimedia =& $this->multimediaView->getCurrentMultimedia();
		if (empty($this->multimedia)) {
			$this->multimedia =& $this->multimediaView->getDefaultMultimedia();
		}

		return 'success';
	}
}
?>