<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画配信表示アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_View_Main_Item_Detail extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	var $item_id = null;
	
	// バリデートによりセット
    var $item = null;
    var $multimedia_obj = null;
    
    // 使用コンポーネントを受け取るため
    var $db = null;
    var $actionChain = null;
    var $multimediaView = null;
    
    // validatorから受け取るため
	
	// 値をセットするため
	var $dialog_name = null;
	var $play_action = null;
	var $xml_action = null;
	var $commentCount = null;
    var $embed_code = null;
    var $item_list = null;
    var $tags = null;
	var $channel = null;
    
    function execute()
    {	
		$this->dialog_name = $this->item['item_name'];
    	$params = array(
    		$this->item_id
    	);
		$sql = "UPDATE {multimedia_item} ".
				"SET item_play_count = item_play_count + 1 ".
				"WHERE item_id = ? ";
		$result = $this->db->execute($sql, $params);
		if ($result === false) {
			return 'error';
		}
		
    	$this->commentCount = $this->multimediaView->getCommentCount($this->item_id);
    	if ($this->commentCount === false) {
			return 'error';
		}
		
		$action_name = $this->actionChain->getCurActionName();
		$pathList = explode("_", $action_name);
    	$dirname = $pathList[0];
    	$this->play_action = $dirname."_view_main_play";
    	$this->xml_action = $dirname."_view_main_xml";
    	$this->embed_code = '<embed src="'.BASE_URL.INDEX_FILE_NAME.'?action='.$this->play_action.'" type="application/x-shockwave-flash" width="400" height="300" '
							.'allowscriptaccess="always" allowfullscreen="true" '
							.'flashvars="file='.BASE_URL.INDEX_FILE_NAME.'?action='.$this->xml_action.'&id='.$this->item_id.'">'
							.'</embed>';
		$this->item_list = $this->multimediaView->getSimilarItems($this->item_id);
    	if ($this->item_list === false) {
			return 'error';
		}
		
    	$this->tags = $this->multimediaView->getTags($this->item_id);
    	if($this->tags === false) {
    		return 'error';
    	}

		$channel = $this->db->selectExecute("multimedia_album", array('album_id' => $this->item['album_id']));
		if($this->tags === false || !isset($channel[0])) {
    		return 'error';
    	}
		$this->channel = $channel[0];
		
		return 'success';
    }
}
?>