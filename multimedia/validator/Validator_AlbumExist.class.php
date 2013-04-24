<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画アルバム存在チェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Validator_AlbumExist extends Validator
{
    /**
     * 動画アルバム存在チェックバリデータ
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
    	$multimediaView =& $container->getComponent("multimediaView");
    	
    	$album = $multimediaView->getAlbum($attributes);
    	if(empty($album)) {
    		return $errStr;
    	}
    	
    	$request =& $container->getComponent("Request");
    	$request->setParameter("album", $album);

        return;
    }
}
?>