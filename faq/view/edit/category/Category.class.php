<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * カテゴリ表示
 *
 * @package     NetCommons
 * @author      Ka
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_View_Edit_Category extends Action
{
    // リクエストパラメータを受け取るため
    var $block_id = null;
    var $room_id = null;
    var $faq_id = null;
    
    // バリデートによりセット
	var $faq_obj = null;
	
	// コンポーネントを受け取るため
	var $db = null;
	
	// 値をセットするため
	var $category_count = null;
	var $categories = null;
	
    /**
     * Category編集画面表示
     *
     * @access  public
     */
    function execute()
    {
    	$params = array(
			"faq_id"=>intval($this->faq_id)
		);
		$this->category_count = $this->db->countExecute("faq_category", $params);
        if ($this->category_count === false) {
        	return 'error';
        }
        
        $order_params = array(
        	"display_sequence" => "ASC"
        );
		$this->categories = $this->db->selectExecute("faq_category", $params, $order_params);
    	if($this->categories === false) {
    		return 'error';
    	}
		return 'success';
    }
}
?>
