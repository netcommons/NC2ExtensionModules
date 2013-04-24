<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 入力用日程チェックバリデータクラス
 *
 * @package	  NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright 2006-2009 NetCommons Project
 * @license	  http://www.netcommons.org/license.txt  NetCommons License
 * @project	  NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Scheduler_Validator_Date extends Validator
{
	/**
	 * 入力用日程チェックバリデータ
	 *
	 * @param   mixed   $attributes チェックする値
	 * @param   string  $errStr	 エラー文字列
	 * @param   array   $params	 オプション引数
	 * @return  string  エラー文字列(エラーの場合)
	 * @access  public
	 */
	function validate($attributes, $errStr, $params)
	{
		if ($attributes['entry_type'] == SCHEDULER_ENTRY_CALENDAR
			&& empty($attributes['date_id'])) {
			return;
		}

		$dateIds = $attributes['date_id'];
		if (!is_array($attributes['date_id'])) {
			return $errStr;
		}

		$container =& DIContainerFactory::getContainer();
		$request =& $container->getComponent('Request');
		$session =& $container->getComponent('Session');

		$alldayFlags = $attributes['allday_flag'];
		$startDates = $attributes['start_date'];
		$startHours = $attributes['start_hour'];
		$startMinutes = $attributes['start_minute'];
		$endDates = $attributes['end_date'];
		$endHours = $attributes['end_hour'];
		$endMinutes = $attributes['end_minute'];
		$timezoneOffsets = $attributes['timezone_offset'];

		$dateValidatorKeySuffix = '.date';
		$timeValidatorKeySuffix = '.time';
		$requiredValidatorKeySuffix = '.required';
		$dateAttributeValue = '1:lang._invalid_date,lang.scheduler_schedule_date';
		$requiredAttributeValue = '1:lang._required,lang.scheduler_schedule_date';
		$dateAttribute = array();
		foreach ($dateIds as $iteration => $dateId) {
			$attributeName = 'start_date' . $iteration;
			$request->setParameter($attributeName, $startDates[$iteration]);
			$dateAttributeKey = $attributeName . $dateValidatorKeySuffix;
			$dateAttribute[$dateAttributeKey] = $dateAttributeValue;
			$dateAttributeKey = $attributeName . $requiredValidatorKeySuffix;
			$dateAttribute[$dateAttributeKey] = $requiredAttributeValue;

			if (!empty($alldayFlags[$iteration])) {
				$startHours[$iteration] = '00';
				$startMinutes[$iteration] = '00';
				$endHours[$iteration] = '24';
				$endMinutes[$iteration] = '00';
			}
			$attributeName = 'start_time' . $iteration;
			$request->setParameter($attributeName, $startHours[$iteration] . $startMinutes[$iteration] . '00');
			$dateAttributeKey = $attributeName . $timeValidatorKeySuffix;
			$dateAttribute[$dateAttributeKey] = $dateAttributeValue;

			/*
			 * 終了日指定未対応
			$attributeName = 'end_date' . $iteration;
			$request->setParameter($attributeName, $endDates[$iteration]);
			$dateAttributeKey = $attributeName . $dateValidatorKeySuffix;
			$dateAttribute[$dateAttributeKey] = $dateAttributeValue;
			$dateAttributeKey = $attributeName . $requiredValidatorKeySuffix;
			$dateAttribute[$dateAttributeKey] = $dateAttributeValue;
			*/

			$temporaryEndHours = null;
			if ($endHours[$iteration] == '24') {
				$temporaryEndHours = $endHours[$iteration];
				$endHours[$iteration] = '00';
			}
			$attributeName = 'end_time' . $iteration;
			$request->setParameter($attributeName, $endHours[$iteration] . $endMinutes[$iteration] . '00');
			$dateAttributeKey = $attributeName . $timeValidatorKeySuffix;
			$dateAttribute[$dateAttributeKey] = $dateAttributeValue;
			if (isset($temporaryEndHours)) {
				$endHours[$iteration] = $temporaryEndHours;
			}
		}
		$validatorManager =& new ValidatorManager();
		$validatorManager->execute($dateAttribute);

		$actionChain =& $container->getComponent('ActionChain');
		$errorList =& $actionChain->getCurErrorList();
		if ($errorList->isExists()) {
			return;
		}

		$startFullDates = array();
		$endFullDates = array();
		foreach ($dateIds as $iteration => $dateId) {
			/*
			 * タイムゾーン指定未対応
			 */
			if (empty($timezoneOffsets[$iteration])) {
				$timezoneOffsets[$iteration] = $session->getParameter('_timezone_offset');
			}
			if (!is_numeric($timezoneOffsets[$iteration])) {
				return $errStr;
			}

			$attributeName = 'start_date' . $iteration;
			$startFullDate = $request->getParameter($attributeName)
							. $startHours[$iteration]
							. $startMinutes[$iteration]
							. '00';
			if (strlen($startFullDate) != 14) {
				return $errStr;
			}

			/*
			 * 終了日指定未対応
			$attributeName = 'end_date' . $iteration;
			$endFullDate = $request->getParameter($attributeName)
						. $endHours[$iteration]
						. $endMinutes[$iteration]
						. '00';
			*/
			$endFullDate = $request->getParameter($attributeName)
							. $endHours[$iteration]
							. $endMinutes[$iteration]
							. '00';
			if (strlen($endFullDate) != 14) {
				return $errStr;
			}

			if ($endFullDate < $startFullDate) {
				$endFullDate = $startFullDate;
			}

			$startFullDates[$iteration] = $startFullDate;
			$endFullDates[$iteration] = $endFullDate;
		}
		$request->setParameter('startFullDate', $startFullDates);
		$request->setParameter('endFullDate', $endFullDates);

		/*
		 * タイムゾーン指定未対応
		 */
		$request->setParameter('timezone_offset', $timezoneOffsets);

		return;
	}
}
?>