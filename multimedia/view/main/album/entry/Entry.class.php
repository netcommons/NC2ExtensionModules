<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画配信のチャンネルを作成
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_View_Main_Album_Entry extends Action 
{
	// リクエストパラメータを受け取るため
	var $module_id = null;
    var $album_id = null;
    
    // バリデートによりセット
    var $multimedia_obj = null;
    
    // コンポーネントを受け取るため
    var $multimediaView = null;
    var $fileView = null;
    var $configView = null;
    
     // 値をセットするため
    var $album_jacket = null;
    var $album_obj = null;
    var $albumJacketSamples = array();
    var $album_number = null;
    
    function execute() 
    {
    	$this->albumJacketSamples = $this->fileView->getCurrentFiles(HTDOCS_DIR.MULTIMEDIA_SAMPLR_JACKET_PATH.MULTIMEDIA_SAMPLR_JACKET_DIR);

    	if(empty($this->album_id)) {
    		$config = $this->configView->getConfig($this->module_id, false);
			if ($config === false) {
        		return $config;
       	 	}
       	 	
       	 	$this->album_obj["multimedia_id"] = $this->multimedia_obj['multimedia_id'];
    		$this->album_obj["album_jacket"] = $config["album_jacket"]["conf_value"];
    		$this->album_obj["public_flag"] = _ON;
    	}else {
	    	$this->album_obj = $this->multimediaView->getAlbum($this->album_id);
	    	if($this->album_obj === false) {
	    		return 'error';
	    	}
    	}
    	
    	$this->album_number = $this->multimediaView->getAlbumCount($this->multimedia_obj['multimedia_id']);
		if ($this->album_number === false) {
        	return 'error';
        }
        $this->album_number++;
    	return 'success';
    }
}
?>