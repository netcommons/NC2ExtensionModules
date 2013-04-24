<?php

class Multimedia_Action_Main_Album_Jacket extends Action 
{
    var $uploadsAction = null;
    var $session = null;
	
	/**
     * ジャケットアップロードアクション
     *
     * @access  public
     */
    function execute()
    {
    	$files = $this->uploadsAction->uploads();
		$this->session->setParameter("multimedia_jacket_upload_id", $files[0]["upload_id"]);
    	
    	return true;
    }
}
?>