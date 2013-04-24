<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 質問表示方法変更画面表示
 *
 * @package     NetCommons
 * @author      Ka
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_View_Edit_Style extends Action
{
    // リクエストパラメータを受け取るため
    var $block_id = null;
    var $room_id = null;
	
	// コンポーネントを受け取るため
	var $db = null;
    var $faqView = null;
	
	// 値をセットするため
	var $faq_obj = null;
	var $categories = null;
	
    /**
     * Faq編集画面表示
     *
     * @access  public
     */
    function execute()
    {
    	$params = array(
			"block_id"=>intval($this->block_id)
		);
		//データがなければ
		$faq = $this->db->selectExecute("faq_block", $params);
    	if($faq === false) {
    		return 'error';
    	}
    	$this->faq_obj = $faq[0];
    	
    	$this->categories = $this->faqView->getCatByFaqId($this->faq_obj['faq_id']);
    	
		return 'success';
    }
}
?>
