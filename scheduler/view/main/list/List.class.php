<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジューラ一覧画面表示アクションクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_View_Main_List extends Action
{
	// リクエストパラメータを受け取るため
	var $scheduler = null;
	var $block_id = null;
	var $visible_row = null;
	var $pageNumber = null;
	var $module_id = null;

	// 使用コンポーネントを受け取るため
	var $schedulerView = null;
	var $session = null;
	var $configView = null;

	// 値をセットするため
	var $schedule = null;
	var $schedules = null;
	var $scheduleCount = null;
	var $config = null;
	var $pagePrevious = null;
	var $pageNext = null;
	var $pageStart = null;
	var $pageEnd = null;

	/**
	 * スケジューラ一覧画面表示アクション
	 *
	 * @access  public
	 */
	function execute()
	{
		if (empty($this->scheduler)) {
			$this->scheduler =& $this->schedulerView->getCurrentScheduler();
		}
		if (empty($this->scheduler)) {
			return 'error';
		}

		if (!isset($this->visible_row)) {
			$this->visible_row = $this->session->getParameter('scheduler_visible_row' . $this->block_id);
		}
		if (!isset($this->visible_row)) {
			$this->visible_row = $this->scheduler['visible_row'];
		}
		$this->visible_row = intval($this->visible_row);
		$this->session->setParameter('scheduler_visible_row' . $this->block_id, $this->visible_row);

		$this->scheduleCount =& $this->schedulerView->getScheduleCount();
		if (empty($this->scheduleCount)) {
			return 'success';
		}

		$this->config = $this->configView->getConfig($this->module_id, false);

		$this->schedules =& $this->schedulerView->getSchedules();
		if ($this->schedules === false) {
			return 'error';
		}

		$pageCount = 0;
		if (!empty($this->visible_row)) {
			$pageCount = ceil($this->scheduleCount / $this->visible_row);
		}
		if ($pageCount <= 1) {
			return 'success';
		}

		$this->pageNumber = intval($this->pageNumber);
		$this->pagePrevious = $this->pageNumber - 1;
		$this->pageNext = $this->pageNumber + 1;

		$visiblePage = $this->session->getParameter('scheduler_visible_page' . $this->block_id);
		if (!isset($visiblePage)) {
			$visiblePage = $this->config['visible_page']['conf_value'];
			$this->session->setParameter('scheduler_visible_page' . $this->block_id, $visiblePage);
		}
		if (empty($visiblePage)) {
			$visiblePage = 1;
		}

		$this->pageStart = $this->pageNumber - $visiblePage + 1;
		if ($this->pageStart < 0){
			$this->pageStart = 0;
		}

		$this->pageEnd = $this->pageNumber + $visiblePage;
		if ($this->pageEnd > $pageCount) {
			$this->pageEnd = $pageCount;
		}

		return 'success';
	}
}
?>