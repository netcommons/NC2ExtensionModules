<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画チャンネルジャケット表示アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_View_Main_Album_Jacket extends Action
{
	// リクエストパラメータを受け取るため
	var $upload_id = null;
	var $album_jacket = null;
	var $multimedia_id = null;
	var $album_id = null;

	// 使用コンポーネントを受け取るため
    var $session = null;
    var $multimediaView = null;
    var $db = null;
    
    // validatorから受け取るため
    var $album_obj = null;
    
    function execute()
    {
		if (!empty($this->upload_id)
				&& $this->upload_id != $this->session->getParameter("multimedia_jacket_upload_id")) {
			return "error";
		}
		
		$album = $this->db->selectExecute("multimedia_album", array("album_id"=>$this->album_id));
		if (!empty($album) || isset($album[0])) {
			$this->album_obj = $album[0];
		}
		
		if (empty($this->upload_id)) {
			$imageSize = $this->multimediaView->getImageSize($this->album_jacket);
		} else {
			$imageSize = $this->multimediaView->getImageSize($this->upload_id);
			$this->album_obj["upload_id"] = $this->upload_id;
		}
    	
		$this->album_obj["album_jacket"] = $this->album_jacket;
		$this->album_obj["jacket_style"] = $this->multimediaView->getImageStyle($imageSize[0], $imageSize[1], MULTIMEDIA_JACKET_WIDTH, MULTIMEDIA_JACKET_HEIGHT);

		return "success";
    }
}
?>
