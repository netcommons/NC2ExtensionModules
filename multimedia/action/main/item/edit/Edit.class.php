<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画編集アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Action_Main_Item_Edit extends Action
{
	// リクエストパラメータを受け取るため
	var $item_id = null;
	var $item_name = null;
	var $item_description = null;
	var $item_tag = null;
	var $album_id = null;
	var $privacy = null;
	
	// バリデートによりセット
    var $item = null;
	
	// 使用コンポーネントを受け取るため
	var $db = null;
	var $multimediaAction = null;

	function execute()
	{
		$params = array(
			"album_id" => $this->album_id,
			"item_name" => $this->item_name,
			"item_description" => $this->item_description,
			"privacy" => $this->privacy
		);
		$where_params = array(
			"item_id" => $this->item_id
		);
	    $result = $this->db->updateExecute("multimedia_item", $params, $where_params, true);
	    if ($result === false) {
	    	return 'error';
	    }
		
	    if($this->album_id != $this->item['album_id']) {
	    	if(!$this->multimediaAction->setItemCount($this->album_id, 1)) {
	    		return 'error';
	    	}
	    	if(!$this->multimediaAction->setItemCount($this->item['album_id'], -1)) {
	    		return 'error';
	    	}
	    }
	    
	    if(!empty($this->item_tag)) {
	    	if(!$this->multimediaAction->setItemTag($this->item_id, $this->item_tag)) {
	    		return 'error';
	    	}
	    }
	    
	    return 'success';
	}
}
?>
