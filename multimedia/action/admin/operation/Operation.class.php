<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * モジュール操作時(move,copy,shortcut)に呼ばれるアクション
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Action_Admin_Operation extends Action
{
	var $mode = null;	//move or shortcut or copy
	// 移動元
	var $block_id = null;
	var $page_id = null;
	var $room_id = null;
	var $unique_id = null;
	
	// 移動先
	var $move_page_id = null;
	var $move_room_id = null;
	var $move_block_id = null;
	
	// コンポーネントを受け取るため
	var $db = null;
	var $commonOperation = null;
	
	function execute()
	{
		switch ($this->mode) {
    		case "move":    			
				//移動先ルームは既に動画配信モジュールがある場合,false
                 $where_params = array(
					"room_id"=> intval($this->move_room_id)
				);
    			$result = $this->db->selectExecute("multimedia", $where_params);
    			if($result === false || isset($result[0]) && $result[0]['multimedia_id'] != $this->unique_id) {
					return "false";
				}
                //存在チェック
                $where_params = array(
					"multimedia_id"=> intval($this->unique_id),
					"room_id"=> intval($this->room_id)
				);
    			$result = $this->db->selectExecute("multimedia", $where_params);
    			if($result === false || !isset($result[0])) {
					return "false";
				}
				
    			//更新
                //動画配信
    			$params = array(
					"room_id"=> intval($this->move_room_id)
				);
				$result = $this->db->updateExecute("multimedia", $params, $where_params, false);
				if($result === false) {
					return "false";
				}
                //チャンネル
                $multimedia_album = $this->db->selectExecute("multimedia_album", $where_params);
                if($multimedia_album === false) {
                    return "false";
                }
                $upload_id_arr = $this->commonOperation->getTextUploads("album_jacket", $multimedia_album);
                $result = $this->db->updateExecute("multimedia_album", $params, $where_params, false);
				if($result === false) {
					return "false";
				}
                //動画
                $multimedia_item = $this->db->selectExecute("multimedia_item", $where_params);
                if($multimedia_item === false) {
                    return "false";
                }
                $upload_id_item_arr = $this->commonOperation->getTextUploads("item_path", $multimedia_item);
                $upload_id_arr =  array_merge($upload_id_arr, $upload_id_item_arr);
                $result = $this->commonOperation->updWysiwygUploads($upload_id_arr, $this->move_room_id);
                if($result === false) {
                    return "false";
                }
                $func = array($this, "_fetchcallbackItem_id");
                $item_id_arr = $this->db->selectExecute("multimedia_item", $where_params, null, null, null, $func);
                if($item_id_arr === false) {
                    return "false";
                }
                $result = $this->db->updateExecute("multimedia_item", $params, $where_params, false);
				if($result === false) {
					return "false";
				}
                if(is_array($item_id_arr) && !empty($item_id_arr)) {
                    $item_where_str = implode("','", $item_id_arr);
                    $item_where_params = array(
                        "item_id IN ('". $item_where_str. "') " => null
                    );
                    $result = $this->db->updateExecute("mutlimedia_user_item", $params, $item_where_params, false);
                    if($result === false) {
                        return "false";
                    }
                 	$result = $this->db->updateExecute("multimedia_item_tag", $params, $item_where_params, false);
                    if($result === false) {
                        return "false";
                    }
                    $sql = "UPDATE {multimedia_tag} SET room_id=? WHERE tag_id IN (SELECT tag_id FROM {multimedia_item_tag} WHERE item_id IN ('".$item_where_str."'))";
                	$result = $this->db->execute($sql, $params);
                    if($result === false) {
                        return "false";
                    }
                }
                //コメント
                $result = $this->db->updateExecute("multimedia_comment", $params, $where_params, false);
				if($result === false) {
					return "false";
				}
                //ブロック
				$multimedia_block_params = array(
					"block_id"=> intval($this->move_block_id),
					"room_id"=> intval($this->move_room_id)
				);
				$where_params = array(
					"block_id"=> intval($this->block_id),
					"multimedia_id"=> intval($this->unique_id)
				);
				$result = $this->db->updateExecute("multimedia_block", $multimedia_block_params, $where_params, false);
				if($result === false) {
					return "false";
				}
				break;
			default:
				return "false";
		}
		return "true";
	}
	
	/**
	 * fetch時コールバックメソッド(config)
	 * @param result adodb object
	 * @access	private
	 */
	function &_fetchcallbackItem_id($result) {
		$item_id_arr = array();
		while ($row = $result->fetchRow()) {
			$item_id_arr[] = $row['item_id'];
		}
		return $item_id_arr;
	}
}
?>