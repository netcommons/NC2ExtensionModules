<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * バナーアップロード画像チェックバリデータクラス
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Banner_Validator_UploadImage extends Validator
{
	/**
	 * バナーアップロード画像チェック
	 *
	 * @param mixed $attributes チェックする値
	 * @param string $errStr エラー文字列
	 * @param array $params オプション引数
	 * @return string エラー文字列(エラーの場合)
	 * @access public
	 */
	function validate($attributes, $errStr, $params)
	{
		if ($attributes['banner_type'] != BANNER_TYPE_IMAGE_VALUE) {
			return;
		}

		$container =& DIContainerFactory::getContainer();
		$fileUpload =& $container->getComponent('FileUpload');
		$errorMessages = $fileUpload->getErrorMes();
		if (empty($errorMessages)) {
			return;
		}

		$errors = $fileUpload->getError();
		if ($errors[0] == UPLOAD_ERR_NO_FILE
			&& !empty($attributes['banner_id'])) {
			return;
		}

		$errStr = implode('<br />', $errorMessages);
		return $errStr;
	}
}
?>