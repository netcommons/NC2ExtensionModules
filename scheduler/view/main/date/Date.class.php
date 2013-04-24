<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジューラ時間の表示
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_View_Main_Date extends Action
{
	// リクエストパラメータを受け取るため
	var $dateIteration = null;

	// 値をセットするため
	var $scheduleDate= null;

	/**
	 * execute実行
	 *
	 * @access  public
	 */
	function execute()
	{
		$this->scheduleDate = array();

		return 'success';
	}
}
?>