<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画の再生チェック
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Validator_PlayCheck extends Validator
{
    /**
     * validate実行
     *
     * @param   mixed   $attributes チェックする値
     *                  
     * @param   string  $errStr     エラー文字列(未使用：エラーメッセージ固定)
     * @param   array   $params     オプション引数
     * @return  string  エラー文字列(エラーの場合)
     * @access  public
     */
    function validate($attributes, $errStr, $params)
    {
    	// container取得
		$container =& DIContainerFactory::getContainer();
		$filterChain =& $container->getComponent("FilterChain");
		$smartyAssign =& $filterChain->getFilterByName("SmartyAssign");
		if(empty($attributes)) {
			return $smartyAssign->getLang("_invalid_input");
		}
		
		$db =& $container->getComponent("DbObject");
		$request =& $container->getComponent("Request");
		
		$params = array("upload_id" => intval($attributes));
		$item = $db->selectExecute("multimedia_item", $params);
		
		if($item === false || !isset($item[0])) {
			return $smartyAssign->getLang("multimedia_err_no_item");
		}
		
		if($item[0]['privacy'] > _AUTH_GUEST) {
			$session =& $container->getComponent("Session");
			$user_id = $session->getParameter("_user_id");
			if(empty($user_id)) {
				exit();
				//return $smartyAssign->getLang("multimedia_err_incorrectauth");
			}
		}
		
		$request->setParameter("item", $item[0]);
    	return;
    }
}
?>