<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画再生
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Action_Main_Item_Play extends Action
{
    // リクエストパラメータを受け取るため
    
    // バリデートによりセット
    var $item = null;
    var $uploadsView = null;

 
    // 値をセットするため
    
    /**
     * [[機能説明]]
     *
     * @access  public
     */
    function execute()
    {
		list($pathname,$filename,$physical_file_name, $cache_flag) = $this->uploadsView->downloadCheck($this->item['upload_id'], null);
		if (!isset($pathname)) {
			exit;
		}

		clearstatcache();
		$caching = true;
		if ($this->item['privacy'] > _AUTH_GUEST) {
			$caching = false;
		}

		$this->uploadsView->headerOutput($pathname, $filename, $physical_file_name, $caching);
		exit;
    }
}
?>