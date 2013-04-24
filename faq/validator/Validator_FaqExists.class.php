<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 質問存在チェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Ka
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_Validator_FaqExists extends Validator
{
    /**
     * 質問存在チェックバリデータ
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
		if (empty($attributes["faq_id"]) &&
				($actionName == "faq_view_edit_create" ||
					$actionName == "faq_action_edit_create")) {
			return;
		}

        $faqView =& $container->getComponent("faqView");
		$request =& $container->getComponent("Request");
		if (empty($attributes["faq_id"])) {
        	$attributes["faq_id"] = $faqView->getCurrentFaqId();
        	$request->setParameter("faq_id", $attributes["faq_id"]);
		}

		if (empty($attributes["faq_id"])) {
			return $errStr;
		}

		if (empty($attributes["block_id"])) {
        	$block = $faqView->getBlock();
			if ($attributes["room_id"] != $block["room_id"]) {
				return $errStr;
			}

			$attributes["block_id"] = $block["block_id"];
        	$request->setParameter("block_id", $attributes["block_id"]);
		}
		
        if (!$faqView->faqExists()) {
			return $errStr;
		}
		
        return;
    }
}
?>