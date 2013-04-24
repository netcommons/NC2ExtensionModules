<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * チャンネル表示順変更アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_View_Main_Album_Sequence extends Action
{
    // リクエストパラメータを受け取るため
    var $multimedia_id = null;
	
	// バリデートによりセット
	var $multimedia_obj = null;

    // 使用コンポーネントを受け取るため
    var $multimediaView = null;
 
    // 値をセットするため
    var $album_count = null;
	var $album_list = null;
    
    /**
     * チャンネル表示順変更
     *
     * @access  public
     */
    function execute()
    {   	
	    $order_params = array(
	 		"album_sequence"  => "ASC"
	 	);
	
    	$this->album_list = $this->multimediaView->getAlbumList($this->multimedia_obj['multimedia_id'], $order_params);
    	if($this->album_list === false) {
    		return 'error';
    	}
    	$this->album_count = count($this->album_list);

		return 'success';
    }
}
?>
