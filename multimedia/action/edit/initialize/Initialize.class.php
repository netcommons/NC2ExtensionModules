<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画配信初期登録アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Action_Edit_Initialize extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
    var $multimedia_id = null;
	
	// 使用コンポーネントを受け取るため
	var $multimediaCount = null;
    
	// コンポーネントを受け取るため
	var $db = null;
	var $request = null;
	var $multimediaView = null;
	
	// 値をセットするため

	function execute()
	{
        $multimedia_obj =& $this->multimediaView->getDefaultMultimedia();
        $multimedia_id = "";
		if(empty($this->multimediaCount)) {
			$params = array(
				"album_authority" => intval($multimedia_obj['album_authority']),
				"vote_flag" => intval($multimedia_obj['vote_flag']),
				"comment_flag" => intval($multimedia_obj['comment_flag']),
				"confirm_flag" => intval($multimedia_obj['confirm_flag'])
			);

		    $multimedia_id = $this->db->insertExecute("multimedia", $params, true, "multimedia_id");
			if ($multimedia_id === false) {
				return 'error';
			}
			
			$default_album_array = explode("|", MULTIMEDIA_DEFAULT_ALBUMS);
	    	$display_seq = 0;
	    	foreach(array_keys($default_album_array) as $i) {
	    		$display_seq++;
	    		$params = array(
					"multimedia_id" => $multimedia_id,
					"album_name" => $default_album_array[$i],
					"album_sequence" => $display_seq,
	    			"album_jacket" => $multimedia_obj['album_jacket'],
	    			"public_flag" => _ON,
	    			"width" => 85,
	    			"height" => 85
				);
				$result = $this->db->insertExecute("multimedia_album", $params, true, "album_id");
	    		if ($result === false) {
					return 'error';
				}
	    	}
		}else {
            $multimedia_id = $this->multimedia_id;
        }

        $where_params = array("block_id" => $this->block_id);
        $block = $this->db->selectExecute("multimedia_block", $where_params);
        if($block === false) {
            return 'error';
        }
        if(empty($block)) {
            $ins_params = array(
		    	"block_id" => $this->block_id,
		    	"multimedia_id" => $multimedia_id,
				"display" => $multimedia_obj['display'],
	    		"autoplay_flag" => intval($multimedia_obj['autoplay_flag']),
	    		"buffer_time" => intval($multimedia_obj['buffer_time']),
				"new_period" => $multimedia_obj['new_period']
		    );

			$result = $this->db->insertExecute("multimedia_block", $ins_params, true);
			if ($result === false) {
				return 'error';
			}
        }
		
	    return 'success';
	}
}
?>