<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Blogparts表示チェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Blogparts_Validator_BlogpartsView extends Validator
{
    /**
     * Blogparts表示チェックバリデータ
     *
     * @param   mixed   $attributes チェックする値
     * @param   string  $errStr     エラー文字列
     * @param   array   $params     オプション引数
     * @return  string  エラー文字列(エラーの場合)
     * @access  public
     */
    function validate($attributes, $errStr, $params)
    {
		$container =& DIContainerFactory::getContainer();
		
		$actionChain =& $container->getComponent("ActionChain");
		$actionName = $actionChain->getCurActionName();
		
		$blogpartsView =& $container->getComponent("blogpartsView");
		
		if($actionName == "blogparts_view_main_init"){
			$parts_data = $blogpartsView->getCurPartsData();
		}else{
			$parts_data = $blogpartsView->getPartsData();
		}
		if(empty($parts_data)||empty($parts_data[0])){
			return $errStr;
		}
		$request =& $container->getComponent("Request");
		$request->setParameter("parts_data", $parts_data[0]);

        return;
    }
}
?>