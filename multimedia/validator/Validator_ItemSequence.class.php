<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画番号チェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Validator_ItemSequence extends Validator
{
    /**
     * 動画番号チェックバリデータ
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
			$attributes['drag_item_id'],
			$attributes['drop_item_id'],
			$attributes['album_id']
		);
		
		$sql = "SELECT item_id, item_sequence ".
				"FROM {multimedia_item} ".
				"WHERE (item_id = ? ".
				"OR item_id = ?) ".
				"AND album_id = ?";
		$result = $db->execute($sql, $params);
		if ($result === false || count($result) != 2) {
			return $errStr;
		}
		
		$sequences[$result[0]['item_id']] = $result[0]['item_sequence'];
		$sequences[$result[1]['item_id']] = $result[1]['item_sequence'];

		if ($attributes["position"] == "left") {
			$sequences[$attributes['drop_item_id']]--;
		}
		
		$request =& $container->getComponent("Request");
		$request->setParameter("drag_sequence", $sequences[$attributes['drag_item_id']]);
		$request->setParameter("drop_sequence", $sequences[$attributes['drop_item_id']]);
		
        return;
    }
}
?>