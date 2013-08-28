<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画配信アルバム登録処理
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Action_Main_Album_Entry extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	var $multimedia_id = null;
	var $album_id = null;
	var $album_name = null;
	var $album_description = null;
	var $album_jacket = null;
	var $upload_id = null;
	var $public_flag = null;
	
	// バリデートによりセット
    var $multimedia_obj = null;
	
	// コンポーネントを受け取るため
	var $db = null;
	var $multimediaView = null;

	function execute()
	{
		if (empty($this->upload_id)) {
			$imageSize = $this->multimediaView->getImageSize($this->album_jacket);
		} else {
			$imageSize = $this->multimediaView->getImageSize($this->upload_id);
		}
		
		$params = array(
			"album_name" => $this->album_name,
			"upload_id" => $this->upload_id,
			"album_jacket" => $this->album_jacket,
			"width" => $imageSize[0],
			"height" => $imageSize[1],
			"album_description" => $this->album_description,
			"public_flag" => intval($this->public_flag)
		);

		if (empty($this->album_id)) {
			$params["multimedia_id"] =  $this->multimedia_id;
			$albumSequence = $this->db->maxExecute("multimedia_album", "album_sequence", array("multimedia_id" => $this->multimedia_id));
			$params["album_sequence"] = $albumSequence + 1;
			$result = $this->db->insertExecute("multimedia_album", $params, true, "album_id");
		} else {
			$result = $this->db->updateExecute("multimedia_album", $params, array("album_id" => $this->album_id), true);
		}
		if ($result === false) {
			return 'error';
		}
		
		return 'success';
	}
}
?>
