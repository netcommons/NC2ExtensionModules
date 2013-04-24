<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * アルバム番号チェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Validator_AlbumSequence extends Validator
{
    /**
     * アルバム番号チェックバリデータ
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
        $db =& $container->getComponent("DbObject");
        
        $params = array(
			$attributes['drag_album_id'],
			$attributes['drop_album_id'],
			$attributes['multimedia_id']
		);
		
		$sql = "SELECT album_id, album_sequence ".
				"FROM {multimedia_album} ".
				"WHERE (album_id = ? ".
				"OR album_id = ?) ".
				"AND multimedia_id = ?";
		$result = $db->execute($sql, $params);
		if ($result === false || count($result) != 2) {
			return $errStr;
		}
		
		$sequences[$result[0]["album_id"]] = $result[0]["album_sequence"];
		$sequences[$result[1]["album_id"]] = $result[1]["album_sequence"];
        
		if (!$sequences) {
			return $errStr;	
		}
		
		$drag_album_id = $attributes["drag_album_id"];
		$drop_album_id = $attributes["drop_album_id"];

		if ($attributes["position"] == "top") {
			$sequences[$drop_album_id]--;
		}
		
		$request =& $container->getComponent("Request");
		$request->setParameter("drag_sequence", $sequences[$drag_album_id]);
		$request->setParameter("drop_sequence", $sequences[$drop_album_id]);
		
        return;
    }
}
?>