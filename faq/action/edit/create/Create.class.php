<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 新規作成
 *
 * @package     NetCommons
 * @author      Ka
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_Action_Edit_Create extends Action
{
    // リクエストパラメータを受け取るため
    var $block_id = null;
	var $module_id = null;
	var $faq_id = null;
	
	// バリデートによりセット
	var $faq_obj = null;

    // 使用コンポーネントを受け取るため
	var $db = null;
	var $configView = null;
	var $request = null;

	// 値をセットするため

    /**
     * execute処理
     *
     * @access  public
     */
    function execute()
    {
    	$params = array(
			"faq_name" => $this->request->getParameter("faq_name"),
			"faq_authority" => intval($this->request->getParameter("faq_authority"))
		);
		
		if(empty($this->faq_id)) {
			//$params = array_merge($params, array("active_flag" => $this->journal_obj['active_flag']));
			$faq_id = $this->db->insertExecute("faq", $params, true, "faq_id");
			if ($faq_id === false) {
	    		return 'error';
	    	}
			$default_categoris_array = explode("|", FAQ_DEFAULT_CATEGORIES);
	    	$display_seq = 0;
	    	foreach(array_keys($default_categoris_array) as $i) {
	    		$display_seq++;
	    		$params = array(
					"faq_id" => $faq_id,
					"category_name" => $default_categoris_array[$i],
					"display_sequence" => $display_seq
				);
				$result = $this->db->insertExecute("faq_category", $params, true, "category_id");
	    	}
	    	$count = $this->db->countExecute("faq_block", array("block_id"=>$this->block_id));
	    	if($count === false) {
	    		return 'error';
	    	}
	    	
			if ($count == 0) {
				$params = array(
					"faq_id" => $faq_id,
					"display_row" => FAQ_DEFAULT_VISIBLE_ITEM
				);
		    	$result = $this->db->insertExecute("faq_block", array_merge(array("block_id" => $this->block_id), $params), true);
			}else {
				$params = array(
					"faq_id" => $faq_id,
				    "display_category"=>0
				);
		    	$result = $this->db->updateExecute("faq_block", $params,  array("block_id"=>$this->block_id), true);
	    	}
	    	if ($result === false) {
	    		return 'error';
	    	}
	    	
	    	return 'style';
		}else {
			$where_params = array("faq_id" => $this->faq_id);
			$result = $this->db->updateExecute("faq", $params, $where_params);
			if ($result === false) {
	    		return 'error';
	    	}
	    	
	    	return 'list';
		}
    }
}
?>