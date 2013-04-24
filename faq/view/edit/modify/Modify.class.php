<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * キャビネット編集画面表示
 *
 * @package     NetCommons
 * @author      Ka
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_View_Edit_Modify extends Action
{
    // リクエストパラメータを受け取るため
    var $faq_id = null;

    // 使用コンポーネントを受け取るため
	var $db = null;

    // 値をセットするため
	var $faq_obj = null;

    /**
     * execute処理
     *
     * @access  public
     */
    function execute()
    {
		$result = $this->db->selectExecute("faq", array("faq_id"=>intval($this->faq_id)));
        if ($result === false) {
        	return 'error';
        }
        
        $this->faq_obj = $result[0];
   		return 'success';
    }
}
?>