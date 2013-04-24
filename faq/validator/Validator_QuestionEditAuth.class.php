<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * [[機能説明]]
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_Validator_QuestionEditAuth extends Validator
{
    /**
     * [[機能説明]]
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
    	$request =& $container->getComponent("Request");
    	
    	$question_id = $attributes["question_id"];
    	if(empty($question_id)) {
    		$session =& $container->getComponent("Session");
    		$auth_id = $session->getParameter("_auth_id");
    		$faq_obj = $request->getParameter("faq_obj");
    		if(empty($faq_obj) || $auth_id < $faq_obj['faq_authority']) {
    			return $errStr;
    		}
    		return;
    	}
		
		$faqView =& $container->getComponent("faqView");

		$result = $faqView->getQuestion($question_id);
        if (empty($result)) {
	       	return $errStr;
        }

        
        $request->setParameter("question", $result[0]);

        if(!$result[0]['has_edit_auth']) {
        	return $errStr;
        }

        return;
    }
}
?>
