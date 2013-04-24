<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画配信初期表示アクションクラス
 *
 * @package	 NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license	 http://www.netcommons.org/license.txt  NetCommons License
 * @project	 NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Multimedia_View_Edit_Initialize extends Action
{
	// 使用コンポーネントを受け取るため
	var $multimediaView = null;

	/**
	 * 動画配信初期表示アクション
	 *
	 * @access public
	 */
	function execute()
	{
		$multimediaCount =& $this->multimediaView->getMultimediaCount();
		if (empty($multimediaCount)) {
			return 'entry';
		}
		return 'display';
	}
}
?>