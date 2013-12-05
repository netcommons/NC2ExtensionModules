<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画配信参照権限チェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Validator_MultimediaView extends Validator
{
    /**
     * 動画配信参照権限チェックバリデータ
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
		
		$multimediaView =& $container->getComponent("multimediaView");

    	if ($auth_id < _AUTH_CHIEF) {
			$multimedia_id = $multimediaView->getCurrentMultimediaId();
			if ($multimedia_id != $attributes['multimedia_id']) {
				return $errStr;
			}
		}

        $actionChain =& $container->getComponent("ActionChain");
		$actionName = $actionChain->getCurActionName();
		if (empty($attributes["multimedia_id"])) {
			$multimedia_obj = $multimediaView->getDefaultMultimedia();
		} else {
			$multimedia_obj = $multimediaView->getCurrentMultimedia();
		}

		if (empty($multimedia_obj)) {
        	return $errStr;
        }

		$request =& $container->getComponent("Request");
		$request->setParameter("multimedia_obj", $multimedia_obj);

        return;
    }
}
?>