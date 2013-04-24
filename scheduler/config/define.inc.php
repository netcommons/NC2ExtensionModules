<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * スケジュール情勢定数定義
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */

define('SCHEDULER_DISPLAY_LIST', 1);
define('SCHEDULER_DISPLAY_SELECT', 2);

define('SCHEDULER_ENTRY_INPUT', 1);
define('SCHEDULER_ENTRY_CALENDAR', 2);

define('SCHEDULER_DAY_OF_WEEK_SEPARATOR', '|');
define('SCHEDULER_CALENDAR_SESSION_KEY', 'scheduler_calendars');

define('SCHEDULER_REPLY_NONE', 0);
define('SCHEDULER_REPLY_OK', 1);
define('SCHEDULER_REPLY_NG', 2);
define('SCHEDULER_REPLY_FINE', 3);

define('SCHEDULER_CALENDAR_MORNING_START', '0800');
define('SCHEDULER_CALENDAR_MORNING_END', '1200');
define('SCHEDULER_CALENDAR_AFTERNOON_START', '1200');
define('SCHEDULER_CALENDAR_AFTERNOON_END', '1700');
define('SCHEDULER_CALENDAR_EVENING_START', '1700');
define('SCHEDULER_CALENDAR_EVENING_END', '2300');
?>