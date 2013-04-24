<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画配信存在チェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Validator_MultimediaExist extends Validator
{
    /**
     * 動画配信存在チェックバリデータ
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
		$multimediaView =& $container->getComponent("multimediaView");
		$request =& $container->getComponent('Request');
		
		if (empty($attributes['multimedia_id'])) {
			$attributes['multimedia_id'] =& $multimediaView->getCurrentMultimediaId();
			$request->setParameter('multimedia_id', $attributes['multimedia_id']);
		}

		if (empty($attributes['multimedia_id'])) {
        	$attributes['multimedia_id'] =& $multimediaView->getFirstMultimediaId();
        	$request->setParameter("multimedia_id", $attributes['multimedia_id']);
		}

		if (empty($attributes['multimedia_id']) && strpos($actionName, 'multimedia_view_edit') === 0) {
			return;
		}
		
    	if (empty($attributes['multimedia_id']) && $actionName != 'multimedia_action_edit_initialize') {
			return $errStr;
		}

		if (empty($attributes['block_id'])) {
        	$block =& $multimediaView->getBlock();
			if ($attributes['room_id'] != $block['room_id']) {
				return $errStr;
			}

			$attributes['block_id'] = $block['block_id'];
        	$request->setParameter("block_id", $attributes['block_id']);
		}
		
    	if ($actionName == 'multimedia_action_edit_initialize') {
			return;
		}
		
        if (!$multimediaView->multimediaExist()) {
			return $errStr;
		}

        return;
    }
}
?>