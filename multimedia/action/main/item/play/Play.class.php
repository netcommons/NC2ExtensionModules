<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画再生
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Action_Main_Item_Play extends Action
{
	// 使用コンポーネントを受け取るため
	var $session = null;
	var $uploadsView = null;
	var $db = null;

	// バリデートによりセット
	var $item = null;

	/**
	 * [[機能説明]]
	 *
	 * @access  public
	 */
	function execute()
	{
		list($pathname,$filename,$physical_file_name, $cache_flag) = $this->uploadsView->downloadCheck($this->item['upload_id'], null);
		if (!isset($pathname)) {
			exit;
		}

		clearstatcache();
		$caching = true;
		if ($this->item['privacy'] > _AUTH_GUEST) {
			$caching = false;
		}

		//Rangeヘッダが設定されている場合(iOS)
		if(isset($_SERVER['HTTP_RANGE'])) {
			//動画ファイルサイズ取得
			$file = $pathname . $filename;
			$file_size = filesize($file) - 1;	//Rangeヘッダのオフセットが0であるため、-1
//error_log(print_r($file_size, true)."<-file_size\n\n", 3, LOG_DIR."/error.log");

			//Rangeヘッダを解析
			list($range_header, $range) = explode('=', $_SERVER['HTTP_RANGE']);
			list($range_offset, $range_limit) = explode('-', $range);
//error_log(print_r($_SERVER['HTTP_RANGE'], true)."<-p\n\n", 3, LOG_DIR."/error.log");
			$playback_session_id_array = array();

			//Rangeヘッダが0からファイル終端の場合(ファイル存在確認後、1度のみリクエストされる)
			if($range_offset == 0 && $range_limit == $file_size - 1) {
				//再生回数更新
				$params = array(
					$this->item['item_id']
				);
				$sql = "UPDATE {multimedia_item} ".
						"SET item_play_count = item_play_count + 1 ".
						"WHERE item_id = ? ";
				$result = $this->db->execute($sql, $params);
				if ($result === false) {
					return 'error';
				}
			}

			// Rangeヘッダの終了値が省略されている場合はファイルサイズを設定
			if(is_null($range_limit)) {
				$range_limit = $file_size;
			}

			//Rangeヘッダが指定するコンテンツの長さを設定(Rangeヘッダのオフセットが0であるため、+1)
			$content_length = $range_limit - $range_offset + 1;

			//Rangeヘッダで指定された範囲の動画を取得
			$content = file_get_contents($file, FILE_BINARY, null, $range_offset, $range_limit);

			//ヘッダ出力
			@ob_end_clean();
			header('Content-type: video/mp4');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Content-Transfer-Encoding: binary');
			header('Accept-Ranges: bytes');
			header('HTTP/1.1 206 Partial Content');
			header('Content-Length: ' . $content_length);
			header('Content-Range: bytes ' . $range_offset . '-' . $range_limit . '/' . $file_size);
//			header("Etag: \"" . md5( $_SERVER["REQUEST_URI"] ) . $file_size . "\"" );
			header("Last-Modified: " . gmdate( "D, d M Y H:i:s", filemtime($file)) . " GMT");

// 以下、webapp/components/uploads/View.class.php の function _headerOutput より
			$stats = stat($file);
			$etag = sprintf( '"%x-%x-%x"', $stats['ino'], $stats['size'], $stats['mtime'] );
			header('Etag: '.$etag);

			if($caching == true) {
				// 1Week
				header("Cache-Control: max-age=604800, public");
				header('Pragma: cache'); //no-cache以外の文字列をセット
				$offset = 60 * 60 * 24 * 7; //  1Week
				header('Expires: '.gmdate('D, d M Y H:i:s', time() + $offset).' GMT');
				if (isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) &&
					stripcslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) == $etag ) {
					header( 'HTTP/1.1 304 Not Modified' );
					$status_code = "304";
				}
			//} else if (isset($_SERVER['HTTPS']) && stristr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
			//  // IE + サイト全体SSLの場合、ダウンロードが正常に行われない。
			//  // ダウンロードさせるためには、以下コメントをはずす必要があるが、
			//  // アップロードした画像ファイル等をローカルキャッシュにとられてしまう弊害がある。
			//	// 1Week
			//	header("Cache-Control: max-age=604800, public");
			//	header('Pragma: cache'); //no-cache以外の文字列をセット
			//	$offset = 60 * 60 * 24 * 7; //  1Week
			//	header('Expires: '.gmdate('D, d M Y H:i:s', time() + $offset).' GMT');
			} else {
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Pragma: no-cache");
			}
// 以上、webapp/components/uploads/View.class.php の function _headerOutput より

//error_log(print_r($filename, true)."<-filename\n\n", 3, LOG_DIR."/error.log");
//error_log(print_r($file_size, true)."<-file_size\n\n", 3, LOG_DIR."/error.log");
//error_log(print_r($content_length, true)."<-content_length\n\n", 3, LOG_DIR."/error.log");

			//ファイル出力
//			echo $content;
			$fp = fopen($file, "rb");
			fseek($fp, $range_offset, SEEK_SET);
			echo fread($fp, $content_length);
			exit;
		}
		//Rangeヘッダが設定されていない場合
		else {
			//HTTP_USER_AGENTが設定されている場合のみ再生数をカウント(Android Chrome対策)
			if(isset($_SERVER['HTTP_USER_AGENT'])) {
				//再生回数更新
				$params = array(
					$this->item['item_id']
				);
				$sql = "UPDATE {multimedia_item} ".
						"SET item_play_count = item_play_count + 1 ".
						"WHERE item_id = ? ";
				$result = $this->db->execute($sql, $params);
				if ($result === false) {
					return 'error';
				}
			}

			//動画出力
			$this->uploadsView->headerOutput($pathname, $filename, $physical_file_name, $caching);
			exit;
		}
	}
}
?>