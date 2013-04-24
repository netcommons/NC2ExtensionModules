<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * コメント削除アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Action_Main_Comment_Delete extends Action
{
	// リクエストパラメータを受け取るため
	var $comment_id = null;
	
    // 使用コンポーネントを受け取るため
    var $db = null;
 
    /**
     * コメント削除アクション
     *
     * @access  public
     */
    function execute()
    {
    	$params = array(
			"comment_id" => $this->comment_id
		);
    	if (!$this->db->deleteExecute("multimedia_comment", $params)) {
    		return 'error';
    	}

		return 'success';
    }
}
?>
