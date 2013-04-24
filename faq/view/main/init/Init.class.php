<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 質問一覧画面表示アクションクラス
 *
 * @package     NetCommons
 * @author      Ka
 * @copyright   2006-2009 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_View_Main_Init extends Action
{
    // リクエストパラメータを受け取るため
    var $faq_id = null;
    var $category_id = null;
    var $display_row = null;
    var $question_id = null;
	var $block_id = null;

    // バリデートによりセット
    var $faq_obj = null;

    // 使用コンポーネントを受け取るため
    var $faqView = null;
    var $session = null;
	var $mobileView = null;

    // 値をセットするため
    var $categories = null;
    var $question_count = null;
    var $question_list = null;
    var $show_cate_list = true;
	var $block_num = null;

    //ページ
    var $data_cnt    = 0;
    var $now_page    = null;
    var $total_page  = 0;
    var $next_link   = FALSE;
    var $prev_link   = FALSE;
    var $disp_begin  = 0;
    var $disp_end    = 0;
    var $link_array  = NULL;

	/**
	 * 質問一覧画面表示アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		if ($this->session->getParameter('_mobile_flag') == true) {
			$this->block_num = $this->mobileView->getCountForBlockInPageWithBlock($this->block_id);
		}

        if(empty($this->faq_id)) {
            $this->faq_id = $this->faq_obj['faq_id'];
        }
        
        $this->categories = $this->faqView->getCatByFaqId($this->faq_id);
        if($this->categories === false) {
            return 'error';
        }
        
        $currentFaq = $this->faqView->getCurrentFaq();
        $dispCate = $currentFaq['display_category'];
        
        if (0!=intval($dispCate)) {
        	$this->category_id = $dispCate;
        	$this->show_cate_list = false;
        }
        
        $category_id = $this->session->getParameter(array("faq", $this->faq_id, "category_id"));
        if($this->category_id != "") {
            $this->session->setParameter(array("faq", $this->faq_id, "category_id"), $this->category_id);
        }else if($category_id != ""){
            $this->category_id = $category_id;
        }

        $display_row = $this->session->getParameter(array("faq", $this->faq_id, "display_row"));
        if($this->display_row != "") {
            if($display_row != "" && $this->display_row != $display_row) {
                $this->now_page = 1;
            }
            $this->session->setParameter(array("faq", $this->faq_id, "display_row"), $this->display_row);
        }else if($display_row != ""){
            $this->display_row = $display_row;
        }else {
            $this->display_row = $this->faq_obj['display_row'];
        }	
        
        $now_page = $this->session->getParameter(array("faq", $this->faq_id, "now_page"));
        if(!empty($this->now_page)) {
            $this->session->setParameter(array("faq", $this->faq_id, "now_page"), $this->now_page);
        }else if(!empty($now_page)){
            $this->now_page = $now_page;
        }
        
        $this->question_count = $this->faqView->getQuestionCount($this->faq_id, $this->category_id);
        if($this->question_count === false) {
            return 'error';
        }
        
        if(!empty($this->display_row)) {
            $this->setPageInfo($this->question_count, $this->display_row, $this->now_page);
        }
        $this->question_list = $this->faqView->getQuestionList($this->faq_id, $this->category_id, $this->display_row, $this->disp_begin);
        if($this->question_list === false) {
            return 'error';
        }
        /**
        foreach($this->question_list as &$row) {
        	$row['question_name'] = nl2br($row['question_name']);
        }
        */
        
        return 'success';
    }
    
   /**
     * ページに関する設定を行います
     *
     * @param    int    disp_cnt    1ページ当り表示件数
     * @param    int    now_page    現ページ
     */
    function setPageInfo($data_cnt, $disp_cnt, $now_page = NULL){
        $this->data_cnt = $data_cnt;
        // now page
        $this->now_page = (NULL == $now_page) ? 1 : $now_page;
        // total page
        $this->total_page = ceil($this->data_cnt / $disp_cnt);
        if($this->total_page < $this->now_page) {
            $this->now_page = 1;
        }
        // link array {{
        if(($this->now_page - FAQ_FRONT_AND_BEHIND_LINK_CNT) > 0){
            $start = $this->now_page - FAQ_FRONT_AND_BEHIND_LINK_CNT;
        }else{
            $start = 1;
        }
        if(($this->now_page + FAQ_FRONT_AND_BEHIND_LINK_CNT) >= $this->total_page){
            $end = $this->total_page;
        }else{
            $end = $this->now_page + FAQ_FRONT_AND_BEHIND_LINK_CNT;
        }
        $i = 0;
        for($i = $start; $i <= $end; $i++){
            $this->link_array[] = $i;
        }
        // next link
        if($disp_cnt < $this->data_cnt){
            if($this->now_page < $this->total_page){
                $this->next_link = TRUE;
            }
        }
        // prev link
        if(1 < $this->now_page){
            $this->prev_link = TRUE;
        }
        // begin disp number
        $this->disp_begin = ($this->now_page - 1) * $disp_cnt;
        // end disp number
        $tmp_cnt = $this->now_page * $disp_cnt;
        $this->disp_end = ($this->data_cnt < $tmp_cnt) ? $this->data_cnt : $tmp_cnt;
    }
}
?>
