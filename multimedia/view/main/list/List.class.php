<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画配信表示画面
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_View_Main_List extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	var $now_page = null;
	var $view_type = null;
	
	// バリデートによりセット
    var $multimedia_obj = null;
	
	// コンポーネントを受け取るため
	var $db = null;
    var $multimediaView = null;
    var $session = null;
    
    // 値をセットするため
    var $album_count = null;
    var $album_list = null;
    var $album = null;
    var $album_name = null;
    var $album_item_count = null;
    var $date = null;
    var $sort = null;
    var $item_count = null;
    var $item_list = null;

	//ページ
    var $pager = array();
	
	function execute()
	{
		$view_type = $this->session->getParameter("multimedia_list_view_type".$this->block_id);
		if(!empty($this->view_type)) {
			$this->session->setParameter("multimedia_list_view_type".$this->block_id, $this->view_type);
		}else if(!empty($view_type)) {
			$this->view_type = $view_type;
		}else {
			$this->view_type = MULTIMEDIA_MOVIE_TYPE_NEW;
		}
		if(!isset($this->view_type) || $this->view_type == MULTIMEDIA_MOVIE_TYPE_NEW) {
			$order_params = array("insert_time" => "DESC");
		}else if($this->view_type == MULTIMEDIA_MOVIE_TYPE_POP) {
			$order_params = array("item_play_count" => "DESC");
		}else if($this->view_type == MULTIMEDIA_MOVIE_TYPE_FAV) {
			$order_params = array("item_vote_count" => "DESC");
		}
		
    	$this->album_list = $this->multimediaView->getAlbumList($this->multimedia_obj['multimedia_id'], array("album_sequence"  => "ASC"));
		if($this->album_list === false) {
    		return 'error';
    	}
    	$this->album_count = count($this->album_list);

		if($this->album_count > 0) {
			$album = $this->session->getParameter("multimedia_list_album".$this->block_id);
			if(!empty($this->album)) {
				$this->session->setParameter("multimedia_list_album".$this->block_id, $this->album);
			}else if(!empty($album)) {
				$this->album = $album;
			}else {
				$this->album = MULTIMEDIA_ALL;
			}
	
			if($this->album == MULTIMEDIA_ALL) {
				$this->album_name = MULTIMEDIA_ALBUM_DEFAULT;
			}else {
				foreach($this->album_list as $album) {
					if($album['album_id'] == $this->album) {
						$this->album_name = $album['album_name'];
						$this->album_item_count = $album['item_count'];
					}
				}
			}
			if(empty($this->album_name)) {
				$this->album = MULTIMEDIA_ALL;
				$this->album_name = MULTIMEDIA_ALBUM_DEFAULT;
				$this->session->setParameter("multimedia_list_album".$this->block_id, $this->album);
			}
		}
		
		$date = $this->session->getParameter("multimedia_list_date");
		if(!empty($this->date)) {
			$this->session->setParameter("multimedia_list_date", $this->date);
		}else if(!empty($date)) {
			$this->date = $date;
		}else {
			$this->date = MULTIMEDIA_ALL;
		}
		
		$sort = $this->session->getParameter("multimedia_list_sort");
		if(!empty($this->sort)) {
			$this->session->setParameter("multimedia_list_sort", $this->sort);
		}else if(!empty($sort)) {
			$this->sort = $sort;
		}else {
			$this->sort = MULTIMEDIA_SORT_DATE_DESC;
		}
		
		$now_page = $this->session->getParameter("multimedia_list_now_page");
		if(!empty($this->now_page)) {
			$this->session->setParameter("multimedia_list_now_page", $this->now_page);
		}else if(!empty($now_page)){
			$this->now_page = $now_page;
		}
		
		$params = array(
			"multimedia_id" => intval($this->multimedia_obj['multimedia_id']),
			"agree_flag" => _ON
		);
		
		if(!empty($this->album) && $this->album != MULTIMEDIA_ALL) {
			$params = array_merge($params, array("album_id" => $this->album));
		}
		
		if($this->date == MULTIMEDIA_DATE_DAY) {
			$params = array_merge($params, array("date(T.insert_time) = curdate()" => null));
		}else if($this->date == MULTIMEDIA_DATE_WEEK) {
			$params = array_merge($params, array("weekofyear(T.insert_time) = weekofyear(now())" => null));
		}else if($this->date == MULTIMEDIA_DATE_MONTH) {
			$params = array_merge($params, array("month(T.insert_time) = month(now())" => null));
		}
		
		if($this->sort == "" || $this->sort == MULTIMEDIA_SORT_DATE_DESC) {
			$order_params = array_merge($order_params, array("insert_time" => "DESC"));
		}else if($this->sort == MULTIMEDIA_SORT_DATE_ASC) {
			$order_params = array_merge($order_params, array("insert_time" => "ASC"));
		}else if($this->sort == MULTIMEDIA_SORT_TITLE_ASC) {
			$order_params = array_merge($order_params, array("item_name" => "ASC"));
		}else if($this->sort == MULTIMEDIA_SORT_PLAY_DESC) {
			$order_params = array("item_play_count" => "DESC");
		}else if($this->sort == MULTIMEDIA_SORT_VOTE_DESC) {
			$order_params = array("item_vote_count" => "DESC");
		}

		$this->item_count = $this->multimediaView->getItemListCount($params);
		if($this->item_count === false) {
			return 'error';
		}
		$this->multimediaView->setPageInfo($this->pager, $this->item_count, MULTIMEDIA_VISIBLE_ITEM_CNT, $this->now_page);
		$this->item_list = $this->multimediaView->getItemList($params, $order_params, $this->pager['disp_begin']);
    	if($this->item_list === false) {
    		return 'error';
    	}
    	
    	return 'success';
	}
}
?>