<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 新規作成画面表示
 *
 * @package     NetCommons
 * @author      Ka
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_View_Edit_Create extends Action
{
    // リクエストパラメータを受け取るため
    var $room_id = null;
    var $module_id = null;
    
    // バリデートによりセット
	var $faq_obj = null;

    // 使用コンポーネントを受け取るため
	var $db = null;

    // 値をセットするため

    /**
     * execute処理
     *
     * @access  public
     */
    function execute()
    {
    	$count = $this->db->countExecute("faq", array("room_id"=>$this->room_id));
    	$faq_name = FAQ_NEW_TITLE.($count+1);
    	$this->faq_obj['faq_name'] = $faq_name;

    	return 'success';
    }
}
?>