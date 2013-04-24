<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 汎用データベース照権限チェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_Validator_FaqView extends Validator
{
    /**
     * 汎用データベース参照権限チェックバリデータ
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

		$session =& $container->getComponent("Session");
		$auth_id = $session->getParameter("_auth_id");
		
		$request =& $container->getComponent("Request");
		$prefix_id_name = $request->getParameter("prefix_id_name");
		
		if ($auth_id < _AUTH_CHIEF &&
				$prefix_id_name == FAQ_REFERENCE_PREFIX_NAME.$attributes['faq_id']) {
			return $errStr;
		}
		
        $actionChain =& $container->getComponent("ActionChain");
		$actionName = $actionChain->getCurActionName();

        $faqView =& $container->getComponent("faqView");
		if (empty($attributes['faq_id'])) {
			$faq_obj = $this->getDefaultFaq();
		} elseif ($prefix_id_name == FAQ_REFERENCE_PREFIX_NAME.$attributes['faq_id'] 
					|| $actionName == "faq_view_edit_modify" || $actionName == "faq_view_edit_category"
					|| $actionName == "faq_view_edit_modify") {

			$faq_obj = $faqView->getFaq();
		} else {

			$faq_obj = $faqView->getCurrentFaq();
		}

		if (empty($faq_obj)) {
        	return $errStr;
        }

		$request =& $container->getComponent("Request");
		$request->setParameter("faq_obj", $faq_obj);
 
        return;
    }
    
    /*
     * 仮方法
     */
    function getDefaultFaq() {
    	$container =& DIContainerFactory::getContainer();
        $configView =& $container->getComponent("configView");
        $request =& $container->getComponent("Request");
        $module_id = $request->getParameter("module_id");
        $config = $configView->getConfig($module_id, false);
        if ($config === false) {
            return $config;
        }

        $faq = array(
            "display_row" => $config["display_row"]["conf_value"],
            "faq_authority" => constant($config["faq_authority"]["conf_value"])
        );
    	return $faq;
    }
}
?>
