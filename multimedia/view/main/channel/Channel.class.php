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
class Multimedia_View_Main_Channel extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	var $album_edit_flag = null;
	var $visible_row = null;
	var $now_page = null;
	
	// バリデートによりセット
    var $multimedia_obj = null;
	
	// コンポーネントを受け取るため
    var $multimediaView = null;
    var $session = null;
    
    // 値をセットするため
    var $album_count = null;
    var $album_list = null;

	//ページ
	var $pager = array();
	
	function execute()
	{
		$this->album_count = $this->multimediaView->getAlbumCount($this->multimedia_obj['multimedia_id']);
		if($this->album_count === false) {
			return 'error';
		}
		
		if($this->album_count == 0) {
			return 'success';
		}
		
		if(!$this->album_edit_flag) {
			$visible_row = $this->session->getParameter("multimedia_channel_visible_row".$this->block_id);
			if ($this->visible_row != "") {
				if($visible_row != "" && $this->visible_row != $visible_row) {
					$this->now_page = 1;
				}
				$this->session->setParameter("multimedia_channel_visible_row".$this->block_id, $this->visible_row);
	    	}else if($visible_row != ""){
				$this->visible_row = $visible_row;
			}else {
	    		$this->visible_row = $this->multimedia_obj['album_visible_row'];
	    	}
	    	
			$now_page = $this->session->getParameter("multimedia_channel_now_page".$this->block_id);
			if(!empty($this->now_page)) {
				$this->session->setParameter("multimedia_channel_now_page".$this->block_id, $this->now_page);
			}else if(!empty($now_page)){
				$this->now_page = $now_page;
			}
		}
		
	    $this->multimediaView->setPageInfo($this->pager, $this->album_count, $this->visible_row, $this->now_page);
		$order_params = array(
			"album_sequence" => "ASC"
		);
		$this->album_list = $this->multimediaView->getAlbumList($this->multimedia_obj['multimedia_id'], $order_params, $this->visible_row, $this->pager['disp_begin']);
    	if($this->album_list === false) {
    		return 'error';
    	}
    	
    	return 'success';
	}
}
?>