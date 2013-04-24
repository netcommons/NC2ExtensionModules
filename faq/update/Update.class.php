<?php
/**
 * モジュールアップデートクラス
 *
 * @package     NetCommons.components
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_Update extends Action
{
	//使用コンポーネントを受け取るため
	var $db = null;

	function execute()
	{
		// faqにindexを追加
		$sql = "SHOW INDEX FROM `".$this->db->getPrefix()."faq` ;";
		$results = $this->db->execute($sql);
		if($results === false) return false;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."faq` ADD INDEX ( `room_id`  ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}

		// faq_blockにindexを追加
		$sql = "SHOW INDEX FROM `".$this->db->getPrefix()."faq_block` ;";
		$results = $this->db->execute($sql);
		if($results === false) return false;
		$alter_table_faq_id_flag = true;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "faq_id") {
				$alter_table_faq_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if($alter_table_faq_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."faq_block` ADD INDEX ( `faq_id` ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."faq_block` ADD INDEX ( `room_id`  ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}

		// faq_categoryにindexを追加
		$sql = "SHOW INDEX FROM `".$this->db->getPrefix()."faq_category` ;";
		$results = $this->db->execute($sql);
		if($results === false) return false;
		$alter_table_faq_id_flag = true;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "faq_id") {
				$alter_table_faq_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if($alter_table_faq_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."faq_category` ADD INDEX ( `faq_id`, `display_sequence` ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."faq_category` ADD INDEX ( `room_id`  ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}

		// faq_questionにindexを追加
		$sql = "SHOW INDEX FROM `".$this->db->getPrefix()."faq_question` ;";
		$results = $this->db->execute($sql);
		if($results === false) return false;
		$alter_table_faq_id_flag = true;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "faq_id") {
				$alter_table_faq_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if($alter_table_faq_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."faq_question` ADD INDEX ( `faq_id`, `category_id` ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."faq_question` ADD INDEX ( `room_id`  ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}

		// 件数表示で全てを選択されている場合、100件にする
		$sql = "UPDATE `".$this->db->getPrefix()."faq_block` SET display_row = 100 WHERE display_row = 0";
		$result = $this->db->execute($sql);
		if($result === false) return false;

		return true;
	}
}
?>
