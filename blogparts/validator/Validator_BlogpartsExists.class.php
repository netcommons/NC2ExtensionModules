<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Blogparts存在チェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Blogparts_Validator_BlogpartsExists extends Validator
{
    /**
     * Blogparts存在チェックバリデータ
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

        $blogpartsView =& $container->getComponent("blogpartsView");
		$request =& $container->getComponent("Request");
		//parts_idの存在チェックをしてなかった場合はカレントのparts_idを入れる
		if (empty($attributes["parts_id"])) {
			$attributes["parts_id"] = $blogpartsView->getCurrentBlogpartsID();
        	$request->setParameter("parts_id", $attributes["parts_id"]);
		}
		//parts_idの存在チェック
		if (empty($attributes["parts_id"])) {
			return $errStr;
		}
		//block_idの存在チェック
		if (empty($attributes["block_id"])) {
        	return $errStr;
		}
		
        if (!$blogpartsView->blogpartsExists()) {
			return $errStr;
		}
        return;
    }
}
?>