<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画のアップロードとチェック
 * 必須チェック
 *  リクエストパラメータ
 *  var $registration_id = null;
 *  var $items = null;
 *
 * @package     NetCommons.validator
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Validator_ItemUpload extends Validator
{
    /**
     * validate実行
     *
     * @param   mixed   $attributes チェックする値
     *                  
     * @param   string  $errStr     エラー文字列(未使用：エラーメッセージ固定)
     * @param   array   $params     オプション引数
     * @return  string  エラー文字列(エラーの場合)
     * @access  public
     */

    function validate($attributes, $errStr, $params)
    {
		// container取得
		$container =& DIContainerFactory::getContainer();
		$db =& $container->getComponent("DbObject");
		$fileUpload =& $container->getComponent("FileUpload");
		$uploadsAction =& $container->getComponent("uploadsAction");

		$filterChain =& $container->getComponent("FilterChain");
		$smartyAssign =& $filterChain->getFilterByName("SmartyAssign");
		$multimediaAction =& $container->getComponent("multimediaAction");

		if(!isset($attributes['multimedia_id']) || !isset($attributes['album_id']) || !isset($attributes['privacy'])) {
			return $smartyAssign->getLang("_invalid_input");
		}

		//ffmpegのチェック
		if(!$multimediaAction->hasFfmpeg()) {
			return sprintf($smartyAssign->getLang("multimedia_item_upload_video_no_ffmpeg_error"), MULTIMEDIA_FFMPEG_PATH);
		}

		//MP4Boxのチェック
		if(!$multimediaAction->hasMp4box()) {
			return sprintf($smartyAssign->getLang("multimedia_item_upload_video_no_mp4box_error"), MULTIMEDIA_MP4BOX_PATH);
		}

		// PHP GDライブラリのチェック
		if(!function_exists("gd_info")) {
			return $smartyAssign->getLang("multimedia_item_upload_video_no_gd_error");
		}

		//アップロードしたファイルの処理
		$files = $uploadsAction->uploads();
		$params = array(
			"album_id" => $attributes['album_id']
		);

		$itemSequence = $db->maxExecute("multimedia_item", "item_sequence", $params);
		$actionChain =& $container->getComponent("ActionChain");
		$action_name = $actionChain->getCurActionName();
		$pathList = explode("_", $action_name);
		$dirname = $pathList[0];
		$file_path = $dirname."/";

		foreach($files as $file) {
			$itemSequence++;
			$file_dir = FILEUPLOADS_DIR.$file_path;

			//動画パスを定義
			$temp_video_path = null;	// MP4Box変換前
			$video_path = null;			// MP4Box変換後

			$item_name = str_replace(".". $file['extension'], "", $file['physical_file_name']);
			$temp_video_path =  $file_dir . $item_name . "_temp.mp4";	// MP4Box変換前
			$video_path =  $file_dir . $item_name . ".mp4";				// MP4Box変換後

			if($file['extension'] != "mp4") {
				//動画変換実施
				$str_cmd = MULTIMEDIA_FFMPEG_PATH . " -y -i " . $file_dir . $file['physical_file_name']
							." " . MULTIMEDIA_FFMPEG_OPTION . " " . $temp_video_path . " 2>&1";
				exec($str_cmd, $arr, $ret);

				//変換エラー時
				if($ret != 0) {
					$uploadsAction->delUploadsById($file['upload_id']);
					return sprintf($smartyAssign->getLang("multimedia_item_upload_video_convert_error"), $ret);
				}else {
					//元動画ファイルを削除
					unlink($file_dir.$file['physical_file_name']);
				}
			}
			//MP4のアップロード時
			else {
				// ファイル名称変更
				rename($file_dir . $file['physical_file_name'], $temp_video_path);
			}

			//変換後の動画情報を取得
			$str_cmd = MULTIMEDIA_FFMPEG_PATH . " -i " . $temp_video_path . " 2>&1";
			exec($str_cmd, $stuout, $ret);

			//情報を取得出来なかった場合
			if($ret != 1) {
				//ファイル削除(MP4Box変換前動画)
				$multimediaAction->deleteFiles(array($temp_video_path));
				return $smartyAssign->getLang("multimedia_item_upload_video_information_error");
			}

			//動画情報から時間、サイズを取得
			foreach($stuout as $line) {
				//時間を取得(フォーマット：Duration: 00:00:00.0)
				preg_match("/Duration: [0-9]{2}:[0-9]{2}:[0-9]{2}\.\d+/s", $line, $matches);

				//時間を取得出来た場合
				if(count($matches) > 0) {
					//「:」で文字列分割
					$result_line = split(":",$matches[0]);

					//動画の時間を計算
					$duration = intval(trim($result_line[1])) * 3600 + intval($result_line[2]) * 60 + $result_line[3];
					break;
				}
			}

			//キャプチャ画像パスを定義
			$captuer_image_path = $file_dir . $file['upload_id'] . MULTIMEDIA_MOVIE_CAPTUER_NAME;
			//サムネイル画像パスを定義
			$thumbmail_image_path = $file_dir.$file['upload_id'] . MULTIMEDIA_MOVIE_THUMBNAIL_NAME;

			//キャプチャ画像切り出し
			//切り出し定義秒数で分岐
			switch(MULTIMEDIA_THUMBNAIL_SECONDS) {
				//切り出し定義秒数が動画秒数未満である場合、切り出し定義秒数で画像切り出し
				case MULTIMEDIA_THUMBNAIL_SECONDS < intval($duration):
					$str_cmd = MULTIMEDIA_FFMPEG_PATH . " -i " . $temp_video_path . " -ss " . MULTIMEDIA_THUMBNAIL_SECONDS . " -vframes 1 -f image2 " . $captuer_image_path . " 2>&1";
					break;
				//切り出し定義秒数が動画秒数以上である場合、動画の半分の秒数で画像切り出し(切り出し定義秒数が未設定の場合も含む)
				default:
					$str_cmd = MULTIMEDIA_FFMPEG_PATH . " -i " . $temp_video_path . " -ss " . intval($duration /2) . " -vframes 1 -f image2 " . $captuer_image_path . " 2>&1";
					break;
			}

			exec($str_cmd, $stuout, $ret);

			//画像を作成出来なかった場合
			if($ret != 0) {
				//ファイル削除(MP4Box変換前動画)
				$multimediaAction->deleteFiles(array($temp_video_path));
				return $smartyAssign->getLang("multimedia_item_upload_thmbnail_create_error");
			}

			//キャプチャ画像サイズ等取得
			list($width_orig, $height_orig, $type, $attr) = GetImageSize($captuer_image_path);

			//比率等の計算
			$scale = min(MULTIMEDIA_MOVIE_THUMBNAIL_WIDTH / $width_orig, MULTIMEDIA_MOVIE_THUMBNAIL_HEIGHT / $height_orig);
			$width = (int)($width_orig * $scale);
			$height = (int)($height_orig * $scale);
			$delta_width = (int)((MULTIMEDIA_MOVIE_THUMBNAIL_WIDTH - $width) / 2);
			$delta_height = (int)((MULTIMEDIA_MOVIE_THUMBNAIL_HEIGHT - $height) / 2);

			//キャプチャ画像のキャンバスを生成
			$image_orig = imagecreatefromjpeg($captuer_image_path);

			//キャンバス作成に失敗した場合
			if($image_orig === false) {
				//ファイル削除(MP4Box変換前動画、キャプチャ動画)
				$multimediaAction->deleteFiles(array($temp_video_path, $captuer_image_path));
				return $smartyAssign->getLang("multimedia_item_upload_thmbnail_create_error");
			}

			$gb_result = false;

			//サムネイル画像のキャンバスを作成
			$image_p = imagecreatetruecolor(MULTIMEDIA_MOVIE_THUMBNAIL_WIDTH, MULTIMEDIA_MOVIE_THUMBNAIL_HEIGHT);

			//サムネイル画像のキャンバス作成に失敗した場合
			if($image_orig === false) {
				//ファイル削除(MP4Box変換前動画、キャプチャ動画)
				$multimediaAction->deleteFiles(array($temp_video_path, $captuer_image_path));
				return $smartyAssign->getLang("multimedia_item_upload_thmbnail_create_error");
			}

			//黒色を取得(0が取得される)
			$back = imagecolorallocate($image_p, 0, 0, 0);

			//黒色の取得に失敗した場合(5.1.3以降はfalseを返し、その前までのバージョンでは-1を返す)
			if($back === false || $back === -1) {
				//ファイル削除(MP4Box変換前動画、キャプチャ動画)
				$multimediaAction->deleteFiles(array($temp_video_path, $captuer_image_path));
				return $smartyAssign->getLang("multimedia_item_upload_thmbnail_create_error");
			}

			//キャンバスを黒色で塗りつぶす
			$gb_result = imagefill($image_p, 0, 0, $back);

			//塗りつぶしに失敗した場合
			if($gb_result === false) {
				//ファイル削除(MP4Box変換前動画、キャプチャ動画)
				$multimediaAction->deleteFiles(array($temp_video_path, $captuer_image_path));
				return $smartyAssign->getLang("multimedia_item_upload_thmbnail_create_error");
			}

			//キャプチャ画像を縮小し、サムネイル画像のキャンバスに貼り付け
			$gb_result = imagecopyresampled($image_p, $image_orig, $delta_width, $delta_height, 0, 0, $width, $height, $width_orig, $height_orig);

			//貼り付けに失敗した場合
			if($gb_result === false) {
				//ファイル削除(MP4Box変換前動画、キャプチャ動画)
				$multimediaAction->deleteFiles(array($temp_video_path, $captuer_image_path));
				return $smartyAssign->getLang("multimedia_item_upload_thmbnail_create_error");
			}

			//サムネイル画像出力
			$gb_result = imageJpeg($image_p, $thumbmail_image_path);

			//サムネイル画像出力に失敗した場合
			if($gb_result === false) {
				//ファイル削除(MP4Box変換前動画、キャプチャ動画)
				$multimediaAction->deleteFiles(array($temp_video_path, $captuer_image_path));
				return $smartyAssign->getLang("multimedia_item_upload_thmbnail_create_error");
			}

			//GDリソースを解放(解放できない場合でも、サムネイル画像は作成完了しているため、エラーとして検知しない)
			imageDestroy($image_orig);
			imageDestroy($image_p);

			//ファイル削除(キャプチャ画像)
			$multimediaAction->deleteFiles(array($captuer_image_path));

			//MP4Boxで動画変換実施(動画情報、キャプチャ画像切り出し後ではないと変換できない)
			$str_cmd = MULTIMEDIA_MP4BOX_PATH . " -add " . $temp_video_path ." -brand mmp4:1 -new " . $video_path . " 2>&1";
			exec($str_cmd, $arr, $ret);

			//変換エラー時
			if($ret != 0) {
				//ファイル削除(MP4Box変換前動画、サムネイル動画)
				$multimediaAction->deleteFiles(array($temp_video_path, $thumbmail_image_path));
				return sprintf($smartyAssign->getLang("multimedia_item_upload_video_convert_error"), $ret);
			}else {
				//ファイル削除(MP4Box変換前動画)
				$multimediaAction->deleteFiles(array($temp_video_path));
			}

			//動画情報更新
			$file['physical_file_name'] = $item_name.".mp4";
			$upload_params = array(
				"file_name" => $file['physical_file_name'],
				"physical_file_name" => $file['physical_file_name'],
				"file_size" => filesize($video_path),
				"extension" => "mp4",
				"mimetype" => "video/mp4"
			);
			$where_params = array(
				"upload_id" => $file['upload_id']
			);
			if(!$uploadsAction->updUploads($upload_params, $where_params)) {
				//ファイル削除(MP4Box変換後動画、サムネイル動画)
				$multimediaAction->deleteFiles(array($video_path, $thumbmail_image_path));
				return $smartyAssign->getLang("multimedia_item_upload_video_update_error");
			}

			$session =& $container->getComponent("Session");
			$request =& $container->getComponent("Request");
			$multimedia_obj = $request->getParameter("multimedia_obj");
			$auth_id = $session->getParameter("_auth_id");
			if($auth_id >= _AUTH_CHIEF || $multimedia_obj['confirm_flag'] == _OFF) {
				$agree_flag = _ON;
			}else {
				$agree_flag = _OFF;
			}
			$params = array(
				"album_id" => $attributes['album_id'],
				"multimedia_id" => $attributes['multimedia_id'],
				"item_name" => empty($attributes['item_name'])?str_replace(".". $file['extension'], "", $file['file_name']):$attributes['item_name'],
				"agree_flag" => $agree_flag,
				"item_sequence" => $itemSequence,
				"upload_id" => $file['upload_id'],
				"duration" => $duration,
				"file_path" => $file_path,
				"item_path" => "?". ACTION_KEY. "=". $file['action_name']. "&upload_id=". $file['upload_id'],
				"item_description" => $attributes['item_description'],
				"privacy" => $attributes['privacy']
			);

			$item_id = $db->insertExecute("multimedia_item", $params, true, "item_id");

			if (!$item_id) {
				//ファイル削除(MP4Box変換後動画、サムネイル動画)
				$multimediaAction->deleteFiles(array($video_path, $thumbmail_image_path));
				return $smartyAssign->getLang("multimedia_item_upload_video_insert_error");
			}
			if(!$multimediaAction->setItemTag($item_id, $attributes['item_tag'])) {
				//ファイル削除(MP4Box変換後動画、サムネイル動画)
				$multimediaAction->deleteFiles(array($video_path, $thumbmail_image_path));
				return $smartyAssign->getLang("multimedia_item_upload_video_tag_error");
			}

			if(!$multimediaAction->setItemCount($attributes['album_id'], 1)) {
				return $smartyAssign->getLang("multimedia_item_upload_video_count_error");
			}

			$result = $db->updateExecute("multimedia_album", array("item_upload_time" => timezone_date()), array("album_id" => $attributes['album_id']), true);
			if($result === false) {
				return $smartyAssign->getLang("multimedia_item_upload_video_insert_error");
			}
		}

		return;
	}
}
?>
