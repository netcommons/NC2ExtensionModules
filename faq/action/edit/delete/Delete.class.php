<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 質問の削除から呼ばれるアクション
 *
 * @package     NetCommons
 * @author      Ka
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_Action_Edit_Delete extends Action
{
    // パラメータを受け取るため
    var $faq_id = null;

    // 使用コンポーネントを受け取るため
    var $faqAction = null;

    /**
     * 日誌の削除から呼ばれるアクション
     * @return boolean
     * @access  public
     */
    function execute()
    {
        $result = $this->faqAction->delFaq($this->faq_id);
    	if($result === false) {
    		return 'error';
    	}
    	return 'success';
    }
}
?>
