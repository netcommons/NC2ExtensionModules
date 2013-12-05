<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * プレーヤーリストを取得
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_View_Main_Xml extends Action
{
	// リクエストパラメータを受け取るため
	
	// バリデートによりセット
	var $item = null;
	var $list = null;
	
	// 使用コンポーネントを受け取るため
	
	// 値をセットするため

	/**
     * プレーヤーリストを取得
     *
     * @access  public
     */
	function execute()
	{
		if ((isset($_SERVER['HTTPS']) || strncasecmp(BASE_URL, "https", 5) === 0) && stristr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
			header('Pragma: cache');
        	$offset = 1; //キャッシュは一分です。
        	header('Expires: '.gmdate('D, d M Y H:i:s', time() +  $offset).' GMT');
        	header("Content-Type: text/xml");
		}
		return 'success';
	}
}
?>