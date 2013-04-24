<?php
/**
 * モジュールアップデートアクションクラス
 *
 * @package     NetCommons.components
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Update extends Action
{
	//使用コンポーネントを受け取るため
	var $dbObject = null;

	/**
	 * モジュールアップデートアクション
	 *
	 * @access  public
	 */
	function execute()
	{
		$sql = "SELECT DISTINCT "
					. "T.item_id, "
					. "I.room_id "
				. "FROM {multimedia_item_tag} T "
				. "INNER JOIN {multimedia_item} I "
				. "ON T.item_id = I.item_id "
				. "AND T.room_id != I.room_id";
		$items = $this->dbObject->execute($sql);
		if ($items === false) {
			return false;
		}

		foreach ($items as $item) {
			$sql = "UPDATE {multimedia_item_tag} SET "
					. "room_id = ? "
					. "WHERE item_id = ?";
			$inputs = array(
				$item['room_id'],
				$item['item_id']
			);
			$result = $this->dbObject->execute($sql, $inputs);
			if ($result === false) {
				return false;
			}
		}

		$sql = "SELECT DISTINCT "
					. "T.tag_id, "
					. "I.room_id "
				. "FROM {multimedia_tag} T "
				. "INNER JOIN {multimedia_item_tag} I "
				. "ON T.tag_id = I.tag_id "
				. "AND T.room_id != I.room_id";
		$items = $this->dbObject->execute($sql);
		if ($items === false) {
			return false;
		}

		foreach ($items as $item) {
			$sql = "UPDATE {multimedia_tag} SET "
					. "room_id = ? "
					. "WHERE tag_id = ?";
			$inputs = array(
				$item['room_id'],
				$item['tag_id']
			);
			$result = $this->dbObject->execute($sql, $inputs);
			if ($result === false) {
				return false;
			}
		}

		// multimediaにindexを追加
		$sql = "SHOW INDEX FROM `".$this->dbObject->getPrefix()."multimedia` ;";
		$results = $this->dbObject->execute($sql);
		if($results === false) return false;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."multimedia` ADD INDEX ( `room_id`  ) ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}

		// multimedia_albumにindexを追加
		$sql = "SHOW INDEX FROM `".$this->dbObject->getPrefix()."multimedia_album` ;";
		$results = $this->dbObject->execute($sql);
		if($results === false) return false;
		$alter_table_multimedia_id_flag = true;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "multimedia_id") {
				$alter_table_multimedia_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if($alter_table_multimedia_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."multimedia_album` ADD INDEX ( `multimedia_id` , `album_sequence` ) ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."multimedia_album` ADD INDEX ( `room_id`  ) ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}

		// multimedia_blockにindexを追加
		$sql = "SHOW INDEX FROM `".$this->dbObject->getPrefix()."multimedia_block` ;";
		$results = $this->dbObject->execute($sql);
		if($results === false) return false;
		$alter_table_multimedia_id_flag = true;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "multimedia_id") {
				$alter_table_multimedia_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if($alter_table_multimedia_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."multimedia_block` ADD INDEX ( `multimedia_id` ) ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."multimedia_block` ADD INDEX ( `room_id`  ) ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}

		// multimedia_commentにindexを追加
		$sql = "SHOW INDEX FROM `".$this->dbObject->getPrefix()."multimedia_comment` ;";
		$results = $this->dbObject->execute($sql);
		if($results === false) return false;
		$alter_table_item_id_flag = true;
		$alter_table_album_id_flag = true;
		$alter_table_multimedia_id_flag = true;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "item_id") {
				$alter_table_item_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "album_id") {
				$alter_table_album_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "multimedia_id") {
				$alter_table_multimedia_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if($alter_table_item_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."multimedia_comment` ADD INDEX ( `item_id` ) ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_album_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."multimedia_comment` ADD INDEX ( `album_id`  ) ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_multimedia_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."multimedia_comment` ADD INDEX ( `multimedia_id`, `room_id`  ) ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."multimedia_comment` ADD INDEX ( `room_id`  ) ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}

		// multimedia_itemにindexを追加
		$sql = "SHOW INDEX FROM `".$this->dbObject->getPrefix()."multimedia_item` ;";
		$results = $this->dbObject->execute($sql);
		if($results === false) return false;
		$alter_table_album_id_flag = true;
		$alter_table_multimedia_id_flag = true;
		$alter_table_upload_id_flag = true;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "album_id") {
				$alter_table_album_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "multimedia_id") {
				$alter_table_multimedia_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "upload_id") {
				$alter_table_upload_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if($alter_table_album_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."multimedia_item` ADD INDEX ( `album_id`, `item_sequence` ) ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_multimedia_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."multimedia_item` ADD INDEX ( `multimedia_id`, `room_id`  ) ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_upload_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."multimedia_item` ADD INDEX ( `upload_id`  ) ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."multimedia_item` ADD INDEX ( `room_id`  ) ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}

		// multimedia_item_tagにindexを追加
		$sql = "SHOW INDEX FROM `".$this->dbObject->getPrefix()."multimedia_item_tag` ;";
		$results = $this->dbObject->execute($sql);
		if($results === false) return false;
		$alter_table_tag_id_flag = true;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "tag_id") {
				$alter_table_tag_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if($alter_table_tag_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."multimedia_item_tag` ADD INDEX ( `tag_id` ) ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."multimedia_item_tag` ADD INDEX ( `room_id`  ) ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}

		// multimedia_tagにindexを追加
		$sql = "SHOW INDEX FROM `".$this->dbObject->getPrefix()."multimedia_tag` ;";
		$results = $this->dbObject->execute($sql);
		if($results === false) return false;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."multimedia_tag` ADD INDEX ( `room_id`  ) ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}

		// mutlimedia_user_itemにindexを追加
		$sql = "SHOW INDEX FROM `".$this->dbObject->getPrefix()."mutlimedia_user_item` ;";
		$results = $this->dbObject->execute($sql);
		if($results === false) return false;
		$alter_table_user_id_flag = true;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "user_id") {
				$alter_table_user_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if(!$alter_table_user_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."mutlimedia_user_item`
						DROP INDEX `user_id` ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->dbObject->getPrefix()."mutlimedia_user_item` ADD INDEX ( `room_id`  ) ;";
			$result = $this->dbObject->execute($sql);
			if($result === false) return false;
		}

		return true;
	}
}
?>