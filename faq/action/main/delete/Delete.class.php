<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * [[機能説明]]
 *
 * @package     NetCommons
 * @author      Ka
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_Action_Main_Delete extends Action
{
    // リクエストパラメータを受け取るため
    var $block_id = null;
    var $question_id = null;

    // 使用コンポーネントを受け取るため
    var $db = null;
 	var $request = null;
 	var $session = null;


    /**
     * [[機能説明]]
     *
     * @access  public
     */
    function execute()
    {
		$result = $this->db->deleteExecute("faq_question", array("question_id"=>$this->question_id));
		if($result === false) {
			return 'error';
		}
		$this->request->setParameter("question_id", null);
        return 'main';
    }
}
?>
