<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Scheduler登録コンポーネント
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Components_Action
{
	/**
	 * @var DBオブジェクトを保持
	 *
	 * @access	private
	 */

	var $_db = null;

	/**
	 * @var Requestオブジェクトを保持
	 *
	 * @access	private
	 */
	var $_request = null;

	/**
	 * コンストラクター
	 *
	 * @access	public
	 */
	function Scheduler_Components_Action()
	{
		$container =& DIContainerFactory::getContainer();
		$this->_db =& $container->getComponent('DbObject');
		$this->_request =& $container->getComponent('Request');
	}

	/**
	 * スケジューラデータを登録する
	 *
	 * @return boolean true or false
	 * @access public
	 */
	function setScheduler()
	{
		$params = array(
			'authority' => intval($this->_request->getParameter('authority')),
			'mail_send' => intval($this->_request->getParameter('mail_send')),
			'mail_authority' => intval($this->_request->getParameter('mail_authority')),
			'mail_subject' => $this->_request->getParameter('mail_subject'),
			'mail_body' => $this->_request->getParameter('mail_body')
		);

		$schedulerId = $this->_request->getParameter('scheduler_id');
		if (empty($schedulerId)) {
			$result = $this->_db->insertExecute('scheduler', $params, true, 'scheduler_id');
		} else {
			$params['scheduler_id'] = $schedulerId;
			$result = $this->_db->updateExecute('scheduler', $params, 'scheduler_id', true);
		}
		if (!$result) {
			return false;
		}

		if (!empty($schedulerId)) {
			return true;
		}

		$schedulerId = $result;
		$this->_request->setParameter('scheduler_id', $schedulerId);
		if (!$this->setBlock()) {
			return false;
		}

		return true;
	}

	/**
	 * スケジューラ用ブロックデータを登録する
	 *
	 * @return boolean true or false
	 * @access	public
	 */
	function setBlock()
	{
		$blockId = $this->_request->getParameter('block_id');

		$params = array(
			$blockId
		);
		$sql = "SELECT block_id "
				. "FROM {scheduler_block} "
				. "WHERE block_id = ?";
		$blockIds = $this->_db->execute($sql, $params);
		if ($blockIds === false) {
			$this->_db->addError();
			return false;
		}

		$params = array(
			'block_id' => $blockId,
			'scheduler_id' => intval($this->_request->getParameter('scheduler_id')),
			'display' => intval($this->_request->getParameter('display')),
			'visible_row' => intval($this->_request->getParameter('visible_row')),
			'new_period' => intval($this->_request->getParameter('new_period'))
		);

		if ($params['display'] == SCHEDULER_DISPLAY_LIST) {
			$params['schedule_id'] = 0;
		}

		if (!empty($blockIds)) {
			$result = $this->_db->updateExecute('scheduler_block', $params, 'block_id', true);
		} else {
			$result = $this->_db->insertExecute('scheduler_block', $params, true);
		}
		if (!$result) {
			return false;
		}

		return true;
	}

	/**
	 * 選択されたスケジュールを登録する
	 *
	 * @return boolean true or false
	 * @access	public
	 */
	function selectSchedule()
	{
		$params = array(
			'block_id' => $this->_request->getParameter('block_id'),
			'schedule_id' => intval($this->_request->getParameter('schedule_id'))
		);
		if (!$this->_db->updateExecute('scheduler_block', $params, 'block_id', true)) {
			return false;
		}

		return true;
	}

	/** スケジュールデータを登録する */
	function setSchedule()
	{
		$scheduleId = intval($this->_request->getParameter('schedule_id'));
		$isInsert = false;
		if (empty($scheduleId)) {
			$isInsert = true;
		}

		$params = array(
			'scheduler_id' => $this->_request->getParameter('scheduler_id'),
			'icon' => $this->_request->getParameter('icon'),
			'summary' => $this->_request->getParameter('summary'),
			'period' => $this->_request->getParameter('period'),
			'entry_type' => intval($this->_request->getParameter('entry_type'))
		);
		if ($isInsert) {
			$result = $this->_db->insertExecute('scheduler_schedule', $params, true, 'schedule_id');
		} else {
			$params['schedule_id'] = $scheduleId;
			$result = $this->_db->updateExecute('scheduler_schedule', $params, 'schedule_id', true);
		}
		if (!$result) {
			return false;
		}

		if (empty($scheduleId)) {
			$scheduleId = $result;
		}

		$params = array(
			'schedule_id' => $scheduleId,
			'description' => $this->_request->getParameter('description'),
			'room_id' => $this->_request->getParameter('room_id')
		);
		if ($isInsert) {
			$result = $this->_db->insertExecute('scheduler_schedule_description', $params);
		} else {
			$result = $this->_db->updateExecute('scheduler_schedule_description', $params, 'schedule_id', true);
		}
		if (!$result) {
			return false;
		}

		$requestDateIds = $this->_request->getParameter('date_id');
		$params = array($scheduleId);
		$sql = "SELECT date_id "
				. "FROM {scheduler_date} "
				. "WHERE schedule_id = ?";
		$dateIds = $this->_db->execute($sql, $params, null, null, false, array($this, '_fetchDateIds'));
		if ($dateIds === false) {
			$this->_db->addError();
			return false;
		}

		$deleteScheduleIds = array_diff($dateIds, $requestDateIds);
		if (!empty($deleteScheduleIds)) {
			$sql = "DELETE FROM {scheduler_date} ".
					"WHERE date_id IN (" . implode(',', $deleteScheduleIds) . ")";
			$result = $this->_db->execute($sql);
			if ($result === false) {
				$this->_db->addError();
				return false;
			}
		}

		$container =& DIContainerFactory::getContainer();
		$schedulerView =& $container->getComponent('schedulerView');
		$dateIds = $this->_request->getParameter('date_id');
		$alldayFlags = $this->_request->getParameter('allday_flag');
		$startFullDates = $this->_request->getParameter('startFullDate');
		$endFullDates = $this->_request->getParameter('endFullDate');
		$timezoneOffsets = $this->_request->getParameter('timezone_offset');
		$dateFormat = 'Ymd';
		$timeFormat = 'His';

		foreach ($dateIds as $iteration => $dateId) {
			if (empty($alldayFlags[$iteration])) {
				$alldayFlag = _OFF;
			} else {
				$alldayFlag = _ON;
			}

			$gmtOffset = $timezoneOffsets[$iteration] * -1;
			$offsetUnixTimestamp = $schedulerView->_getOffsetUnixTimestamp($startFullDates[$iteration], $gmtOffset);
			$startDate = date($dateFormat, $offsetUnixTimestamp);
			$startTime = date($timeFormat, $offsetUnixTimestamp);

			$offsetUnixTimestamp = $schedulerView->_getOffsetUnixTimestamp($endFullDates[$iteration], $gmtOffset);
			$endDate = date($dateFormat, $offsetUnixTimestamp);
			$endTime = date($timeFormat, $offsetUnixTimestamp);

			$params = array(
				'scheduler_id' => $this->_request->getParameter('scheduler_id'),
				'schedule_id' => $scheduleId,
				'allday_flag' => $alldayFlag,
				'start_date' => $startDate,
				'start_time' => $startTime,
				'start_time_full' => $startDate . $startTime,
				'end_date' => $endDate,
				'end_time' => $endTime,
				'end_time_full' => $endDate . $endTime,
				'timezone_offset' => $timezoneOffsets[$iteration]
			);
			if (empty($dateId)) {
				$result = $this->_db->insertExecute('scheduler_date', $params, true, 'date_id');
			} else {
				$params['date_id'] = $dateId;
				$result = $this->_db->updateExecute('scheduler_date', $params, 'date_id', true);
			}
			if (!$result) {
				return false;
			}
		}

		$scheduler = $this->_request->getParameter('scheduler');
		$mail = $this->_request->getParameter('mail_send');
		if ($scheduler['mail_send'] == _ON
			&& $mail == _ON) {
			$session =& $container->getComponent('Session');
			$session->setParameter('scheduler_mail_schedule_id', $scheduleId);
		}

		$whatsnewAction =& $container->getComponent('whatsnewAction');
		$whatsnew = array(
			'unique_id' => $scheduleId,
			'title' => $this->_request->getParameter('summary'),
			'description' => $this->_request->getParameter('description'),
			'action_name' => 'scheduler_view_main_reply',
			'parameters' => 'schedule_id=' . $scheduleId
		);
		if (!$whatsnewAction->auto($whatsnew)) {
			return false;
		}

		return true;
	}

	/**
	 * 日程ID配列を作成する
	 *
	 * @param array $recordSet 日程IDADORecordSet
	 * @return array 日程ID配列
	 * @access	private
	 */
	function &_fetchDateIds(&$recordSet)
	{
		$dateIDs = array();
		while ($row = $recordSet->fetchRow()) {
			$dateIDs[] = $row[0];
		}

		return $dateIDs;
	}

	/**
	 * スケジュールデータを削除する
	 *
	 * @return boolean true or false
	 * @access	public
	 */
	function deleteSchedule()
	{
		$params = array(
			'schedule_id' => $this->_request->getParameter('schedule_id')
		);

		if (!$this->_db->deleteExecute('scheduler_reply', $params)) {
			return false;
		}

		if (!$this->_db->deleteExecute('scheduler_schedule_description', $params)) {
			return false;
		}

		if (!$this->_db->deleteExecute('scheduler_date', $params)) {
			return false;
		}

		if (!$this->_db->deleteExecute('scheduler_schedule', $params)) {
			return false;
		}

		$container =& DIContainerFactory::getContainer();
		$whatsnewAction =& $container->getComponent('whatsnewAction');
		if (!$whatsnewAction->delete($this->_request->getParameter('schedule_id'))) {
			return false;
		}

		return true;
	}

	/**
	 * スケジュール提出データを登録する
	 *
	 * @return boolean true or false
	 * @access	public
	 */
	function setReply()
	{
		$container =& DIContainerFactory::getContainer();
		$schedulerView =& $container->getComponent('schedulerView');
		$ownReplies =& $schedulerView->getOwnReplies();

		$session =& $container->getComponent('Session');
		$scheduleId = intval($this->_request->getParameter('schedule_id'));
		$replies = $this->_request->getParameter('reply');
		$reply_comments = $this->_request->getParameter('reply_comment');
		foreach (array_keys($replies) as $dateId) {
			$params = array(
				'scheduler_id' => intval($this->_request->getParameter('scheduler_id')),
				'schedule_id' => $scheduleId,
				'date_id' => intval($dateId),
				'user_id' => $session->getParameter('_user_id'),
				'user_name' => $session->getParameter('_handle'),
				'reply' => intval($replies[$dateId]),
				'reply_comment' => $reply_comments[$dateId]
			);

			if (!isset($ownReplies[$dateId]['reply'])) {
				$result = $this->_db->insertExecute('scheduler_reply', $params, true, 'reply_id');
			} else {
				$params['reply_id'] = $ownReplies[$dateId]['reply_id'];
				$result = $this->_db->updateExecute('scheduler_reply', $params, 'reply_id', true);
			}
			if (!$result) {
				return false;
			}
		}

		$params = array(
			$scheduleId
		);
		$sql = "SELECT date_id, "
					. "rank "
				. "FROM {scheduler_date} "
				. "WHERE schedule_id = ? "
				. "ORDER BY start_time_full";
		$scheduleDates = $this->_db->execute($sql, $params);
		if ($scheduleDates === false) {
			$this->_db->addError();
			return false;
		}
		$rankPoints = array();
		$replyUserCounts = array();
		foreach ($scheduleDates as $scheduleDate) {
			$dateId = $scheduleDate['date_id'];
			$rankPoints[$dateId] = 0;
			$replyUserCounts[$dateId] = 0;
		}

		$configView =& $container->getComponent('configView');
		$moduleID = $this->_request->getParameter('module_id');
		$config = $configView->getConfig($moduleID, false);
		if ($config === false) {
			return $config;
		}
		$replayOkFactor = intval($config['replay_ok_factor']['conf_value']);
		$replayNgFactor = intval($config['replay_ng_factor']['conf_value']);
		$replayFineFactor = intval($config['replay_fine_factor']['conf_value']);
		$replayNoneFactor = intval($config['replay_none_factor']['conf_value']);

		$sql = "SELECT date_id, "
					. "reply, "
					. "COUNT(reply_id) AS replyCount "
				. "FROM {scheduler_reply} "
				. "WHERE schedule_id = ? "
				. "GROUP BY date_id, reply";
		$replyCounts = $this->_db->execute($sql, $params);
		if ($replyCounts === false) {
			$this->_db->addError();
			return false;
		}
		foreach ($replyCounts as $replyCount) {
			if ($replyCount['reply'] == SCHEDULER_REPLY_OK) {
				$replayFactor = $replayOkFactor;
			} elseif ($replyCount['reply'] == SCHEDULER_REPLY_NG) {
				$replayFactor = $replayNgFactor;
			} elseif ($replyCount['reply'] == SCHEDULER_REPLY_FINE) {
				$replayFactor = $replayFineFactor;
			} else {
				$replayFactor = $replayNoneFactor;
			}

			$dateId = $replyCount['date_id'];
			$rankPoints[$dateId] += $replyCount['replyCount'] * $replayFactor;
			$replyUserCounts[$dateId] += $replyCount['replyCount'];
		}

		$params =& $schedulerView->getRoomMemberWhereParameter();
		$sql = "SELECT COUNT(U.user_id) "
				. $schedulerView->getRoomMemberFromSql();
		$roomMemberCounts = $this->_db->execute($sql, $params, null, null, false);
		if ($roomMemberCounts === false) {
			$this->_db->addError();
			return false;
		}
		$roomMemberCount = $roomMemberCounts[0][0];

		foreach ($scheduleDates as $scheduleDate) {
			$dateId = $scheduleDate['date_id'];
			$replyNoneCount = $roomMemberCount - $replyUserCounts[$dateId];
			$rankPoints[$dateId] += $replyNoneCount * $replayNoneFactor;
		}

		$sortRankPoints = array();
		while (!empty($rankPoints)) {
			$maxPoint = max($rankPoints);
			$samePointDateIds = array_keys($rankPoints, $maxPoint);

			foreach ($samePointDateIds as $dateId) {
				$sortRankPoints[$dateId] = $rankPoints[$dateId];
				unset($rankPoints[$dateId]);
			}
		}

		$rankNumber = 0;
		foreach ($sortRankPoints as $dateId => $sortRankPoint) {
			$rankNumber++;
			$params = array(
				'date_id' => $dateId,
				'rank' => $rankNumber,
				'rank_point' => $sortRankPoint
			);
			if (!$this->_db->updateExecute('scheduler_date', $params, 'date_id', true)) {
				return false;
			}
		}

		return true;
	}
}