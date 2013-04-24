<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * [[機能説明]]
 *
 * @package     NetCommons
 * @author      Ka
 * @copyright   2006-2009 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_View_Main_Sequence extends Action 
{
	// リクエストパラメータを受け取るため
	var $faq_id = null;
    var $category_id = null;
    
    // 使用コンポーネントを受け取るため
    var $faqView = null;
    var $session = null;
    
    var $categories = null;
    var $questions = null;
    var $question_count = null;
    
        /**
     * [[機能説明]]
     *
     * @access  public
     */
    function execute() {

    	//カテゴリリストを取得する
        $this->categories = $this->faqView->getCatByFaqId($this->faq_id);
        if($this->categories === false) {
            return 'error';
        }
    	//質問リストを取得する
        $this->questions = $this->faqView->getQuestionList($this->faq_id, $this->category_id, null, null);
        if($this->questions === false) {
            return 'error';
        }
        /**
        foreach($this->questions as &$row) {
            $row['question_name'] = nl2br($row['question_name']);
        }
        */
        $this->question_count = count($this->questions);
    	return "success";
    }
}
?>