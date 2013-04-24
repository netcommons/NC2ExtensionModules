<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 検索アクション
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_View_Admin_Search extends Action
{
	// リクエストを受け取るため
	var $block_id = null;
	var $limit = null;
	var $offset = null;
	var $only_count = null;

	// WHERE句用パラメータ
	var $params = null;
	var $sqlwhere = null;
	
	// 値をセットするため
	var $count = 0;
	var $results = null;
	
	// Filterによりセット
	var $block_id_arr = null; 
	var $room_id_arr = null; 
	
	// コンポーネントを受け取るため
	var $db = null;

    /**
     * execute処理
     *
     * @access  public
     */
	function execute()
	{
		if ($this->block_id_arr) {
			$sqlwhere = " WHERE block.block_id IN (".implode(",", $this->block_id_arr).")";
		} else {
			return 'success';
		}
		$sqlwhere .= " AND (block.display_category=0 OR block.display_category=question.category_id) ";
		$sqlwhere .= $this->sqlwhere;
		$sql = "SELECT COUNT(*)" .
				" FROM {faq_question} question" .
				" INNER JOIN {faq} faq ON (question.faq_id=faq.faq_id)" .
				" INNER JOIN {faq_block} block ON (question.faq_id=block.faq_id)". 
				$sqlwhere;
		$result = $this->db->execute($sql, $this->params, null ,null, false);
		if ($result !== false) {
			$this->count = $result[0][0];
		} else {
			$this->count = 0;
		}
		if ($this->only_count == _ON) {
			return 'count';
		}

		if ($this->count > 0) {
			$sql = "SELECT block.block_id, faq.room_id, question.*" .
					" FROM {faq_question} question" .
					" INNER JOIN {faq} faq ON (question.faq_id=faq.faq_id)" .
					" INNER JOIN {faq_block} block ON (question.faq_id=block.faq_id)". 
					$sqlwhere.
					" ORDER BY question.insert_time DESC";
			$this->results =& $this->db->execute($sql ,$this->params, $this->limit, $this->offset, true, array($this, '_fetchcallback'));
		}
		return 'success';
	}
	
	/**
	 * fetch時コールバックメソッド(blocks)
	 * 
	 * @param result adodb object
	 * @access	private
	 */
	function _fetchcallback($result) 
	{
		$ret = array();
		$i = 0;
		while ($row = $result->fetchRow()) {
			$ret[$i] = array();
			$ret[$i]['block_id'] =  $row['block_id'];
			$ret[$i]['pubDate'] = $row['insert_time'];
			$ret[$i]['title'] = $row['question_name'];
    		$ret[$i]['url'] = BASE_URL.INDEX_FILE_NAME."?action=".DEFAULT_ACTION.
								"&block_id=".$row['block_id'].
								"&active_action=faq_view_main_init".
    		                    "&question_id=".$row['question_id'].
								//"&post_id=".($row['parent_id'] > 0 ? $row['parent_id'] : $row['post_id']).
								//($row['parent_id'] > 0 ? "&comment_flag="._ON : "").
								"#_faq_answer_".$row['question_id'];
			$ret[$i]['description'] = $row['question_answer'];
			$ret[$i]['user_id'] = $row['insert_user_id'];
			$ret[$i]['user_name'] = $row['insert_user_name'];
			$i++;
		}
		return $ret;
	}
}
?>