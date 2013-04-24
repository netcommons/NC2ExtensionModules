<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画配信登録アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Action_Edit_Entry extends Action
{
	// リクエストパラメータを受け取るため
	var $multimedia_id = null;
	var $album_authority = null;
	var $upload_authority = null;
	var $vote_flag = null;
	var $comment_flag = null;
	
	// 使用コンポーネントを受け取るため
	var $request = null;
	var $multimediaView = null;
    
	// コンポーネントを受け取るため
	var $db = null;
	
	// 値をセットするため

	function execute()
	{
		$params = array(
			"album_authority" => $this->album_authority,
			"vote_flag" => intval($this->vote_flag),
			"comment_flag" => intval($this->comment_flag)
		);
		
		$multimedia_id = $this->request->getParameter("multimedia_id");
		$result = $this->db->updateExecute("multimedia", $params, array("multimedia_id" => $multimedia_id), true);
		if ($result === false) {
			return 'error';
		}
	    return 'success';
	}
}
?>