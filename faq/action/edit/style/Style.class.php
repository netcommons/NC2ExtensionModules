<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 表示方法変更アクション
 *
 * @package     NetCommons
 * @author      Ka
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_Action_Edit_Style extends Action
{
    // リクエストパラメータを受け取るため
    var $block_id = null;
    var $display_row = null;
    var $display_category = null;
    var $show_cate_list = null;
    
	// コンポーネントを受け取るため
	var $db = null;
	
	// 値をセットするため
	var $faq = null;
	
    /**
     * 表示方法変更アクション
     *
     * @access  public
     */
    function execute()
    {
    	$params = array(
			"block_id"=>intval($this->block_id)
		);
		//データがなければ、update
    	$this->faq = $this->db->selectExecute("faq_block", $params);
    	if($this->faq === false) {
    		return 'error';
    	}
    	if(isset($this->faq[0])) {
    		$update_params = array(
    			"display_row"  => $this->display_row,
    		    "display_category" => $this->getDisplayCategory()
    		);
    		
	    	$result = $this->db->updateExecute("faq_block", $update_params, $params, true);
	    	if($result === false) {
	    		return 'error';
	    	}
    	}

    	return 'success';
    }
    
    /**
     * display_category値を取得する
     * @return int display_type
     */
    function getDisplayCategory() {
        return intval($this->show_cate_list)==1?0:intval($this->display_category);
    }
    
}
?>
