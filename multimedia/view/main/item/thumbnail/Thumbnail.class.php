<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画の画像表示アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_View_Main_Item_Thumbnail extends Action
{
	// リクエストパラメータを受け取るため
	
	// バリデートによりセット
    var $item = null;
    
    // 使用コンポーネントを受け取るため
    var $uploadsView = null;
    
    // validatorから受け取るため
	
	// 値をセットするため
    
    function execute()
    {
    	clearstatcache();
    	if($this->item['file_path'] != null) {
    		$this->uploadsView->headerOutput(FILEUPLOADS_DIR.$this->item['file_path'], $this->item['upload_id'].MULTIMEDIA_MOVIE_THUMBNAIL_NAME, null, true);
			exit;
    	}
    }
}
?>