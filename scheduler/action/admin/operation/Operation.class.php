<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * モジュール操作時(move,copy,shortcut)に呼ばれるアクション
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Action_Admin_Operation extends Action
{
	var $mode = null;	//move or shortcut or copy
	// 移動元
	var $block_id = null;
	var $page_id = null;
	var $room_id = null;
	var $unique_id = null;

	// 移動先
	var $move_page_id = null;
	var $move_room_id = null;
	var $move_block_id = null;

	// コンポーネントを受け取るため
	var $db = null;
	var $commonOperation = null;
	var $whatsnewAction = null;

	function execute()
	{
		switch ($this->mode) {
			case "move":
				//スケジューラーチェック
				$where_params = array(
					"scheduler_id"=> intval($this->unique_id),
					"room_id"=> intval($this->room_id)
				);
				$result = $this->db->selectExecute("scheduler", $where_params);
				if($result === false || !isset($result[0])) {
					return "false";
				}

				//更新
				$params = array(
					"room_id"=> intval($this->move_room_id)
				);
				$result = $this->db->updateExecute("scheduler", $params, $where_params, false);
				if($result === false) {
					return "false";
				}
				$scheduler_block_params = array(
					"block_id"=> intval($this->move_block_id),
					"room_id"=> intval($this->move_room_id)
				);
				$where_params = array(
					"block_id"=> intval($this->block_id),
					"scheduler_id"=> intval($this->unique_id)
				);
				$result = $this->db->updateExecute("scheduler_block", $scheduler_block_params, $where_params, false);
				if($result === false) {
					return "false";
				}

				$where_params = array(
					"scheduler_id"=> intval($this->unique_id),
					"room_id"=> intval($this->room_id)
				);
				$func = array($this, "_fetchcallbackSchedulerSchedule");
				$schedule_id_arr = $this->db->selectExecute("scheduler_schedule", $where_params, null, null, null, $func);
				if($schedule_id_arr === false) {
					return "false";
				}
				$result = $this->db->updateExecute("scheduler_schedule", $params, $where_params, false);
				if($result === false) {
					return "false";
				}
				if(is_array($schedule_id_arr)) {
					$where_str = implode("','", $schedule_id_arr);
					$where_params = array(
						"schedule_id IN ('". $where_str. "') " => null
					);
					$result = $this->db->updateExecute("scheduler_schedule_description", $params, $where_params, false);
					if($result === false) {
						return "false";
					}
					$where_params = array(
						"schedule_id IN ('". $where_str. "') " => null
					);
					$result = $this->db->updateExecute("scheduler_reply", $params, $where_params, false);
					if($result === false) {
						return "false";
					}
					$result = $this->db->updateExecute("scheduler_date", $params, $where_params, false);
					if($result === false) {
						return "false";
					}

					//
					// 添付ファイル更新処理
					// WYSIWYG
					//
					$where_params = array(
							"schedule_id IN ('". $where_str. "') " => null
						);
					$description = $this->db->selectExecute("scheduler_schedule_description", $where_params);
					if($description === false) {
						return "false";
					}
					$upload_id_arr = $this->commonOperation->getWysiwygUploads("description", $description);
					$result = $this->commonOperation->updWysiwygUploads($upload_id_arr, $this->move_room_id);
					if($result === false) {
						return "false";
					}
				}

				//--新着情報関連 Start--
				if(is_array($schedule_id_arr) && count($schedule_id_arr) > 0) {
					$whatsnew = array(
						"unique_id" => $schedule_id_arr,
						"room_id" => $this->move_room_id
					);
					$result = $this->whatsnewAction->moveUpdate($whatsnew);
					if ($result === false) {
						return false;
					}
				}
				//--新着情報関連 End--
				break;
			default:
				return "false";
		}
		return "true";
	}

	/**
	 * fetch時コールバックメソッド(config)
	 * @param result adodb object
	 * @access	private
	 */
	function &_fetchcallbackSchedulerSchedule($result) {
		$schedule_id_arr = array();
		while ($row = $result->fetchRow()) {
			$schedule_id_arr[] = $row['schedule_id'];
		}
		return $schedule_id_arr;
	}
}
?>