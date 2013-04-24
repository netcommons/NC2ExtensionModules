<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * コメント登録アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Action_Main_Comment_Entry extends Action
{
	// リクエストパラメータを受け取るため
	var $comment_id = null;
	var $comment_value = null;
	
	// バリデートによりセット
	var $album = null;
	var $item = null;
	
    // 使用コンポーネントを受け取るため
    var $db = null;
 
    /**
     * コメント登録アクション
     *
     * @access  public
     */
    function execute()
    {
		if (empty($this->comment_id)) {
			$params = array(
				"item_id" => $this->item['item_id'],
				"album_id" => $this->album['album_id'],
				"multimedia_id" => $this->album['multimedia_id'],
				"comment_value" => $this->comment_value
			);
			$result = $this->db->insertExecute("multimedia_comment", $params, true, "comment_id");
		} else {
			$params = array(
				"comment_id" => $this->comment_id,
				"comment_value" => $this->comment_value
			);
			$result = $this->db->updateExecute("multimedia_comment", $params, "comment_id", true);
		}
		if (!$result) {
			return 'error';
		}
		
		return 'success';
    }
}
?>
