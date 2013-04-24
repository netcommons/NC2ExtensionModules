<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画削除アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Action_Main_Item_Delete extends Action
{
	// リクエストパラメータを受け取るため
	var $item_id = null;
	
	// 使用コンポーネントを受け取るため
	var $multimediaAction = null;

    /**
     * 動画削除アクション
     *
     * @access  public
     */
    function execute()
    {	
    	if (!$this->multimediaAction->deleteItem($this->item_id)) {
        	return 'error';
        }

        return 'success';
    }
}
?>