<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画配信表示画面
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_View_Main_Init extends Action
{

	// リクエストパラメータを受け取るため
	var $album_edit_flag = null;
	
	// バリデートによりセット
    var $multimedia_obj = null;
	
	// コンポーネントを受け取るため
    
    // 値をセットするため
	
	function execute()
	{
		if($this->multimedia_obj['display'] == MULTIMEDIA_DISPLAY_ALBUM || $this->album_edit_flag == _ON) {
			return 'channel';
		}else {
			return 'list';
		}
	}
}
?>
