<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画表示順変更アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Action_Main_Item_Sequence extends Action
{
	// リクエストパラメータを受け取るため
	var $album_id = null;
	var $drag_item_id = null;
	var $drag_sequence = null;
	var $drop_sequence = null;
	
	// コンポーネントを受け取るため
	var $db = null;
	
    /**
     * 動画表示順変更アクション
     *
     * @access  public
     */
    function execute()
    {
		$params = array(
			$this->album_id,
			$this->drag_sequence,
			$this->drop_sequence
		);
		
        if ($this->drag_sequence > $this->drop_sequence) {
        	$sql = "UPDATE {multimedia_item} ".
					"SET item_sequence = item_sequence + 1 ".
					"WHERE album_id = ? ".
					"AND item_sequence < ? ".
					"AND item_sequence > ?";
        } else {
        	$sql = "UPDATE {multimedia_item} ".
					"SET item_sequence = item_sequence - 1 ".
					"WHERE album_id = ? ".
					"AND item_sequence > ? ".
					"AND item_sequence <= ?";
        }
        
		$result = $this->db->execute($sql, $params);
		if($result === false) {
			return 'error';
		}
		
		if ($this->drag_sequence > $this->drop_sequence) {
			$this->drop_sequence++;
		}
		$params = array(
			$this->drop_sequence,
			$this->drag_item_id
		);

    	$sql = "UPDATE {multimedia_item} ".
				"SET item_sequence = ? ".
				"WHERE item_id = ?";
        $result = $this->db->execute($sql, $params);
		if($result === false) {
			return 'error';
		}
		
        return 'success';
    }
}
?>