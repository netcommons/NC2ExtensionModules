<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * [[機能説明]]
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_View_Main_Post extends Action
{
    // リクエストパラメータを受け取るため
    var $question_id = null;
    var $category_id = null;
    
    // バリデートによりセット
	var $faq_obj = null;

    // 使用コンポーネントを受け取るため
    var $db = null;
    var $faqView = null;
    var $edit_flag = false;
    var $cate_can_select = true;
 
    // 値をセットするため
    var $categories = null;
    var $post = null;
    
    /**
     * [[機能説明]]
     *
     * @access  public
     */
    function execute()
    {
    	$this->categories = $this->faqView->getCatByFaqId($this->faq_obj['faq_id']);
    	if($this->categories === false) {
    		return 'error';
    	}
    	
    	$currentFaq = $this->faqView->getCurrentFaq();
    	if (0!=$currentFaq['display_category']) {
    		$this->cate_can_select = false;
    	}
    	
    	if(!empty($this->question_id)) {
    		$result = $this->faqView->getQuestion($this->question_id);
    		if($result === false || !isset($result[0])) {
    			return 'error';
    		}
    		$this->post = $result[0];
    	}else {
    		$this->edit_flag = _OFF;
    		
    		$this->post = array(
    			"faq_id" => $this->faq_obj['faq_id'],
    			"category_id" => $currentFaq['display_category'],
    			"question_name" => "",
    			"question_answer" => ""
    		);
    	}

        return 'success';
    }
}
?>
