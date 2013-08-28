<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * コメント表示アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_View_Main_Comment extends Action
{
	// リクエストパラメータを受け取るため
	var $item_id = null;
	var $now_page = null;
	
	// バリデートによりセット
    var $item = null;
	
    // 使用コンポーネントを受け取るため
    var $multimediaView = null;
	
    // 値をセットするため
    var $commentCount = null;
    var $comments = null;
    
    //ページ
    var $pager = array();
    
    /**
     * コメント表示アクション
     *
     * @access  public
     */
    function execute()
    {	
		$this->commentCount = $this->multimediaView->getCommentCount($this->item_id);
    	if ($this->comments === false) {
			return 'error';
		}
        $this->multimediaView->setPageInfo($this->pager, $this->commentCount, MULTIMEDIA_VISIBLE_ITEM_COMMENT, $this->now_page);
    	$this->comments = $this->multimediaView->getComments($this->item_id, $this->pager['disp_begin']);
    	if ($this->comments === false) {
			return 'error';
		}
		
		return 'success';
    }
}
?>
