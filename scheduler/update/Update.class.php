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
class Scheduler_Update extends Action
{
	//使用コンポーネントを受け取るため
	var $db = null;

	function execute()
	{
		// schedulerにindexを追加
		$sql = "SHOW INDEX FROM `".$this->db->getPrefix()."scheduler` ;";
		$results = $this->db->execute($sql);
		if($results === false) return false;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."scheduler` ADD INDEX ( `room_id`  ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}

		// scheduler_blockにindexを追加
		$sql = "SHOW INDEX FROM `".$this->db->getPrefix()."scheduler_block` ;";
		$results = $this->db->execute($sql);
		if($results === false) return false;
		$alter_table_scheduler_id_flag = true;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "scheduler_id") {
				$alter_table_scheduler_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if($alter_table_scheduler_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."scheduler_block` ADD INDEX ( `scheduler_id` ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."scheduler_block` ADD INDEX ( `room_id`  ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}

		// scheduler_dateにindexを追加
		$sql = "SHOW INDEX FROM `".$this->db->getPrefix()."scheduler_date` ;";
		$results = $this->db->execute($sql);
		if($results === false) return false;
		$alter_table_scheduler_id_flag = true;
		$alter_table_schedule_id_flag = true;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "scheduler_id") {
				$alter_table_scheduler_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "schedule_id") {
				$alter_table_schedule_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if($alter_table_scheduler_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."scheduler_date` ADD INDEX ( `scheduler_id` ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_schedule_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."scheduler_date` ADD INDEX ( `schedule_id`, `start_time_full`  ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."scheduler_date` ADD INDEX ( `room_id`  ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}

		// scheduler_replyにindexを追加
		$sql = "SHOW INDEX FROM `".$this->db->getPrefix()."scheduler_reply` ;";
		$results = $this->db->execute($sql);
		if($results === false) return false;
		$alter_table_scheduler_id_flag = true;
		$alter_table_schedule_id_flag = true;
		$alter_table_date_id_flag = true;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "scheduler_id") {
				$alter_table_scheduler_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "schedule_id") {
				$alter_table_schedule_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "date_id") {
				$alter_table_date_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if($alter_table_scheduler_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."scheduler_reply` ADD INDEX ( `scheduler_id` , `user_id`) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_schedule_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."scheduler_reply` ADD INDEX ( `schedule_id`) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_date_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."scheduler_reply` ADD INDEX ( `date_id` ,`user_id` ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."scheduler_reply` ADD INDEX ( `room_id`  ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}

		// scheduler_scheduleにindexを追加
		$sql = "SHOW INDEX FROM `".$this->db->getPrefix()."scheduler_schedule` ;";
		$results = $this->db->execute($sql);
		if($results === false) return false;
		$alter_table_scheduler_id_flag = true;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "scheduler_id") {
				$alter_table_scheduler_id_flag = false;
			}
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if($alter_table_scheduler_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."scheduler_schedule` ADD INDEX ( `scheduler_id`, `room_id` ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."scheduler_schedule` ADD INDEX ( `room_id`  ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}

		// scheduler_schedule_descriptionにindexを追加
		$sql = "SHOW INDEX FROM `".$this->db->getPrefix()."scheduler_schedule_description` ;";
		$results = $this->db->execute($sql);
		if($results === false) return false;
		$alter_table_room_id_flag = true;
		foreach($results as $result) {
			if(isset($result['Key_name']) && $result['Key_name'] == "room_id") {
				$alter_table_room_id_flag = false;
			}
		}
		if($alter_table_room_id_flag) {
			$sql = "ALTER TABLE `".$this->db->getPrefix()."scheduler_schedule_description` ADD INDEX ( `room_id`  ) ;";
			$result = $this->db->execute($sql);
			if($result === false) return false;
		}

		// 件数表示で全てを選択されている場合、100件にする
		$sql = "UPDATE `".$this->db->getPrefix()."scheduler_block` SET visible_row = 100 WHERE visible_row = 0";
		$result = $this->db->execute($sql);
		if($result === false) return false;

		return true;
	}
}
?>
