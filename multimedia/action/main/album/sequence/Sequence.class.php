<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * アルバム順序変更アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Action_Main_Album_Sequence extends Action
{
    // リクエストパラメータを受け取るため
	var $multimedia_id = null;
	var $drag_album_id = null;
	var $drag_sequence = null;
	var $drop_sequence = null;
	
	// 使用コンポーネントを受け取るため
    var $db = null;

    /**
     * アルバム順序変更アクション
     *
     * @access  public
     */
    function execute()
    {
		$params = array(
			$this->multimedia_id,
			$this->drag_sequence,
			$this->drop_sequence
		);

        if ($this->drag_sequence > $this->drop_sequence) {
        	$sql = "UPDATE {multimedia_album} ".
					"SET album_sequence = album_sequence + 1 ".
					"WHERE multimedia_id = ? ".
					"AND album_sequence < ? ".
					"AND album_sequence > ?";
        } else {
        	$sql = "UPDATE {multimedia_album} ".
					"SET album_sequence = album_sequence - 1 ".
					"WHERE multimedia_id = ? ".
					"AND album_sequence > ? ".
					"AND album_sequence <= ?";
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
			$this->drag_album_id
		);

    	$sql = "UPDATE {multimedia_album} ".
				"SET album_sequence = ? ".
				"WHERE album_id = ?";
        $result = $this->db->execute($sql, $params);
    	if($result === false) {
			return 'error';
		}
		
		return 'success';
    }
}
?>