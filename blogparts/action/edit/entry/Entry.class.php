<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Blogparts追加アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Blogparts_Action_Edit_Entry extends Action
{
    // 使用コンポーネントを受け取るため
    var $blogpartsAction = null;
    /**
     * Blogparts追加アクション
     *
     * @access  public
     */
    function execute()
    {
    	$result = $this->blogpartsAction->setParts();
        if (false === $result) {
        	return "error";
        }else if("create" === $result) {
        	return "create";
        }
		return "success";
    }
}
?>
