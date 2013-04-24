<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 表示方法登録アクションクラス
 *
 * @package	 NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license	 http://www.netcommons.org/license.txt  NetCommons License
 * @project	 NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Multimedia_Action_Edit_Display extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	var $display = null;
	var $autoplay_flag = null;
	var $buffer_time = null;
	var $album_visible_row = null;
	
	// 使用コンポーネントを受け取るため
	var $db = null;

	/**
	 * 表示方法登録示アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		$update_params = array(
    		"display" => intval($this->display),
    		"autoplay_flag" => intval($this->autoplay_flag),
    		"buffer_time" => intval($this->buffer_time),
    		"album_visible_row" => intval($this->album_visible_row)
    	);
    	$result = $this->db->updateExecute("multimedia_block", $update_params, array("block_id" => $this->block_id), true);
    	if($result === false) {
    		return 'error';
    	}
    	
		return 'success';
	}
}
?>