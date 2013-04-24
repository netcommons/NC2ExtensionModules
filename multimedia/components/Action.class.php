<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画配信登録コンポーネント
 *
 * @package     NetCommons Components
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Components_Action 
{
	/**
	 * @var DBオブジェクトを保持
	 *
	 * @access	private
	 */
    var $_db = null;

    /**
	 * @var リクエストオブジェクトを保持
	 *
	 * @access	private
	 */
	var $_request = null;
	
	/**
	 * コンストラクター
	 *
	 * @access	public
	 */
    function Multimedia_Components_Action()
	{
		$container =& DIContainerFactory::getContainer();
		$this->_db =& $container->getComponent("DbObject");
		$this->_request =& $container->getComponent("Request");
	}
	
	function hasFfmpegLib() {
		$extension_fullname = PHP_EXTENSION_DIR . "/" . MULTIMEDIA_EXTENSION.".".PHP_SHLIB_SUFFIX;
    	if (!extension_loaded(MULTIMEDIA_EXTENSION)) {
	    	//dl($extension_soname) or die("Can't load extension $extension_fullname\n");
	    	return false;
		}
		return true;
	}
	
	function deleteMultimedia() {
		$params = array(
			"multimedia_id" => $this->_request->getParameter("multimedia_id")
		);
		
    	if (!$this->_db->deleteExecute("multimedia_block", $params)) {
    		return false;
    	}
    	
    	if (!$this->_db->deleteExecute("multimedia_comment", $params)) {
    		return false;
    	}

		$sql = "SELECT item_id, upload_id, file_path ".
					"FROM {multimedia_item} ".
					"WHERE multimedia_id = ?";
		$items = $this->_db->execute($sql, $params);
		if ($items === false) {
			$this->_db->addError();
			return false;
		}
		if (!$this->deleteItemFile($items)) {
    		return false;
    	}
    	if (!$this->_db->deleteExecute("multimedia_item", $params)) {
    		return false;
    	}
    	if (!$this->_db->deleteExecute("multimedia_album", $params)) {
    		return false;
    	}
    	if (!$this->_db->deleteExecute("multimedia", $params)) {
    		return false;
    	}

		return true;
	}

	//動画ファイル削除
	function deleteItemFile($items)
	{
		if (empty($items)) {
			return true;
		}
		
		$container =& DIContainerFactory::getContainer();
		$commonMain =& $container->getComponent("commonMain");
		$uploads =& $commonMain->registerClass(WEBAPP_DIR.'/components/uploads/Action.class.php', "Uploads_Action", "uploadsAction");
		$item_ids = array();
		foreach ($items as $item) {
			if (!$uploads->delUploadsById($item['upload_id'])) {
				return false;
			}
			//動画のサムネイル画像があれば、削除
			$thumbnail_path = FILEUPLOADS_DIR.$item['file_path'].$item['upload_id']."_thumbnail.jpg";
			if(file_exists($thumbnail_path)) {
				@chmod($thumbnail_path, 0777);
				unlink($thumbnail_path);
			}
			//タグ削除
			$sql = "SELECT tag_id "
					. "FROM {multimedia_item_tag} "
					. "WHERE item_id = ?;";
			$params = array(
				"item_id" => $item['item_id']
			);
			$tag_id_string = $this->_db->execute($sql, $params, $limit = null, $offset = null, false, array($this, "_makeImplodeString"));
			if ($tag_id_string === false) {
				$this->_db->addError();
				return false;
			}
			if (!empty($tag_id_string)) {
				if(!$this->_db->deleteExecute("multimedia_item_tag", $params)) {
					return false;
				}
			}
			$this->_tagReCount($tag_id_string);
	    	
			if (!$this->_db->deleteExecute("multimedia_item_tag", $params)) {
	    		return false;
	    	}
		}

		return true;
	}
	
	//カテゴリ削除
	function deleteAlbum($multimedia_id, $album_id) {
		$params = array(
			"album_id" => $album_id
		);
		
		if (!$this->_db->deleteExecute("multimedia_comment", $params)) {
    		return false;
    	}
		
		$sql = "SELECT item_id, upload_id, file_path ".
					"FROM {multimedia_item} ".
					"WHERE album_id = ?";
		$items = $this->_db->execute($sql, $params);
		if ($items === false) {
			$this->_db->addError();
			return false;
		}
		if (!$this->deleteItemFile($items)) {
    		return false;
    	}
    	if (!$this->_db->deleteExecute("multimedia_item", $params)) {
    		return false;
    	}
   	
		$sql = "SELECT album_sequence, upload_id ".
					"FROM {multimedia_album} ".
					"WHERE album_id = ?";
		$albums = $this->_db->execute($sql, $params);
		if ($albums === false) {
			$this->_db->addError();
			return false;
		}

    	if (!$this->_db->deleteExecute("multimedia_album", $params)) {
    		return false;
    	}

		if(!empty($albums[0]['upload_id'])) {
			$container =& DIContainerFactory::getContainer();
			$commonMain =& $container->getComponent("commonMain");
			$uploads =& $commonMain->registerClass(WEBAPP_DIR.'/components/uploads/Action.class.php', "Uploads_Action", "uploadsAction");
			if (!$uploads->delUploadsById($albums[0]['upload_id'])) {
				return false;
			}
		}

		$params = array(
			"multimedia_id" => $multimedia_id
		);
		$sequenceParam = array(
			"album_sequence" => $albums[0]['album_sequence']
		);
		if (!$this->_db->seqExecute("multimedia_album", $params, $sequenceParam)) {
			return false;
		}

		return true;
	}
	
	//動画削除
	function deleteItem($item_id) {
		$params = array(
			"item_id" => $item_id
		);
		
		//コメント削除
    	if (!$this->_db->deleteExecute("multimedia_comment", $params)) {
    		return false;
    	}

		//動画削除
    	$sql = "SELECT item_id, album_id, upload_id, file_path ". 
				"FROM {multimedia_item} ".
				"WHERE item_id = ?";
		$item = $this->_db->execute($sql, $params);
		if ($item === false) {
			$this->_db->addError();
			return false;
		}
		if (!$this->deleteItemFile($item)) {
    		return false;
    	}
    	if (!$this->_db->deleteExecute("multimedia_item", $params)) {
    		return false;
    	}
    	
    	if(!$this->setItemCount($item[0]['album_id'], -1)) {
    		return false;
    	}

		return true;
	}
	
	//アルバムの動画件数を計算
	function setItemCount($album_id, $count) {
		if(empty($album_id)) {
			return false;
		}
		
		$params = array(
			"album_id" => $album_id
		);
		
		$album = $this->_db->selectExecute("multimedia_album", $params);
		if(empty($album) || !isset($album[0])) {
			return false;
		}
		
		$sql = "UPDATE {multimedia_album} ".
				"SET item_count = item_count + ".($count)." ".
				"WHERE album_id = ?";
		$result = $this->_db->execute($sql, $params);
		if ($result === false) {
			$this->_db->addError();
			return false;
		}
		
		return true;
	}
	
	/**
	 * 動画タグデータを登録する
	 *
	 * @param string $item_id 対象の動画ID
	 * @return boolean	true or false
	 * @access	public
	 */
	function setItemTag($item_id, $item_tag) {
		if(empty($item_id)) {
			return false;
		}
		
		$container =& DIContainerFactory::getContainer();
		
		$tag_values = array();
		if (!empty($item_tag)) {
			$item_tag = str_replace('，', MULTIMEDIA_TAG_SEPARATOR, $item_tag);
			$item_tag = str_replace('、', MULTIMEDIA_TAG_SEPARATOR, $item_tag);
			$item_tag = str_replace('､', MULTIMEDIA_TAG_SEPARATOR, $item_tag);
			$tag_values = split(MULTIMEDIA_TAG_SEPARATOR, $item_tag);
		}

		foreach ($tag_values as $key => $value) {
			$value = preg_replace("/^[ 　]+/u","",$value);
			$value = preg_replace("/[ 　]+$/u","",$value);
			$tag_values[$key] = $value;
		}

		$tag_values = array_unique($tag_values);

		$sql = "SELECT R.tag_id "
				. "FROM {multimedia_item_tag} AS R "
				. "INNER JOIN {multimedia_tag} AS T "
				. "ON R.tag_id = T.tag_id "
				. "WHERE R.item_id = ? ";
		$params = array(
			$item_id
		);
		$tag_id_string = $this->_db->execute($sql, $params, $limit = null, $offset = null, false, array($this, "_makeImplodeString"));

		if ($tag_id_string === false) {
			$this->_db->addError();
			return false;
		}
		if (!empty($tag_id_string)) {
			$sql = "DELETE FROM {multimedia_item_tag} "
					. "WHERE item_id = ? "
					. "AND tag_id IN (" . $tag_id_string . ") ";
			$params = array(
				$item_id
			);
			if ($this->_db->execute($sql, $params) === false) {
				$this->_db->addError();
				return false;
			}
		}

		$sequence = 1;
		$tag_ids = array();
		foreach ($tag_values as $tag_value) {
			if (empty($tag_value)) {
				continue;
			}
			$sql = "SELECT tag_id "
					. "FROM {multimedia_tag} "
					. "WHERE tag_value = ? AND room_id = ? ";
			$params = array(
				$this->getSynonym($tag_value),
                $this->_request->getParameter("room_id")
			);
			$item_tag_ids = $this->_db->execute($sql, $params);
			if ($item_tag_ids === false) {
				$this->_db->addError();
				return false;
			}
			if (empty($item_tag_ids)) {
				$params = array(
					'tag_value' => $this->getSynonym($tag_value),
					'used_number' => 1
				);

				$tag_id = $this->_db->insertExecute('multimedia_tag', $params, true, 'tag_id');
				if (empty($tag_id)) {
					return false;
				}

			} else {
				$tag_id = $item_tag_ids[0]['tag_id'];
			}
			$tag_ids[] = $tag_id;

			$item_tag = array(
				'item_id' => $item_id,
				'tag_id' => $tag_id,
				'tag_value' => $tag_value,
				'sequence' => $sequence
			);
			if (!$this->_db->insertExecute('multimedia_item_tag', $item_tag, true)){
				return false;
			}
			$sequence++;
		}

		if (!empty($tag_id_string) && !empty($tag_ids)) {
			$tag_id_string .= ',';
		}
		$tag_id_string .= implode(',', $tag_ids);
		
		if (!$this->_tagReCount($tag_id_string)) {
			return false;
		}

		return true;
	}

	/**
	 * 動画タグの使用回数を再カウント。0件ならmultimedia_tagから削除。
	 *
	 * @param	string	$tagIDString		区切り文字
	 * @return	boolean true or false
	 * @access	private
	 */
	function _tagReCount($tag_id_string) {
		$counted_tag_ids = array();
		if (!empty($tag_id_string)) {
			$sql = "SELECT tag_id, "
						. "COUNT(DISTINCT item_id) AS tag_count "
					. "FROM {multimedia_item_tag} "
					. "WHERE tag_id IN (" . $tag_id_string . ") " 
					. "GROUP BY tag_id;";
			$tags = $this->_db->execute($sql);
			if ($tags === false) {
				$this->_db->addError();
				return false;
			}
			if(!empty($tags)) {
				foreach ($tags as $tag) {
					$tag_used_number = array(
						'tag_id' => $tag['tag_id'],
						'used_number' => $tag['tag_count']
					);
					if (!$this->_db->updateExecute('multimedia_tag', $tag_used_number, 'tag_id', true)) {
						return false;
					}
					
					$counted_tag_ids[] = $tag['tag_id'];
				}
			}
		}

		$tag_ids = explode(',', $tag_id_string);
		$tag_ids = array_diff($tag_ids, $counted_tag_ids);
		foreach ($tag_ids as $tag_id) {
			$tag_used_number = array(
				'tag_id' => $tag_id
			);
			if (!$this->_db->deleteExecute('multimedia_tag', $tag_used_number)) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * ADORecordSetの1カラム目を指定文字区切りの文字列にする
	 *
	 * @param	array	$recordSet	ADORecordSet
	 * @param	array	$glue		区切り文字
	 * @return array	指定文字区切りの文字列
	 * @access	private
	 */
	function &_makeImplodeString(&$recordSet, $glue = ",") {
		$string = "";
		if ($recordSet->EOF) {
			return $string;
		}
		
		while ($row = $recordSet->fetchRow()) {
			$string .= $row[0] . $glue;
		}
		if (!empty($glue)) {
			$string = substr($string, 0, strlen($glue) * -1);
		}

		return 	$string;	
	}
	
	function getSynonym($str) {
		//半角ｶﾅ(濁点付き)→全角カナに変換
		$replace_of = array('ｳﾞ', 'ｶﾞ', 'ｷﾞ', 'ｸﾞ',
							'ｹﾞ', 'ｺﾞ', 'ｻﾞ', 'ｼﾞ',
							'ｽﾞ', 'ｾﾞ', 'ｿﾞ', 'ﾀﾞ',
							'ﾁﾞ', 'ﾂﾞ', 'ﾃﾞ', 'ﾄﾞ',
							'ﾊﾞ', 'ﾋﾞ', 'ﾌﾞ', 'ﾍﾞ',
							'ﾎﾞ', 'ﾊﾟ', 'ﾋﾟ', 'ﾌﾟ', 'ﾍﾟ', 'ﾎﾟ');
		
		$replace_by = array('ヴ', 'ガ', 'ギ', 'グ',
							'ゲ', 'ゴ', 'ザ', 'ジ',
							'ズ', 'ゼ', 'ゾ', 'ダ',
							'ヂ', 'ヅ', 'デ', 'ド',
							'バ', 'ビ', 'ブ', 'ベ',
							'ボ', 'パ', 'ピ', 'プ', 'ペ', 'ポ');
		$_result = str_replace($replace_of, $replace_by, $str);
	   
		//半角ｶﾅ→全角カナに変換
		$replace_of = array('ｱ', 'ｲ', 'ｳ', 'ｴ', 'ｵ',
							'ｶ', 'ｷ', 'ｸ', 'ｹ', 'ｺ',
							'ｻ', 'ｼ', 'ｽ', 'ｾ', 'ｿ',
							'ﾀ', 'ﾁ', 'ﾂ', 'ﾃ', 'ﾄ',
							'ﾅ', 'ﾆ', 'ﾇ', 'ﾈ', 'ﾉ',
							'ﾊ', 'ﾋ', 'ﾌ', 'ﾍ', 'ﾎ',
							'ﾏ', 'ﾐ', 'ﾑ', 'ﾒ', 'ﾓ',
							'ﾔ', 'ﾕ', 'ﾖ', 'ﾗ', 'ﾘ',
							'ﾙ', 'ﾚ', 'ﾛ', 'ﾜ', 'ｦ',
							'ﾝ', 'ｧ', 'ｨ', 'ｩ', 'ｪ',
							'ｫ', 'ヵ', 'ヶ', 'ｬ', 'ｭ',
							'ｮ', 'ｯ', '､', '｡', 'ｰ',
							'｢', '｣', 'ﾞ', 'ﾟ');
		
		$replace_by = array('ア', 'イ', 'ウ', 'エ', 'オ',
							'カ', 'キ', 'ク', 'ケ', 'コ',
							'サ', 'シ', 'ス', 'セ', 'ソ',
							'タ', 'チ', 'ツ', 'テ', 'ト',
							'ナ', 'ニ', 'ヌ', 'ネ', 'ノ',
							'ハ', 'ヒ', 'フ', 'ヘ', 'ホ',
							'マ', 'ミ', 'ム', 'メ', 'モ',
							'ヤ', 'ユ', 'ヨ', 'ラ', 'リ',
							'ル', 'レ', 'ロ', 'ワ', 'ヲ',
							'ン', 'ァ', 'ィ', 'ゥ', 'ェ',
							'ォ', 'ヶ', 'ヶ', 'ャ', 'ュ',
							'ョ', 'ッ', '、', '。', 'ー',
							'「', '」', '”', '');
		
		$_result = str_replace($replace_of, $replace_by, $_result);
		
		//全角数字→半角数字に変換
		$replace_of = array('１', '２', '３', '４', '５',
							'６', '７', '８', '９', '０');
		
		$replace_by = array('1', '2', '3', '4', '5',
							'6', '7', '8', '9', '0');
		
		$_result = str_replace($replace_of, $replace_by, $_result);
	
		//全角英字→半角英字に変換
		$replace_of = array('Ａ', 'Ｂ', 'Ｃ', 'Ｄ', 'Ｅ', 
							'Ｆ', 'Ｇ', 'Ｈ', 'Ｉ', 'Ｊ', 
							'Ｋ', 'Ｌ', 'Ｍ', 'Ｎ', 'Ｏ', 
							'Ｐ', 'Ｑ', 'Ｒ', 'Ｓ', 'Ｔ', 
							'Ｕ', 'Ｖ', 'Ｗ', 'Ｘ', 'Ｙ', 
							'Ｚ',
							'ａ', 'ｂ', 'ｃ', 'ｄ', 'ｅ', 
							'ｆ', 'ｇ', 'ｈ', 'ｉ', 'ｊ', 
							'ｋ', 'ｌ', 'ｍ', 'ｎ', 'ｏ', 
							'ｐ', 'ｑ', 'ｒ', 'ｓ', 'ｔ', 
							'ｕ', 'ｖ', 'ｗ', 'ｘ', 'ｙ', 
							'ｚ');
		
		$replace_by = array('A', 'B', 'C', 'D', 'E', 
							'F', 'G', 'H', 'I', 'J', 
							'K', 'L', 'M', 'N', 'O', 
							'P', 'Q', 'R', 'S', 'T', 
							'U', 'V', 'W', 'X', 'Y', 
							'Z',
							'a', 'b', 'c', 'd', 'e', 
							'f', 'g', 'h', 'i', 'j', 
							'k', 'l', 'm', 'n', 'o', 
							'p', 'q', 'r', 's', 't', 
							'u', 'v', 'w', 'x', 'y', 
							'z');
		
		$_result = str_replace($replace_of, $replace_by, $_result);

/*
		//小文字ひらがな→小文字カナに変換
		$replace_of = array('ぁ', 'ぃ', 'ぅ', 'ぇ', 'ぉ',
							'ゃ', 'ゅ', 'ょ', 'っ');
	
		$replace_by = array('ァ', 'ィ', 'ゥ', 'ェ', 'ォ',
							'ャ', 'ュ', 'ョ', 'ッ');

		$_result = str_replace($replace_of, $replace_by, $_result);
*/
		//その他記号に変換
		$replace_of = array('ー', '－', '-', '～', ',', 
							'、', '，', '・', '･', '=', 
							'＝', '　', ' ', '。', '．', 
							'.', '/', '／', '―', '‐', 
							'’', '′', '´', '‘', '゜', 
							'\'', '"', '〝', '″', '゛', 
							'“', '”', 'ッ', 'ュ', 'ィ',
							'ゅ', 'ぃ');

		$replace_by = array('', '', '', '', '', 
							'', '', '', '', '', 
							'', '', '', '', '', 
							'', '', '', '', '', 
							'', '', '', '', '', 
							'', '', '', '', '', 
							'', '', '', '', '', 
							'', '');

		$_result = str_replace($replace_of, $replace_by, $_result);

/*
		//小文字カナ→大文字カナに変換
		$replace_of = array('ァ', 'ィ', 'ゥ', 'ェ', 'ォ',
							'ヶ', 'ヶ', 'ャ', 'ュ', 'ョ', 'ッ');
	
		$replace_by = array('ア', 'イ', 'ウ', 'エ', 'オ',
							'ケ', 'ケ', 'ヤ', 'ユ', 'ヨ', 'ツ');
	
		$_result = str_replace($replace_of, $replace_by, $_result);
*/
		return $_result;
	}
}
?>