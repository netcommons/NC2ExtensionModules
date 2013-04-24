<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画再生アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_View_Main_Play extends Action
{
    function execute()
    {
		if ((isset($_SERVER['HTTPS']) || strncasecmp(BASE_URL, "https", 5) === 0) && stristr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
    		header("Content-disposition: inline; filename=\"player.swf\"");
			header("Cache-Control: max-age=604800, public");
        	header('Pragma: cache');
        	$offset = 60 * 60 * 24 * 7; 
        	//  1Week
        	header('Expires: '.gmdate('D, d M Y H:i:s', time() +  $offset).' GMT');
    		header("Content-Type: application/x-shockwave-flash");
		}
    	readfile(HTDOCS_DIR.MULTIMEDIA_MOVIE_PLAYER);
		exit;
    }
}
?>