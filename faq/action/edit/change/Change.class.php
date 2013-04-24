<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ブッロク編集アクション
 *
 * @package     NetCommons
 * @author      Ka
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_Action_Edit_Change extends Action
{
    // リクエストパラメータを受け取るため
    var $block_id = null;
    var $faq_id = null;
    
	// コンポーネントを受け取るため
	var $db = null;
	
    function execute()
    {
    	$params = array(
			"block_id"=>intval($this->block_id)
		);

    	$blocks_faq = $this->db->selectExecute("faq_block", $params);
    	if($blocks_faq === false) {
    		return 'error';
    	}

    	if(isset($blocks_faq[0])) {
    		$update_params = array(
    			"faq_id"=>intval($this->faq_id)
    		);
	    	$result = $this->db->updateExecute("faq_block", $update_params, $params, true);
	    	if($result === false) {
	    		return 'error';
	    	}
    	} else {
    		$params = array(
    			"block_id" => $this->block_id,
				"faq_id" => $this->faq_id
			);
	    	$result = $this->db->insertExecute("faq_block", $params, true);
	    	if ($result === false) {
    			return 'error';
    		}
    	}
    	return 'success';
    }
}
?>
