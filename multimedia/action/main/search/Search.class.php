<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画検索
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Action_Main_Search extends Action
{
    // リクエストパラメータを受け取るため
    var $room_id = null;
    var $keyword = null;
    var $tag_id = null;
    var $name_check = null;
    var $description_check = null;
    var $poster_check = null;
    var $tag_check = null;
    var $search_album = null;
    var $search_date = null;
    var $search_duration = null;
    var $sort = null;
    var $now_page = null;
    
    // バリデートによりセット
	var $multimedia_obj = null;

    // 使用コンポーネントを受け取るため
    var $db = null;
    var $multimediaView = null;
    var $multimediaAction = null;
    var $session = null;
    
    // 値をセットするため
    var $_where_params = null;
    var $_order_params = null;
    var $item_count = null;
	var $item_list = null;
	
	//ページ
    var $pager = array();
    
	/**
	 * execute実行
	 *
	 * @access  public
	 */
    function execute()
    {
    	if($this->keyword != "") { 
	    	if($this->name_check == _ON) {
	    		$this->_where_params['T.item_name'] = $this->keyword;
	    	}
	   		if($this->description_check == _ON) {
	    		$this->_where_params['T.item_description'] = $this->keyword;
	    	}
	    	if($this->poster_check == _ON) {
	    		$this->_where_params['T.insert_user_name'] = $this->keyword;
	    	}
	    	if($this->tag_check == _ON) {
	    		$tag_value = $this->multimediaAction->getSynonym($this->keyword);
	    		$tag_id = $this->db->selectExecute("multimedia_tag", array("tag_value" => $tag_value, "room_id" => $this->room_id));
	    		if($tag_id === false) {
	    			return 'error';
	    		}
	    		if(isset($tag_id[0])) {
	    			$this->tag_id = $tag_id[0]['tag_id'];
	    			$this->_where_params['G.tag_id'] = $this->tag_id;
	    		}
	    	}
    	}
    	
    	if($this->sort == "" || $this->sort == MULTIMEDIA_SORT_DATE_DESC) {
			$this->_order_params = array("T.insert_time" => "DESC");
		}else if($this->sort == MULTIMEDIA_SORT_DATE_ASC) {
			$this->_order_params = array("T.insert_time" => "ASC");
		}else if($this->sort == MULTIMEDIA_SORT_TITLE_ASC) {
			$this->_order_params = array("T.item_name" => "ASC");
		}else if($this->sort == MULTIMEDIA_SORT_PLAY_DESC) {
			$this->_order_params = array("T.item_play_count" => "DESC");
		}else if($this->sort == MULTIMEDIA_SORT_VOTE_DESC) {
			$this->_order_params = array("T.item_vote_count" => "DESC");
		}

    	$this->_getSearchItems();
    	
    	return 'success';
    }
    
	/**
     * 動画検索結果一覧
     * @return string return_str
     * @access  public
     */
    function _getSearchItems() {	
    	// 検索結果の全数を取得
		$from_str =	" FROM {multimedia_item} T ";
		$from_str .= "LEFT JOIN {multimedia_album} A ON T.album_id=A.album_id ";
		$from_str .= $this->multimediaView->_getAuthorityFromSQL();
    	$where_params = array();
		$where_str = " WHERE T.multimedia_id=".$this->multimedia_obj['multimedia_id']." ";
		$where_param_str = "";
    	if(count($this->_where_params) > 0) {
	    	foreach($this->_where_params as $key => $where_param) {
	    		if($key == "G.tag_id") {
	    			$from_str .= " ,{multimedia_item_tag} G ";
	    			$where_param_str .= "(T.item_id=G.item_id AND ".$key." = ?) OR ";
	    			$where_params[] = $where_param;
	    			continue;
	    		}
    			$where_param_str .= $key." LIKE ? OR ";
    			$where_params[] = "%".$where_param."%";
    		}
    		$where_param_str = "(".substr($where_param_str, 0, -3).") ";
            $where_str .= "AND ".$where_param_str;
    	}
    	if(!empty($this->search_album) && $this->search_album != MULTIMEDIA_ALL) {
	    	$where_str .= "AND T.album_id=".(int)$this->search_album." ";
    	}
    	
    	if(!empty($this->search_date) && $this->search_date != MULTIMEDIA_ALL) {
	    	$where_str .= "AND ";
    		if($this->search_date == MULTIMEDIA_DATE_DAY) {
    			$where_str .= "date(T.insert_time)=curdate() ";
			}else if($this->search_date == MULTIMEDIA_DATE_WEEK) {
				$where_str .= "weekofyear(T.insert_time)=weekofyear(now()) ";
			}else if($this->search_date == MULTIMEDIA_DATE_MONTH) {
				$where_str .= "month(T.insert_time)=month(now()) ";
			}
    	}
    	
    	if(!empty($this->search_duration) && $this->search_duration != MULTIMEDIA_ALL) {
	    	$where_str .= "AND ";
    		if($this->search_duration == MULTIMEDIA_MOVIE_SHORT) {
    			$where_str .= "T.duration > 0 AND T.duration < ".(MULTIMEDIA_MOVIE_SHORT*60)." ";
			}else if($this->search_duration == MULTIMEDIA_MOVIE_MEDIUM) {
				$where_str .= "T.duration >= ".(MULTIMEDIA_MOVIE_SHORT*60)." AND T.duration < ".(MULTIMEDIA_MOVIE_MEDIUM*60)." ";
			}else if($this->search_duration == MULTIMEDIA_MOVIE_LONG) {
				$where_str .= "T.duration >= ".(MULTIMEDIA_MOVIE_MEDIUM*60)." ";
			}
    	}
    	
    	$where_str .= $this->multimediaView->_getAuthorityWhereSQL($where_params);
		$sql = "SELECT COUNT(DISTINCT T.item_id) AS count ".
				$from_str.$where_str;
				
		$result = $this->db->execute($sql, $where_params, null, null ,false);
		if ($result === false || !isset($result[0][0])) {
			$this->db->addError();
	       	return 'error';
		}
		$this->item_count = $result[0][0];

    	// データ取得
    	$sql = "SELECT DISTINCT T.* ";
    	$sql .= $from_str;
    	$sql .= $where_str;
    	$sql .= $this->db->getOrderSQL($this->_order_params);
		//ページャ設定
		$this->multimediaView->setPageInfo($this->pager, $this->item_count, MULTIMEDIA_SEARCH_VISIBLE_ITEM_CNT, $this->now_page);
    	$result = $this->multimediaView->getSearchItemList($sql, $where_params, $this->pager['disp_begin']);
		if ($result === false) {
			$this->db->addError();
	       	return 'error';
		}
		$this->item_list = $result;
    }
}
?>
