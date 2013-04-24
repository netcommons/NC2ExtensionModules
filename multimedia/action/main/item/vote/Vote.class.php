<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 投票アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Action_Main_Item_Vote extends Action
{
	// リクエストパラメータを受け取るため
	var $album_id = null;
	var $item_id = null;
	
    // 使用コンポーネントを受け取るため
    var $db = null;
    var $session = null;
    var $request = null;
    
    /**
     * 投票アクション
     *
     * @access  public
     */
    function execute()
    {		
		$user_id = $this->session->getParameter("_user_id");
		
		if (empty($user_id)) {
			$votes = $this->session->getParameter("multimedia_votes");
			$votes[] = $this->item_id;
			$session->setParameter("multimedia_votes", $votes);
 		} else {
			$params = array(
				"user_id" => $user_id,
				"item_id" => $this->item_id,
				"vote_flag" => _ON
			);
	        if (!$this->db->insertExecute("mutlimedia_user_item", $params, true)) {
				return 'error';
			}
		}

		$params = array($this->item_id);
		$sql = "UPDATE {multimedia_item} ".
				"SET item_vote_count = item_vote_count + 1 ".
				"WHERE item_id = ?";
		$result = $this->db->execute($sql, $params);
		if ($result === false) {
			return 'error';
		}

		$params = array($this->album_id);
		$sql = "UPDATE {multimedia_album} ".
				"SET album_vote_count = album_vote_count + 1 ".
				"WHERE album_id = ?";
		$result = $this->db->execute($sql, $params);
		if ($result === false) {
			return 'error';
		}

		return 'success';
    }
}
?>