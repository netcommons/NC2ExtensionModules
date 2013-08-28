<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画リストチェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Validator_MultimediaXml extends Validator
{
    /**
     * 動画リストチェックバリデータ
     *
     * @param   mixed   $attributes チェックする値
     * @param   string  $errStr     エラー文字列
     * @param   array   $params     オプション引数
     * @return  string  エラー文字列(エラーの場合)
     * @access  public
     */
    function validate($attributes, $errStr, $params)
    {
    	if(empty($attributes)) {
    		return $errStr;
    	}
    	
    	$container =& DIContainerFactory::getContainer();
    	$session =& $container->getComponent("Session");
    	$request =& $container->getComponent("Request");
    	$db =& $container->getComponent("DbObject");
        $multimediaView =& $container->getComponent("multimediaView");
        
        $item = $multimediaView->getItem($attributes);
        if(empty($item)) {
        	return $errStr;
        }
        
    	$item_list = $multimediaView->getSimilarItems($attributes);
    	if($item_list === false) {
    		return $errStr;
    	}
    	
    	$request->setParameter("item", $item);
    	$request->setParameter("list", $item_list);
        return;
    }
}
?>