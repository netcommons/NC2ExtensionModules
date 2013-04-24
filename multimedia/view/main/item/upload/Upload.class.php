<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画アップロード画面表示アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_View_Main_Item_Upload extends Action
{
	// リクエストパラメータを受け取るため
	var $album_id = null;
	
	// バリデートによりセット
	var $multimedia_obj = null;
	
	// 使用コンポーネントを受け取るため
	var $filterChain = null;
	var $multimediaView = null;

	// 値をセットするため
	var $dialog_name = null;
	var $album_list = null;
	var $album = null;

    function execute()
    {
    	$smartyAssign =& $this->filterChain->getFilterByName("SmartyAssign");
		$this->dialog_name = $smartyAssign->getLang("multimedia_item_upload_popup_name");
		
    	$order_params = array(
			"album_sequence" => "ASC"
		);
		
		if(empty($this->album_id)) {
			$this->album_list = $this->multimediaView->getAlbumList($this->multimedia_obj['multimedia_id'], $order_params);
	    	if($this->album_list === false) {
	    		return 'error';
	    	}
		}
		
		return 'success';
    }
}
?>
