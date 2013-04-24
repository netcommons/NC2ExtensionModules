<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画配信表示アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_View_Main_Item_Init extends Action
{
	// リクエストパラメータを受け取るため
	var $album_id = null;
	var $sort = null;
	var $now_page = null;
	var $drag_drop = null;
	
	// バリデートによりセット
    var $multimedia_obj = null;
    
    // 使用コンポーネントを受け取るため
    var $multimediaView = null;
    var $db = null;
    
    // validatorから受け取るため
	
	// 値をセットするため
	var $item_count = null;
	var $item_list = null;
	
	//ページ
    var $pager = array();
    
    function execute()
    {
		$this->item_count = $this->db->countExecute("multimedia_item", array("album_id" => intval($this->album_id)));
    	if ($this->item_count === false) {
			return 'error';
		}
		
    	if($this->sort == null || $this->sort == "") {
    		$order_params = array(
				"item_sequence" => "ASC"
			);
    	} else {
    		if($this->sort != "") {
    			$sort_arr = explode(":", $this->sort);
    			$sort_col = $sort_arr[0];
	    		$sort_dir = $sort_arr[1];
	    		$sort_dir = ($sort_dir == null || $sort_dir == "ASC") ? "ASC" : "DESC";
	    		switch($sort_col) {
	    			case "item_name":
	    			case "item_play_count":
	    			case "item_vote_count":
	    			case "insert_time":
	    				break;
	    			default:
	    				$sort_col = "item_sequence";
	    				break;
	    		}
    		} else {
    			$sort_col = "item_sequence";
    			$sort_dir = "ASC";
    		}
    		
    		$order_params = array(
	 			$sort_col  => $sort_dir,
	 			"item_sequence"  => "ASC"
	 		);
    	}

    	$params = array("album_id" => $this->album_id);
    	$this->item_count = $this->multimediaView->getItemListCount($params);
		if($this->item_count === false) {
			return 'error';
		}
		if($this->drag_drop === _ON) {
			$this->item_list = $this->db->selectExecute("multimedia_item", array("album_id" => intval($this->album_id)), array("item_sequence"  => "ASC"));
	    	if($this->item_list === false) {
	    		return 'error';
	    	}
		}else {
	    	$this->multimediaView->setPageInfo($this->pager, $this->item_count, MULTIMEDIA_VISIBLE_ITEM_CNT, $this->now_page);
	    	$this->item_list = $this->multimediaView->getItemList($params, $order_params, $this->pager['disp_begin']);
	    	if($this->item_list === false) {
	    		return 'error';
	    	}
		}
		
		return 'success';
    }
}
?>