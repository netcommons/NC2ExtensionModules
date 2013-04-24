<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジューラデータ取得コンポーネントクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Components_View
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
	 * @access private
	 */
	var $_request = null;

	/**
	 * @var Sessionオブジェクトを保持
	 *
	 * @access private
	 */
	var $_session = null;

	/**
	 * コンストラクター
	 *
	 * @access	public
	 */
	function Scheduler_Components_View()
	{
		$container =& DIContainerFactory::getContainer();
		$this->_db =& $container->getComponent('DbObject');
		$this->_request =& $container->getComponent('Request');
		$this->_session =& $container->getComponent('Session');
	}

	/**
	 * 現在配置されているスケジューラIDを取得する
	 *
	 * @return string 配置されているスケジューラID
	 * @access pblic
	 */
	function &getCurrentSchedulerId()
	{
		$params = array(
			$this->_request->getParameter('block_id')
		);
		$sql = "SELECT scheduler_id "
				. "FROM {scheduler_block} "
				. "WHERE block_id = ?";
		$schedulerIds = $this->_db->execute($sql, $params);
		if ($schedulerIds === false) {
			$this->_db->addError();
			return $schedulerIds;
		}

		return $schedulerIds[0]['scheduler_id'];
	}

	/**
	 * 現在登録されているスケジューラIDを取得する
	 *
	 * @return string 配置されているスケジューラID
	 * @access pblic
	 */
	function &getFirstSchedulerId()
	{
		$params = array(
			$this->_request->getParameter('room_id')
		);
		$sql = "SELECT scheduler_id "
				. "FROM {scheduler} "
				. "WHERE room_id = ?";
		$schedulerIds = $this->_db->execute($sql, $params, 1);
		if ($schedulerIds === false) {
			$this->_db->addError();
			return $schedulerIds;
		}

		return $schedulerIds[0]['scheduler_id'];
	}

	/**
	 * スケジューラが配置されているブロックータを取得する
	 *
	 * @return array ブロックデータ
	 * @access public
	 */
	function &getBlock()
	{
		$params = array(
			$this->_request->getParameter('scheduler_id')
		);
		$sql = "SELECT room_id, block_id "
				. "FROM {scheduler_block} B "
				. "WHERE scheduler_id = ? "
				. "ORDER BY block_id";
		$blocks = $this->_db->execute($sql, $params, 1);
		if ($blocks === false) {
			$this->_db->addError();
			return $blocks;
		}

		return $blocks[0];
	}

	/**
	 * スケジューラが存在するか判断する
	 *
	 * @return boolean true:存在する、false:存在しない
	 * @access public
	 */
	function schedulerExists()
	{
		$params = array(
			$this->_request->getParameter('scheduler_id'),
			$this->_request->getParameter('room_id')
		);
		$sql = "SELECT scheduler_id "
				. "FROM {scheduler} "
				. "WHERE scheduler_id = ? "
				. "AND room_id = ?";
		$schedulerIds = $this->_db->execute($sql, $params);
		if ($schedulerIds === false) {
			$this->_db->addError();
			return $schedulerIds;
		}

		if (count($schedulerIds) > 0) {
			return true;
		}

		return false;
	}

	/**
	 * スケジューラ用デフォルトデータを取得する
	 *
	 * @return array スケジューラ用デフォルトデータ配列
	 * @access public
	 */
	function &getDefaultScheduler()
	{
		$container =& DIContainerFactory::getContainer();
		$configView =& $container->getComponent('configView');
		$moduleID = $this->_request->getParameter('module_id');
		$config = $configView->getConfig($moduleID, false);
		if ($config === false) {
			return $config;
		}

		$filterChain =& $container->getComponent('FilterChain');
		$smartyAssign =& $filterChain->getFilterByName('SmartyAssign');

		$scheduler = array(
			'authority' => constant($config['authority']['conf_value']),
			'mail_send' => constant($config['mail_send']['conf_value']),
			'mail_authority' => constant($config['mail_authority']['conf_value']),
			'mail_subject' => $smartyAssign->getLang('scheduler_mail_subject_default'),
			'mail_body' => $smartyAssign->getLang('scheduler_mail_body_default'),
			'display' => constant($config['display']['conf_value']),
			'visible_row' => $config['visible_row']['conf_value'],
			'new_period' => $config['new_period']['conf_value']
		);

		return $scheduler;
	}

	/**
	 * スケジューラデータを取得する
	 *
	 * @return array スケジューラデータ配列
	 * @access public
	 */
	function &getScheduler()
	{
		$params = array(
			$this->_request->getParameter('scheduler_id')
		);
		$sql = "SELECT scheduler_id, "
					. "authority, "
					. "mail_send, "
					. "mail_authority, "
					. "mail_subject, "
					. "mail_body "
				. "FROM {scheduler} "
				. "WHERE scheduler_id = ?";
		$schedulers = $this->_db->execute($sql, $params);
		if ($schedulers === false) {
			$this->_db->addError();
			return $schedulers;
		}

		return $schedulers[0];
	}

	/**
	 * ルームIDのスケジューラ件数を取得する
	 *
	 * @return string スケジューラ件数
	 * @access public
	 */
	function &getSchedulerCount()
	{
		$params = array(
			'room_id' => $this->_request->getParameter('room_id')
		);
		$count = $this->_db->countExecute('scheduler', $params);

		return $count;
	}

	/**
	 * 現在配置されているスケジューラデータを取得する
	 *
	 * @return array 配置されているスケジューラデータ配列
	 * @access public
	 */
	function &getCurrentScheduler()
	{
		$params = array(
			$this->_request->getParameter('block_id')
		);
		$sql = "SELECT B.block_id, "
					. "B.scheduler_id, "
					. "B.display, "
					. "B.visible_row, "
					. "B.new_period, "
					. "B.schedule_id, "
					. "S.authority, "
					. "S.mail_send "
				. "FROM {scheduler_block} B "
				. "INNER JOIN {scheduler} S "
				. "ON B.scheduler_id = S.scheduler_id "
				. "WHERE B.block_id = ?";
		$schedulers = $this->_db->execute($sql, $params);
		if ($schedulers === false) {
			$this->_db->addError();
		}
		if (empty($schedulers)) {
			return $schedulers;
		}

		$schedulers[0]['createAuthority'] = $this->_hasCreateAuthority($schedulers[0]);
		$schedulers[0]['selectAuthority'] = $this->_hasSelectAuthority($schedulers[0]);
		$schedulers[0]['newPeriodTime'] = $this->_getNewPeriodTime($schedulers[0]['new_period']);

		return $schedulers[0];
	}

	/**
	 * スケジュール作成権限を取得する
	 *
	 * @param array $scheduler スケジュールデータ配列
	 * @return boolean true:権限有り、false:権限無し
	 * @access public
	 */
	function _hasCreateAuthority($scheduler)
	{
		$authId = $this->_session->getParameter('_auth_id');
		if ($authId >= $scheduler['authority']) {
			return true;
		}

		return false;
	}

	/**
	 * スケジュール選択権限を取得する
	 *
	 * @param array $scheduler スケジュールデータ配列
	 * @return boolean true:権限有り、false:権限無し
	 * @access public
	 */
	function _hasSelectAuthority($scheduler)
	{
		$authId = $this->_session->getParameter('_auth_id');
		if ($scheduler['createAuthority']
			&& $scheduler['display'] == SCHEDULER_DISPLAY_SELECT) {
			return true;
		}

		return false;
	}

	/**
	 * new記号表示期間から対象年月日を取得する
	 *
	 * @param string $newPeriod new記号表示期間
	 * @return string new記号表示対象年月日(YmdHis)
	 * @access public
	 */
	function &_getNewPeriodTime($newPeriod)
	{
		if (empty($newPeriod)) {
			$newPeriod = -1;
		}

		$time = timezone_date();
		$time = mktime(0, 0, 0,
						intval(substr($time, 4, 2)),
						intval(substr($time, 6, 2)) - $newPeriod,
						intval(substr($time, 0, 4))
						);
		$time = date('YmdHis', $time);

		return $time;
	}

	/**
	 * スケジュール件数を取得する
	 *
	 * @return string スケジューラ件数
	 * @access public
	 */
	function &getScheduleCount()
	{
		$params = array(
			'scheduler_id' => $this->_request->getParameter('scheduler_id')
		);
		$count = $this->_db->countExecute('scheduler_schedule', $params);

		return $count;
	}

	/**
	 * スケジュール一覧データを取得する
	 *
	 * @return array スケジュール一覧データ配列
	 * @access public
	 */
	function &getSchedules()
	{
		$blockID = $this->_request->getParameter('block_id');
		$limit = $this->_session->getParameter('scheduler_visible_row' .  $blockID);

		$pageNumber = $this->_request->getParameter('pageNumber');
		$offset = $pageNumber * $limit;

		$params = array(
			$this->_request->getParameter('scheduler_id')
		);
		$orderParams = array(
			'schedule_id' => 'DESC'
		);
		$sql = "SELECT S.schedule_id, "
					. "S.summary, "
					. "S.icon, "
					. "S.period, "
					. "S.insert_time, "
					. "S.insert_user_id, "
					. "S.insert_user_name, "
					. "D.description "
				. "FROM {scheduler_schedule} S "
				. "LEFT JOIN {scheduler_schedule_description} D "
					. "ON S.schedule_id = D.schedule_id "
				. "WHERE scheduler_id = ?"
				. $this->_db->getOrderSQL($orderParams);
		$schedules = $this->_db->execute($sql, $params, $limit, $offset, true, array($this, '_fetchSchedule'));
		if ($schedules === false) {
			$this->_db->addError();
		}

		return $schedules;
	}

	/**
	 * スケジュールデータ配列を生成する
	 *
	 * @param object $recordSet スケジュールADORecordSet
	 * @return array スケジュールデータ配列
	 * @access private
	 */
	function &_fetchSchedule(&$recordSet)
	{
		$container =& DIContainerFactory::getContainer();
		$actionChain =& $container->getComponent('ActionChain');
		$actionName = $actionChain->getCurActionName();
		$action =& $actionChain->getCurAction();
		$classVars = get_class_vars(get_class($action));

		if (array_key_exists('config', $classVars)) {
			$soonPeriod = $action->config['soon_period']['conf_value'];
		} else {
			$configView =& $container->getComponent('configView');
			$moduleID = $this->_request->getParameter('module_id');
			$config = $configView->getConfigByConfname($moduleID, 'soon_period');
			if ($config === false) {
				return $config;
			}
			$soonPeriod = $config['conf_value'];
		}

		$scheduler = $this->_request->getParameter('scheduler');
		if (empty($scheduler)
			&& array_key_exists('scheduler', $classVars)) {
			$scheduler = $action->scheduler;
		}
		if (empty($scheduler)) {
			$scheduler = $this->getCurrentScheduler();
		}
		if (empty($scheduler['createAuthority'])) {
			$scheduler['createAuthority'] = false;
		}

		$today = timezone_date_format(null, null);
		$soonDate = mktime(0, 0, 0,
							intval(substr($today, 4, 2)),
							intval(substr($today, 6, 2)) + $soonPeriod,
							intval(substr($today, 0, 4)));
		$format = str_replace('H', '24', _DATE_FORMAT);
		$today = timezone_date_format(null, $format);
		$soonDate = date($format, $soonDate);

		$schedules = array();
		while ($schedule = $recordSet->fetchRow()) {
			$schedule['editAuthority'] = false;
			if ($scheduler['createAuthority']
				&& $this->_hasEditAuthority($schedule['insert_user_id'])) {
				$schedule['editAuthority'] = true;
			}

			if ($actionName == 'scheduler_view_main_list') {
				$convertHtml = $container->getComponent('convertHtml');
				$schedule['description'] = $convertHtml->convertHtmlToText($schedule['description']);
				$schedule['description'] = trim($schedule['description']);
			}

			if (empty($schedule['period'])) {
				$schedules[] = $schedule;
				continue;
			}

			$period = timezone_date_format($schedule['period'], null);
			$previousDay = -1;

			$periodDate = mktime(intval(substr($period, 8, 2)),
							intval(substr($period, 10, 2)),
							intval(substr($period, 12, 2)),
							intval(substr($period, 4, 2)),
							intval(substr($period, 6, 2)) + $previousDay,
							intval(substr($period, 0, 4)));
			$schedule['displayPeriodDate'] = date($format, $periodDate);

			if ($today > $schedule['displayPeriodDate']) {
				$schedule['periodClassName'] = 'scheduler_period_over';
			} else if ($soonDate >= $schedule['displayPeriodDate']) {
				$schedule['periodClassName'] = 'scheduler_period_soon';
			}

			$schedules[] = $schedule;
		}

		return $schedules;
	}

	/**
	 * スケジュール編集権限を取得する
	 *
	 * @param string $insertUserID 作成者ID
	 * @return boolean true:権限有り、false:権限無し
	 * @access public
	 */
	function _hasEditAuthority(&$insertUserID)
	{
		$authID = $this->_session->getParameter('_auth_id');
		if ($authID >= _AUTH_CHIEF) {
			return true;
		}

		$userID = $this->_session->getParameter('_user_id');
		if ($insertUserID == $userID) {
			return true;
		}

		$hierarchy = $this->_session->getParameter('_hierarchy');
		$container =& DIContainerFactory::getContainer();
		$authCheck =& $container->getComponent('authCheck');
		$insetUserHierarchy = $authCheck->getPageHierarchy($insertUserID);
		if ($hierarchy > $insetUserHierarchy) {
			return true;
		}

		return false;
	}

	/**
	 * スケジュールデータを取得する
	 *
	 * @return array スケジュールデータ配列
	 * @access public
	 */
	function &getSchedule()
	{
		$params = array(
			$this->_request->getParameter('schedule_id')
		);
		$sql = "SELECT S.schedule_id, "
					. "S.scheduler_id, "
					. "S.summary, "
					. "S.icon, "
					. "S.period, "
					. "S.entry_type, "
					. "S.insert_user_id, "
					. "S.insert_user_name, "
					. "S.insert_time, "
					. "D.description "
				. "FROM {scheduler_schedule} S "
				. "LEFT JOIN {scheduler_schedule_description} D "
					. "ON S.schedule_id = D.schedule_id "
				. "WHERE S.schedule_id = ?";
		$schedules = $this->_db->execute($sql, $params, null, null, true, array($this, '_fetchSchedule'));
		if ($schedules === false) {
			$this->_db->addError();
		}
		if (empty($schedules)) {
			return $schedules;
		}

		return $schedules[0];
	}

	/**
	 * スケジュール日程データを取得する
	 *
	 * @param boolean $dayAsKey true:年月日をキーとする
	 * @return array スケジュールデータ配列
	 * @access public
	 */
	function &getScheduleDates($dayAsKey = false)
	{
		$params = array(
			$this->_request->getParameter('schedule_id')
		);
		$sql = "SELECT date_id, "
					. "schedule_id, "
					. "rank, "
					. "allday_flag, "
					. "start_date, "
					. "start_time_full, "
					. "end_date, "
					. "end_time_full, "
					. "timezone_offset, "
					. "calendar_id "
				. "FROM {scheduler_date} "
				. "WHERE schedule_id = ? "
				. "ORDER BY start_time_full, "
					. "end_time_full";
		$scheduleDates = $this->_db->execute($sql, $params, null, null, true, array($this, '_fetchDate'), $dayAsKey);
		if ($scheduleDates === false) {
			$this->_db->addError();
		}
		if (empty($scheduleDates)) {
			return $scheduleDates;
		}

		return $scheduleDates;
	}

	/**
	 * スケジュール日程データ配列を生成する
	 *
	 * @param object $recordSet スケジュール日程ADORecordSet
	 * @param boolean $dayAsKey true:年月日をキーとする
	 * @return array スケジュール日程データ配列
	 * @access private
	 */
	function &_fetchDate(&$recordSet, $dayAsKey)
	{
		$container =& DIContainerFactory::getContainer();
		$filterChain =& $container->getComponent('FilterChain');
		$smartyAssign =& $filterChain->getFilterByName('SmartyAssign');
		$dateFormat = $smartyAssign->getLang('scheduler_date_format');
		$timeFormat = $smartyAssign->getLang('_short_time_format');
		$dayNames = $smartyAssign->getLang('scheduler_day_of_week');
		$dayNames = explode(SCHEDULER_DAY_OF_WEEK_SEPARATOR, $dayNames);
		$entryDateFormat = $smartyAssign->getLang('_input_date_format');

		$scheduleDates = array();
		$schedule = $this->_request->getParameter('schedule');
		if (empty($schedule)) {
			return $scheduleDates;
		}
		while ($scheduleDate = $recordSet->fetchRow()) {
			$offsetUnixTimestamp =& $this->_getOffsetUnixTimestamp($scheduleDate['start_time_full'], $scheduleDate['timezone_offset']);

			$dayNumber = date('w', $offsetUnixTimestamp);
			$dateClassName =& $this->_getDayClassName($dayNumber);
			$timeClassName = $dateClassName;

			$scheduleDate['startDisplayDate'] = date(sprintf($dateFormat, $dayNames[$dayNumber]), $offsetUnixTimestamp);
			$scheduleDate['startDateClassName'] = $dateClassName;
			$scheduleDate['startTimeClassName'] = $timeClassName;
			$scheduleDate['startEntryDate'] = date($entryDateFormat, $offsetUnixTimestamp);
			$scheduleDate['startOffsetUnixTimestamp'] = $offsetUnixTimestamp;

			$offsetUnixTimestamp =& $this->_getOffsetUnixTimestamp($scheduleDate['end_time_full'], $scheduleDate['timezone_offset']);
			$time24Format = $timeFormat;
			$hourFormat = 'H';
			if ($scheduleDate['start_date'] != $scheduleDate['end_date']
				&& date('H', $offsetUnixTimestamp) == '00') {
				$offsetUnixTimestamp = mktime(0,
									intval(date('i', $offsetUnixTimestamp)),
									intval(date('s', $offsetUnixTimestamp)),
									intval(date('m', $offsetUnixTimestamp)),
									intval(date('d', $offsetUnixTimestamp)) - 1,
									intval(date('Y', $offsetUnixTimestamp))
									);
				$time24Format = str_replace('H', '24', $timeFormat);
				$hourFormat = '24';
			}

			$dayNumber = date('w', $offsetUnixTimestamp);
			$dateClassName =& $this->_getDayClassName($dayNumber);
			$timeClassName = $dateClassName;

			$scheduleDate['endDisplayDate'] = date(sprintf($dateFormat, $dayNames[$dayNumber]), $offsetUnixTimestamp);
			$scheduleDate['endDateClassName'] = $dateClassName;
			$scheduleDate['endTimeClassName'] = $timeClassName;
			$scheduleDate['endEntryDate'] = date($entryDateFormat, $offsetUnixTimestamp);
			$scheduleDate['endOffsetUnixTimestamp'] = $offsetUnixTimestamp;

			$scheduleDate['topDateClassName'] = '';
			if ($scheduleDate['rank'] == 1) {
				$scheduleDate['topDateClassName'] = 'scheduler_top_date';
			}

			if (!$dayAsKey) {
				$scheduleDates[] = $scheduleDate;

				continue;
			}

			if ($schedule['entry_type'] == SCHEDULER_ENTRY_CALENDAR) {
				$startTime = date('Hi', $scheduleDate['startOffsetUnixTimestamp']);
				$endTime = date('Hi', $scheduleDate['endOffsetUnixTimestamp']);
				$separateValue = '';
				$separateClassName = '';
				if ($startTime == SCHEDULER_CALENDAR_MORNING_START
					&& $endTime == SCHEDULER_CALENDAR_MORNING_END) {
					$separateValue = $smartyAssign->getLang('scheduler_calendar_morning');
					$separateClassName = 'scheduler_calendar_morning';
				} elseif ($startTime == SCHEDULER_CALENDAR_AFTERNOON_START
					&& $endTime == SCHEDULER_CALENDAR_AFTERNOON_END) {
					$separateValue = $smartyAssign->getLang('scheduler_calendar_afternoon');
					$separateClassName = 'scheduler_calendar_afternoon';
				} elseif ($startTime == SCHEDULER_CALENDAR_EVENING_START
					&& $endTime == SCHEDULER_CALENDAR_EVENING_END) {
					$separateValue = $smartyAssign->getLang('scheduler_calendar_evening');
					$separateClassName = 'scheduler_calendar_evening';
				} elseif ($scheduleDate['allday_flag'] == _ON) {
					$separateValue = $smartyAssign->getLang('scheduler_all_day');
					$separateClassName = 'scheduler_calendar_all_day';
				}

				$timeData = array(
					'date_id' => $scheduleDate['date_id'],
					'separateValue' => $separateValue,
					'separateClassName' => $separateClassName,
					'topDateClassName' => $scheduleDate['topDateClassName']
				);

				$dayKey = date('Ymd', $scheduleDate['startOffsetUnixTimestamp']);
				if (empty($scheduleDates[$dayKey])) {
					$scheduleDate = array(
						'entryTypeClassName' => 'scheduler_entry_type_calendar',
						'displayDate' => $scheduleDate['startDisplayDate'],
						'dateClassName' => $scheduleDate['startDateClassName'],
					);
					$scheduleDates[$dayKey] = $scheduleDate;
				}

				$scheduleDates[$dayKey]['timeDatas'][] = $timeData;
				$scheduleDates[$dayKey]['timeDataCount'] = count($scheduleDates[$dayKey]['timeDatas']);

				continue;
			}

			$dayKey = date('Ymd', $scheduleDate['startOffsetUnixTimestamp']);
			$isMultipleDate = true;
			if (date('Ymd', $scheduleDate['startOffsetUnixTimestamp']) == date('Ymd', $scheduleDate['endOffsetUnixTimestamp'])) {
				$isMultipleDate = false;
				$scheduleDate['startTimeClassName'] .= ' scheduler_schedule_date_single';
			}
			$timeData = array(
				'date_id' => $scheduleDate['date_id'],
				'allday_flag' => $scheduleDate['allday_flag'],
				'isMultipleDate' => $isMultipleDate,
				'startOffsetUnixTimestamp' => $scheduleDate['startOffsetUnixTimestamp'],
				'startTimeClassName' => $scheduleDate['startTimeClassName'],
				'endDisplayDate' => $scheduleDate['endDisplayDate'],
				'endOffsetUnixTimestamp' => $scheduleDate['endOffsetUnixTimestamp'],
				'endTimeClassName' => $scheduleDate['endTimeClassName'],
				'topDateClassName' => $scheduleDate['topDateClassName']
			);
			if (empty($scheduleDates[$dayKey])) {
				$scheduleDate = array(
					'entryTypeClassName' => 'scheduler_entry_type_input',
					'displayDate' => $scheduleDate['startDisplayDate'],
					'dateClassName' => $scheduleDate['startDateClassName']
				);
				$scheduleDates[$dayKey] = $scheduleDate;
			}
			$scheduleDates[$dayKey]['timeDatas'][] = $timeData;
			$scheduleDates[$dayKey]['timeDataCount'] = count($scheduleDates[$dayKey]['timeDatas']);
		}

		return $scheduleDates;
	}

	/**
	 * タイムゾーンを計算した日時を取得する
	 *
	 * @param string $dateTime 日時文字列(YmdHis形式)
	 * @param string $timezoneOffset タイムゾーンオフセット値
	 * @return array スケジュール日時データ
	 * @access private
	 */
	function &_getOffsetUnixTimestamp($dateTime, $timezoneOffset)
	{
		$timezoneOffset = number_format($timezoneOffset, 1, '.', '');
		list($hourOffset, $minuteOffset) = explode('.', $timezoneOffset);
		$minuteOffset = $minuteOffset * 6;
		if ($hourOffset < 0) {
			$minuteOffset = $minuteOffset * -1;
		}

		$offsetUnixTimestamp = mktime(intval(substr($dateTime, 8, 2)) + $hourOffset,
										intval(substr($dateTime, 10, 2)) + $minuteOffset,
										intval(substr($dateTime, 12, 2)),
										intval(substr($dateTime, 4, 2)),
										intval(substr($dateTime, 6, 2)),
										intval(substr($dateTime, 0, 4))
										);

		return $offsetUnixTimestamp;
	}

	/**
	 * 曜日のCSSクラス名称を取得する
	 *
	 * @param string $dayNumber 曜日番号
	 * @return array 曜日のCSSクラス名称
	 * @access private
	 */
	function &_getDayClassName($dayNumber)
	{
		$className = 'scheduler_weekday';
		if ($dayNumber == '6') {
			$className = 'scheduler_saturday';
		}
		if ($dayNumber == '0') {
			$className = 'scheduler_sunday';
		}

		return $className;
	}

	/**
	 * 自分のスケジュール提出データ一覧を取得する
	 *
	 * @return array 自分のスケジュール提出データ一覧配列
	 * @access public
	 */
	function &getOwnReplies()
	{
		$_user_id = $this->_session->getParameter('_user_id');
		if ($_user_id == "0"
			|| $this->_session->getParameter('_auth_id') <= _AUTH_GUEST) {
			return array();
		}
		$params = array(
			$this->_request->getParameter('schedule_id'),
			$_user_id
		);
		$sql = "SELECT reply_id, "
					. "date_id, "
					. "reply, "
					. "reply_comment "
				. "FROM {scheduler_reply} "
				. "WHERE schedule_id = ? "
				. "AND user_id = ?";
		$replies = $this->_db->execute($sql, $params, null, null, true, array($this, '_fetchReply'));
		if ($replies === false) {
			$this->_db->addError();
		}
		return $replies;
	}

	/**
	 * スケジュール提出データ配列を生成する
	 *
	 * @param object $recordSet スケジュール提出ADORecordSet
	 * @return array スケジュール提出データ配列
	 * @access private
	 */
	function &_fetchReply(&$recordSet)
	{
		$replies = array();
		while ($reply = $recordSet->fetchRow()) {
			$dateId = $reply['date_id'];
			$replies[$dateId]['reply_id'] = $reply['reply_id'];
			$replies[$dateId]['reply'] = $reply['reply'];

			if ($reply['reply'] == SCHEDULER_REPLY_FINE) {
				$replies[$dateId]['reply_comment'] = $reply['reply_comment'];
			}
		}
		$replies['reply_user_id'] = $this->_session->getParameter('_user_id');
		return $replies;
	}

	/**
	 * 回答コメントの取得
	 *
	 * @access public
	 */
	function getReplyComment($reply_id)
	{
		$result = $this->_db->selectExecute('scheduler_reply', array('reply_id'=>$reply_id));
		if ($result === false) {
			$this->_db->addError();
			return $result;
		}
		return $result;
	}

	/**
	 * 参加会員取得用のSQL文FROM句を取得する
	 *
	 * @return string 参加会員取得用のSQL文FROM句
	 * @access public
	 */
	function &getRoomMemberFromSql()
	{
		$defaultEntry = $this->_session->getParameter('_default_entry_flag');
		$defaultEntryAuthority = $this->_session->getParameter('_default_entry_auth');
		if ($defaultEntry == _ON
			&& $defaultEntryAuthority != _AUTH_GUEST) {
			$whereSql = '(A.user_authority_id > ? '
						. 'OR P.role_authority_id IS NULL) ';
		} else {
			$whereSql = 'A.user_authority_id > ? ';
		}

		$sql = "FROM {users} U "
				. "LEFT JOIN {pages_users_link} P "
					. "ON U.user_id = P.user_id "
					. "AND P.room_id = ? "
				. "LEFT JOIN {authorities} A "
					. "ON P.role_authority_id = A.role_authority_id "
				. "WHERE U.user_id != ? "
				. "AND U.active_flag = ? "
				. "AND " . $whereSql;

		return $sql;
	}

	/**
	 * 参加会員取得用のWHERE句パラメータ配列を取得する
	 *
	 * @return array WHERE句パラメータ配列
	 * @access public
	 */
	function &getRoomMemberWhereParameter()
	{
		$params = array(
			$this->_request->getParameter('room_id'),
			$this->_session->getParameter('_user_id'),
			_ON,
			_AUTH_GUEST
		);

		return $params;
	}

	/**
	 * スケジュール提出データ一覧を取得する
	 *
	 * @return array スケジュール提出データ一覧配列
	 * @access public
	 */
	function &getReplyUsers()
	{
		$params =& $this->getRoomMemberWhereParameter();
		$orderParams = array(
			'U.role_authority_id' => 'ASC',
			'U.handle' => 'ASC'
		);
		$sql = "SELECT U.user_id, "
					. "U.handle "
				. $this->getRoomMemberFromSql()
				. $this->_db->getOrderSQL($orderParams);
		$users = $this->_db->execute($sql, $params, null, null, true, array($this, '_fetchUsers'));
		if ($users === false) {
			$this->_db->addError();
			return $users;
		}

		$params = array(
			$this->_request->getParameter('schedule_id'),
			$this->_session->getParameter('_user_id')
		);
		$orderParams = array(
			'user_id' => 'ASC',
			'date_id' => 'ASC'
		);
		$sql = "SELECT date_id, "
					. "user_id, "
					. "reply, "
					. "reply_comment "
				. "FROM {scheduler_reply} "
				. "WHERE schedule_id = ? "
				. "AND user_id != ?"
				. $this->_db->getOrderSQL($orderParams);
		$replyUsers = $this->_db->execute($sql, $params, null, null, true, array($this, '_fetchReplyUsers'), $users);
		if ($replyUsers === false) {
			$this->_db->addError();
		}

		return $replyUsers;
	}

	/**
	 * ルーム参加ユーザデータ配列を生成する
	 *
	 * @param object $recordSet ルーム参加ユーザADORecordSet
	 * @return array ルーム参加ユーザデータ配列
	 * @access private
	 */
	function &_fetchUsers(&$recordSet)
	{
		$users = array();
		while ($user = $recordSet->fetchRow()) {
			$userId = $user['user_id'];
			$users[$userId] = $user;
		}

		return $users;
	}

	/**
	 * スケジュール提出データ配列を生成する
	 *
	 * @param object $recordSet スケジュール提出ADORecordSet
	 * @param array $users ルーム参加ユーザデータ配列
	 * @return array スケジュール提出データ配列
	 * @access private
	 */
	function &_fetchReplyUsers(&$recordSet, &$users)
	{
		$replyUsers = $users;
		while ($replyUser = $recordSet->fetchRow()) {
			$userId = $replyUser['user_id'];
			if (empty($replyUsers[$userId])) {
				continue;
			}

			$dateId = $replyUser['date_id'];
			$replyUsers[$userId]['reply'][$dateId] = $replyUser['reply'];
			if ($replyUser['reply'] == SCHEDULER_REPLY_FINE) {
				$replyUsers[$userId]['reply_comment'][$dateId] = $replyUser['reply_comment'];
			}
		}

		return $replyUsers;
	}

	/**
	 * 他人のコメントを取得する
	 *
	 * @return string コメント
	 * @access private
	 */
	function getOthersComment()
	{
		$sql = "SELECT reply_comment "
				. "FROM {scheduler_reply} "
				. "WHERE date_id = ? "
				. "AND user_id = ?";

		$params = array(
			$this->_request->getParameter('date_id'),
			$this->_request->getParameter('reply_user_id')
		);

		$result = $this->_db->execute($sql, $params);
		if ($result === false) {
			$this->_db->addError();
		}
		return $result[0]['reply_comment'];
	}

	/**
	 * カレンダデータを設定する
	 *
	 * @param object $action アクションクラス
	 * @access private
	 */
	function setCalendarValues(&$action)
	{
		$container =& DIContainerFactory::getContainer();
		$filterChain =& $container->getComponent('FilterChain');
		$smartyAssign =& $filterChain->getFilterByName('SmartyAssign');
		$dayOfWeekText = $smartyAssign->getLang('scheduler_day_of_week');
		$action->dayOfWeeks = explode(SCHEDULER_DAY_OF_WEEK_SEPARATOR, $dayOfWeekText);

		$second4day = 86400;
		$formatDate = date('Ymd', intval($action->calendarDate));
		$firstDay = mktime(0, 0, 0,
							date('m', $action->calendarDate),
							1,
							date('Y', $action->calendarDate)
							);
		$action->calendarStart = $firstDay - date('w', $firstDay) * $second4day;

		$action->calendarEnd = $firstDay + date('t', $firstDay) * $second4day;
		$dayNumber = date('w', $action->calendarEnd);
		if ($dayNumber > 0) {
			$action->calendarEnd = $action->calendarEnd + (7 - $dayNumber) * $second4day;
		}

		$blockId = $this->_request->getParameter('block_id');
		$sessionCalendars = $this->_session->getParameter(SCHEDULER_CALENDAR_SESSION_KEY . $blockId);
		if (empty($sessionCalendars)) {
			$sessionCalendars = array();
		}

		foreach ($sessionCalendars as $calendarStartDate => $sessionCalendar) {
			if ($sessionCalendar['allday_flag'] == _ON
				&& $calendarStartDate >= date('Ymd', $action->calendarStart)
				&& $calendarStartDate < date('Ymd', $action->calendarEnd)) {
				$action->sessionCalendars[$calendarStartDate] = $sessionCalendar;
				continue;
			}

			if ($sessionCalendar['allday_flag'] == _OFF
				&& $calendarStartDate >= date('Ymd', $action->calendarStart) . '0000'
				&& $calendarStartDate < date('Ymd', $action->calendarEnd) . '0000') {
				$action->sessionCalendars[$calendarStartDate] = $sessionCalendar;
				continue;
			}
		}

		$holidayView =& $container->getComponent('holidayView');
		$startDate = date('YmdHis', $action->calendarStart);
		$endDate = date('YmdHis', $action->calendarEnd);
		$holidays = $holidayView->get($startDate, $endDate, false);
		$action->holidays = array_keys($holidays);
	}

	/**
	 * カレンダデータを設定する
	 *
	 * @param object $action アクションクラス
	 * @access private
	 */
	function setHolidays(&$scheduleDates)
	{
		$dates = array();
		foreach ($scheduleDates as $date => $scheduleDate) {
			$dates[] = timezone_date($date . '000000', true, 'YmdHis');
		}

		$container =& DIContainerFactory::getContainer();
		$holidayView =& $container->getComponent('holidayView');
		$holidays = $holidayView->getHolidays($dates);

		$search = array(
			'scheduler_weekday',
			'scheduler_saturday',
			'scheduler_sunday'
		);
		$replace = 'scheduler_holiday';
		foreach ($holidays as $date => $summary) {
			$scheduleDates[$date]['dateClassName'] = $replace;
			if (empty($scheduleDates[$date]['timeDatas'])) {
				continue;
			}

			foreach ($scheduleDates[$date]['timeDatas'] as $index => $timeData) {
				if (isset($timeData['startTimeClassName'])) {
					$startTimeClassName =& $scheduleDates[$date]['timeDatas'][$index]['startTimeClassName'];
					$startTimeClassName = str_replace($search, $replace, $startTimeClassName);

					$endTimeClassName =& $scheduleDates[$date]['timeDatas'][$index]['endTimeClassName'];
					$endTimeClassName = str_replace($search, $replace, $endTimeClassName);
				}
			}
		}
	}
}
?>