<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * BLOGPARTS一覧画面表示
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Blogparts_View_Edit_List extends Action
{
	// パラメータを受け取るため
	var $module_id = null;
	var $block_id = null;
	var $scroll = null;

	// 使用コンポーネントを受け取るため
	var $blogpartsView = null;
	var $configView = null;
	var $request = null;
	var $filterChain = null;

	// validatorから受け取るため
	var $blogpartsCount = null;

	// 値をセットするため
	var $visibleRows = null;
	var $blogpartsList = null;
	var $currentBlogpartsID = null;
	
    /**
     * Blogparts一覧画面表示アクション
     *
     * @access  public
     */
	function execute()
	{
    	//scrollが_ON以外の場合Configテーブルからconf_valueの値を取り出しlimitとしてViewフィルタに格納する
        if ($this->scroll != _ON) {
        	//DBのConfigテーブルからinstall.iniで登録したblogparts_list_row_countの行を取り出す。
			$config = $this->configView->getConfigByConfname($this->module_id, "blogparts_list_row_count");
			if ($config === false) {
	        	return "error";
	        }
	        //その行からvisibleRowsと言う変数にconf_valueの値を取り出す。
	        $this->visibleRows = $config["conf_value"];
	        $this->request->setParameter("limit", $this->visibleRows);
	        $this->currentBlogpartsID = $this->blogpartsView->getCurrentBlogpartsID();
	        if ($this->currentBlogpartsID === false) {
	        	return "error";
	        }
		}
		//ViewのgetBlogpartsListメソッドでDBからパーツのリストを取得する
		$this->blogpartsList = $this->blogpartsView->getBlogpartsList();
        if (empty($this->blogpartsList)) {
        	return "error";
        }
        //scrollが_ONの時Viewフィルタのdefine:themeを0(テーマなし)に設定する。
        if ($this->scroll == _ON) {
			$view =& $this->filterChain->getFilterByName("View");
			$view->setAttribute("define:theme", 0);
        	
        	return "scroll";
        }
        
        return "screen";
    }
}
?>
