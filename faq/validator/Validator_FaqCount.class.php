<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Todo件数チェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Ka
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_Validator_FaqCount extends Validator
{
    /**
     * 件数チェックバリデータ
     *
     * @param   mixed   $attributes チェックする値
     * @param   string  $errStr     エラー文字列
     * @param   array   $params     オプション引数
     * @return  string  エラー文字列(エラーの場合)
     * @access  public
     */
    function validate($attributes, $errStr, $params)
    {
		if ($attributes["scroll"] == _ON) {
			return;
		}

		$container =& DIContainerFactory::getContainer();
        $faqView =& $container->getComponent("faqView");
		$faq_count = $faqView->getFaqCount();
        if ($faq_count == 0) {
        	return $errStr;
        }

		$request =& $container->getComponent("Request");
		$request->setParameter("faq_count", $faq_count);
		
        return;
    }
}
?>