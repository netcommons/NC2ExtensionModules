<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 期限チェックバリデータクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Validator_Period extends Validator
{
	/**
	 * 期限チェックバリデータ
	 *
	 * @param   mixed   $attributes チェックする値
	 * @param   string  $errStr	 エラー文字列
	 * @param   array   $params	 オプション引数
	 * @return  string  エラー文字列(エラーの場合)
	 * @access  public
	 */
	function validate($attributes, $errStr, $params)
	{
		$container =& DIContainerFactory::getContainer();
		$request =& $container->getComponent('Request');

		if (empty($attributes['period_checkbox'])) {
			$request->setParameter('period', '');
			return;
		}

		if (!empty($attributes['period'])) {
			$attributes['period_hour'] = '24';
			$attributes['period_minute'] = '00';

			$attributes['period_hour'] = intval($attributes['period_hour']);
			$attributes['period_minute'] = intval($attributes['period_minute']);
			$period = $attributes['period'].
						sprintf('%02d', $attributes['period_hour']).
						sprintf('%02d', $attributes['period_minute']).
						'00';
		} else {
			$startFullDates = $request->getParameter('startFullDate');
			if (empty($startFullDates)) {
				return $errStr;
			}
			$minStartDate = min($startFullDates);
			$period = intval(substr($minStartDate, 0, 8) . '000000');
		}

		$period = timezone_date($period, true);
		$gmt = timezone_date();

		if ($period < $gmt) {
			return $errStr;
		}
		$request->setParameter('period', $period);

		return;
	}
}
?>