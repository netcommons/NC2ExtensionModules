<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * カテゴリ追加処理
 *
 * @package     NetCommons
 * @author      Ka
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_Action_Edit_Categoryadd extends Action
{
    // パラメータを受け取るため
    var $block_id = null;
	var $faq_id = null;
	var $category_name = null;
    
	// コンポーネントを受け取るため
	var $db = null;
	
    /**
     * Todo追加処理
     *
     * @access  public
     */
    function execute()
    {
    	$display_sequence = $this->db->maxExecute("faq_category", "display_sequence", array("faq_id"=>intval($this->faq_id)));
    	if($display_sequence === false) {
    		return 'error';
    	}
    	$params = array(
			"faq_id" => intval($this->faq_id),
			"category_name" => $this->category_name,
			"display_sequence" => $display_sequence+1
			
		);
    	$category_id = $this->db->insertExecute("faq_category", $params, true, "category_id");
    	if($category_id === false) {
    		return 'error';
    	}
    	
    	return 'success';
    }
    
}
?>
