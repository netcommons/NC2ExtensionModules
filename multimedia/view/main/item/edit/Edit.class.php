<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画編集画面アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_View_Main_Item_Edit extends Action
{
	// リクエストパラメータを受け取るため
	
	// バリデートによりセット
    var $item = null;
    
    // 使用コンポーネントを受け取るため
    var $filterChain = null;
    var $multimediaView = null;

    // 値をセットするため
	var $dialog_name = null;
	var $album_list = null;
	var $item_tag = null;
	
    /**
     * 写真登録画面アクションクラス
     *
     * @access  public
     */
    function execute()
    {
    	$smartyAssign =& $this->filterChain->getFilterByName("SmartyAssign");
		$this->dialog_name = sprintf($smartyAssign->getLang("multimedia_item_edit_title"), $this->item["item_name"]);

    	$order_params = array(
			"album_sequence" => "ASC"
		);
		$this->album_list = $this->multimediaView->getAlbumList($this->item['multimedia_id'], $order_params);
    	if($this->album_list === false) {
    		return 'error';
    	}
		return 'success';
    }
}
?>