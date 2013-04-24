<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 編集画面表示
 *
 * @package     NetCommons
 * @author      Ka
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_View_Edit_List extends Action
{
    // パラメータを受け取るため
    var $module_id = null;
	var $block_id = null;
	var $scroll = null;

    // 使用コンポーネントを受け取るため
    var $faqView = null;
    var $configView = null;
    var $request = null;
    var $session = null;
    var $filterChain = null;

	// validatorから受け取るため
	var $faq_count = null;

    // 値をセットするため
    var $display_row = null;
    var $faq_list = null;
	var $current_faq_id = null;

    /**
     * execute処理
     *
     * @access  public
     */
    function execute()
    {
    	if ($this->scroll != _ON) {
			$config = $this->configView->getConfigByConfname($this->module_id, "faq_list_row_count");
			if ($config === false) {
	        	return 'error';
	        }
	        
	        $this->display_row = $config["conf_value"];
	        $this->request->setParameter("limit", $this->display_row);
	        
	        $this->current_faq_id = $this->faqView->getCurrentFaqId();
	        if ($this->current_faq_id === false) {
	        	return 'error';
	        }
		}
		
		$this->faq_list = $this->faqView->getFaqs();
        if (empty($this->faq_list)) {
        	return 'error';
        }
        
        if ($this->scroll == _ON) {
			$view =& $this->filterChain->getFilterByName("View");
			$view->setAttribute("define:theme", 0);
        	
        	return 'scroll';
        }
        
        return 'screen';
    }
}
?>
