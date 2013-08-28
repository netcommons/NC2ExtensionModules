<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 動画配信データ取得コンポーネント
 *
 * @package     NetCommons Components
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_Components_View 
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
	 * @access	private
	 */
    var $_request = null;

	/**
	 * @var Sessionオブジェクトを保持
	 *
	 * @access	private
	 */
    var $_session = null;

    /**
	 * コンストラクター
	 *
	 * @access	public
	 */
	function Multimedia_Components_View()
	{
		$container =& DIContainerFactory::getContainer();
		$this->_db =& $container->getComponent("DbObject");
		$this->_request =& $container->getComponent("Request");
		$this->_session =& $container->getComponent("Session");
	}
	
	/**
	 * 配置されている動画配信IDを取得する
	 *
     * @return string	配置されている動画配信ID
	 * @access	public
	 */
	function &getCurrentMultimediaId() {
		$params = array($this->_request->getParameter("block_id"));	
		$sql = "SELECT multimedia_id ".
				"FROM {multimedia_block} ".
				"WHERE block_id = ?";
		$result = $this->_db->execute($sql, $params);
		if ($result === false) {
			$this->_db->addError();
			return $result;
		}

		return $result[0]["multimedia_id"];
	}
	
	/**
	 * 現在登録されている動画配信IDを取得する
	 *
	 * @return string 配置されている動画配信ID
	 * @access	pblic
	 */
	function &getFirstMultimediaId() {
		$params = array(
			$this->_request->getParameter('room_id')
		);
		$sql = "SELECT multimedia_id "
				. "FROM {multimedia} "
				. "WHERE room_id = ?";
		$result = $this->_db->execute($sql, $params, 1);
		if ($result === false) {
			$this->_db->addError();
			return $result;
		}

		return $result[0]['multimedia_id'];
	}
	
	/**
	 * 動画配信が配置されているブロックデータを取得する
	 *
     * @return string	ブロックデータ
	 * @access	public
	 */
	function &getBlock() {
		$params = array($this->_request->getParameter("multimedia_id"));
		$sql = "SELECT M.room_id, B.block_id ".
				"FROM {multimedia} M ".
				"INNER JOIN {multimedia_block} B ".
				"ON M.multimedia_id = B.multimedia_id ".
				"WHERE M.multimedia_id = ? ".
				"ORDER BY B.block_id";
		$result = $this->_db->execute($sql, $params, 1);
		if ($result === false) {
			$this->_db->addError();
			return $result;
		}

		return $result[0];
	}
	
	/**
	 * 動画配信が存在するか判断する
	 *
     * @return boolean	true:存在する、false:存在しない
	 * @access	public
	 */
	function multimediaExist() {
		$params = array(
			$this->_request->getParameter("multimedia_id"),
			$this->_request->getParameter("room_id")
		);
		$sql = "SELECT multimedia_id ".
				"FROM {multimedia} ".
				"WHERE multimedia_id = ? ".
				"AND room_id = ?";
		$result = $this->_db->execute($sql, $params);
		if ($result === false) {
			$this->_db->addError();
			return $result;
		}
		
		if (count($result) > 0) {
			return true;
		}

		return false;
	}
	
	function getMultimediaCount() {
    	$params["room_id"] = $this->_request->getParameter("room_id");
    	$count = $this->_db->countExecute("multimedia", $params);
    	if($count === false) {
    		$this->_db->addError();
    	}

		return $count;
	}
	
	/**
	 * 動画配信の設定データを取得する
	 *
     * @return string	設定データ配列
	 * @access	public
	 */
	function &getConfig() {
		$container =& DIContainerFactory::getContainer();
		$configView =& $container->getComponent("configView");
		$module_id = $this->_request->getParameter("module_id");
		$config = $configView->getConfig($module_id, false);

		return $config;
	}
	
	/**
	 * 動画配信用デフォルトデータを取得する
	 *
     * @return array	動画配信用デフォルトデータ配列
	 * @access	public
	 */
	function &getDefaultMultimedia() {
		$config = $this->getConfig();
		if ($config === false) {
        	return $config;
        }
		
		$multimedia = array(
			"album_authority" => constant($config['album_authority']['conf_value']),
			"vote_flag" => constant($config['vote_flag']['conf_value']),
    		"comment_flag" => constant($config['comment_flag']['conf_value']),
    		"confirm_flag" => constant($config['confirm_flag']['conf_value']),
			"display" => constant($config['display']['conf_value']),
    		"autoplay_flag" => constant($config['autoplay_flag']['conf_value']),
    		"buffer_time" => $config['buffer_time']['conf_value'],
    		"album_visible_row" => $config['album_visible_row']['conf_value'],
			"new_period" => $config['new_period']['conf_value'],
			"album_jacket" => $config['album_jacket']['conf_value']
		);
		
		return $multimedia;
	}
	
	/**
	 * 動画配信データを取得する
	 *
     * @return array	動画配信データ
	 * @access	public
	 */
	function &getCurrentMultimedia() {
		$params = array($this->_request->getParameter("block_id"));
		$sql = "SELECT B.block_id, B.multimedia_id, B.display, ".
						"B.autoplay_flag, B.buffer_time, ".
						"B.album_visible_row, B.new_period, ".
						"M.album_authority, M.vote_flag, M.comment_flag, M.confirm_flag ".
				"FROM {multimedia_block} B ".
				"INNER JOIN {multimedia} M ".
				"ON B.multimedia_id = M.multimedia_id ".
				"WHERE block_id = ?";
		$result = $this->_db->execute($sql, $params);
		if ($result === false) {
			$this->_db->addError();
		}
		if (empty($result)) {
			return $result;
		}
		
		$result[0]['album_authority'] = $this->_hasAlbumAuthority($result[0]);
		$result[0]['new_period_time'] = $this->_getNewPeriodTime($result[0]['new_period']);
		
		return $result[0];
	}
	
	/**
	 * アルバム作成権限を取得する
	 *
	 * @param	array	$photoalbum	フォトアルバムデータ配列
	 * @return boolean	true:権限有り、false:権限無し
	 * @access	public
	 */
	function _hasAlbumAuthority($multimedia) {
		$auth_id = $this->_session->getParameter("_auth_id");
		if ($auth_id >= $multimedia['album_authority']) {
			return true;
		}
		
		return false;
	}
	
	function getMultimediaById($multimedia_id) {
		$params = array(
			"multimedia_id" => $multimedia_id
		);
		
		$result = $this->_db->selectExecute("multimedia", $params);
		if ($result === false) {
			$this->_db->addError();
		}
		
		if(!empty($result) && isset($result[0])) {
			return $result[0];
		}

		return $result;
	}
	
	/**
	 * フォトアルバムデータを取得する
	 *
     * @return array	フォトアルバムデータ
	 * @access	public
	 */
	function &getMultimedia() {
		$params = array($this->_request->getParameter("multimedia_id"));
		$sql = "SELECT multimedia_id, album_authority, vote_flag, comment_flag, confirm_flag ".
				"FROM {multimedia} ".
				"WHERE multimedia_id = ?";
		$result = $this->_db->execute($sql, $params);
		if ($result === false) {
			$this->_db->addError();
			return $result;
		}
		
		return $result[0];
	}
	
	/**
	 * new記号表示期間から対象年月日を取得する
	 *
	 * @param	string	$new_period		new記号表示期間
     * @return string	new記号表示対象年月日(YmdHis)
	 * @access	public
	 */
	function &_getNewPeriodTime($new_period) {
		if (empty($new_period)) {
			$new_period = -1;
		}
		
		$time = timezone_date();
		$time = mktime(0, 0, 0, 
						intval(substr($time, 4, 2)), 
						intval(substr($time, 6, 2)) - $new_period,
						intval(substr($time, 0, 4))
						);
		$time = date("YmdHis", $time);
		
		return $time;
	}
	
	/**
	 * 動画配信のアルバムの計数を取得する
	 *
	 * @param	string	$new_period		new記号表示期間
     * @return string	new記号表示対象年月日(YmdHis)
	 * @access	public
	 */
	function getAlbumCount($multimedia_id) {
		$params[] = $multimedia_id;

		$sql = "SELECT COUNT(*) as count ".
				"FROM {multimedia_album} A ";
		$sql .= $this->_getAuthorityFromSQL();
		$sql .= "WHERE A.multimedia_id = ? ";
		$sql .= $this->_getAuthorityWhereSQL($params);
		if(!empty($order_params)) {
			$sql .= $this->_db->getOrderSQL($order_params);
		}
    	$result = $this->_db->execute($sql, $params);
		if ( $result === false ) {
			// エラーが発生した場合、エラーリストに追加
			$this->_db->addError();
			return $result;
		}
		return $result[0]['count'];
	}
	
	/**
	 * アルバム一覧を取得する
	 *
     * @return array	アルバム一覧データ
	 * @access	public
	 */
	function getAlbumList($multimedia_id, $order_params=null, $disp_cnt=null, $begin=null) {
		$params[] = $multimedia_id;

		$sql = "SELECT A.* ".
				"FROM {multimedia_album} A ";
		$sql .= $this->_getAuthorityFromSQL();
		$sql .= "WHERE A.multimedia_id = ? ";
		$sql .= $this->_getAuthorityWhereSQL($params);
		if(!empty($order_params)) {
			$sql .= $this->_db->getOrderSQL($order_params);
		}
    	$result = $this->_db->execute($sql, $params, $disp_cnt, $begin, true, array($this, "_makeAlbumArray"));
		if ( $result === false ) {
			// エラーが発生した場合、エラーリストに追加
			$this->_db->addError();
		}

		return $result;
	}
	
	/**
	 * アルバムデータ配列を生成する
	 *
	 * @param	array	$recordSet	タスクADORecordSet
	 * @return array	タスクデータ配列
	 * @access	private
	 */
	function &_makeAlbumArray(&$recordSet) {
		$multimedia = $this->_request->getParameter("multimedia_obj");
		
		$albums = array();
		while ($row = $recordSet->fetchRow()) {
			if (!empty($row["album_jacket"])) {
				$row["jacket_style"] = $this->getImageStyle($row["width"], $row["height"], MULTIMEDIA_JACKET_WIDTH, MULTIMEDIA_JACKET_HEIGHT);
			}

			$row["edit_authority"] = false;
			if ($multimedia["album_authority"]
					&& $this->_hasEditAuthority($row["insert_user_id"])) {
				$row["edit_authority"] = true;
			}

			$albums[] = $row;
		}

		return $albums;
	}
	
	/**
	 * アルバムのサムネイルのスタイル属性値を取得する
	 *
	 * @param	string	$width	幅
	 * @param	string	$height	高さ
	 * @param	string	$maxWidth	最大幅
	 * @param	string	$maxHeight	最大高さ
	 * @return array	画像のスタイル属性値
	 * @access	private
	 */
	function &getImageStyle($width, $height, $maxWidth, $maxHeight) {
		$ratio = $height / $width;
		
		$widthRatio = $width / $maxWidth;
		$heightRatio = $height / $maxHeight;
		
		if ($widthRatio > $heightRatio) {
			$height = $maxHeight;
			$widht = intval($height / $ratio);
			$top = 0;
			$right = intval(($widht + $maxWidth) / 2);
			$bottom = $maxHeight;
			$left = intval(($widht - $maxWidth) / 2);
			$marginLeft = $left * -1;
			$marginTop = $top;
		} else {
			$widht = $maxWidth;
			$height = intval($widht * $ratio);
			$top = intval(($height - $maxHeight) / 2);
			$right = $maxWidth;
			$bottom = intval(($height + $maxHeight) / 2);
			$left = 0;
			$marginLeft = $left;
			$marginTop = $top * -1;
		}
		
		$style = sprintf(MULTIMEDIA_THUMBNAIL_STYLE, $widht, $height, $top, $right, $bottom, $left, $marginLeft, $marginTop);
		
		return $style;
	}
	
	/**
	 * アルバムを取得する
	 *
     * @return array	アルバムデータ
	 * @access	public
	 */
	function getAlbum($album_id) {
		$params = array(
			"album_id" => intval($album_id)
		);
		
		$result = $this->_db->selectExecute("multimedia_album", $params, null, null, null, array($this, "_makeAlbumArray"));
		if ($result === false) {
			$this->_db->addError();
		}
		
		if(!empty($result) && isset($result[0])) {
			return $result[0];
		}

		return $result;		
	}
	
	/**
	 * 動画を取得する
	 *
     * @return array	動画データ
	 * @access	public
	 */
	function getItem($item_id) {
		$params = array(
			"item_id" => intval($item_id)
		);
		
		$result = $this->_db->selectExecute("multimedia_item", $params, null, null, null, array($this, "_makeItemArray"));	
		if ($result === false) {
			$this->_db->addError();
		}

		if(!empty($result) && isset($result[0])) {
			$result[0]['vote_authority'] = $this->_hasVoteAuthority($item_id);
			$result[0]['comment_authority'] = $this->_hasCommentAuthority();
			return $result[0];
		}

		return $result;
	}
	
	/**
	 * 編集権限を取得する
	 *
	 * @param	array	$insertUserID	登録者ID
	 * @return boolean	true:権限有り、false:権限無し
	 * @access	public
	 */
	function _hasEditAuthority(&$insertUserID) {
		$container =& DIContainerFactory::getContainer();

		$authID = $this->_session->getParameter("_auth_id");
		if ($authID >= _AUTH_CHIEF) {
			return true;
		}

		$userID = $this->_session->getParameter("_user_id");
		if ($insertUserID == $userID) {
			return true;
		} 

		$hierarchy = $this->_session->getParameter("_hierarchy");
		$authCheck =& $container->getComponent("authCheck");
		$insetUserHierarchy = $authCheck->getPageHierarchy($insertUserID);
		if ($hierarchy > $insetUserHierarchy) {
	        return true;
		}

	    return false;
	}
	
	/**
	 * 投票権限を取得する
	 *
	 * @param	array	$item	動画データ配列
	 * @return boolean	true:権限有り、false:権限無し
	 * @access	public
	 */
	function _hasVoteAuthority($item_id) {
		$multimedia = $this->_request->getParameter("multimedia_obj");
		if ($multimedia['vote_flag'] != _ON) {
			return false;
		}
		
		$votes = $this->_session->getParameter("multimedia_votes");
		if (!empty($votes) && in_array($item_id, $votes)) {
			return false;
    	}

		$user_id = $this->_session->getParameter("_user_id");
		if (empty($user_id)) {
			return true;
		}
		
		$params = array(
			$user_id,
			$item_id
		);
		$sql = "SELECT vote_flag ".
				"FROM {mutlimedia_user_item} ".
				"WHERE user_id = ? ".
				"AND item_id = ?";
		$vote_flags = $this->_db->execute($sql, $params, null, null, false);
		if ($vote_flags === false) {
        	$this->_db->addError();
			return false;
		}

		if (empty($vote_flags) || $vote_flags[0][0] != _ON) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * コメント権限を取得する
	 *
	 * @return boolean	true:権限有り、false:権限無し
	 * @access	public
	 */
	function _hasCommentAuthority() {
		$multimedia = $this->_request->getParameter("multimedia_obj");
		if ($multimedia['comment_flag'] != _ON) {
			return false;
		}
		
		$auth_id = $this->_session->getParameter("_auth_id");
		if ($auth_id <= _AUTH_GUEST) {
			return false;
		}

		return true;
	}
	
	/**
	 * 動画のコメントの計数を取得する
	 *
	 * @param	string	$item_id		動画id
     * @return string	動画のコメントの計数
	 * @access	public
	 */
	function getCommentCount($item_id) {
		$params = array(
			"item_id" => intval($item_id)
		);
		$commentCount = $this->_db->countExecute("multimedia_comment", $params);
		
		if($commentCount === false) {
			$this->_db->addError();
		}
		
		return $commentCount;
	}
	
	/**
	 * 動画のコメントを取得する
	 *
	 * @param	string	$item_id		動画id
     * @return string	動画のコメント
	 * @access	public
	 */
	function getComments($item_id, $begin=null) {
		$comments = $this->_db->selectExecute("multimedia_comment", array("item_id" => intval($item_id)), array("insert_time" => "DESC"), MULTIMEDIA_VISIBLE_ITEM_COMMENT, $begin, array($this, "_makeCommentArray"));	
		if($comments === false) {
			$this->_db->addError();
		}
		
		return $comments;
	}
	
	/**
	 * コメントデータ配列を生成する
	 *
	 * @param	array	$recordSet	タスクADORecordSet
	 * @return array	タスクデータ配列
	 * @access	private
	 */
	function &_makeCommentArray(&$recordSet) {
		$comments = array();
		while ($row = $recordSet->fetchRow()) {
			$row["edit_authority"] = false;
			if ($this->_hasEditAuthority($row["insert_user_id"])) {
				$row["edit_authority"] = true;
			}
			
			$comments[] = $row;
		}

		return $comments;
	}
	
	/**
	 * 画像の大きさを取得
	 *
     * @return string	画像の大きさ
	 * @access	public
	 */
	function &getImageSize($image) {
		if (is_numeric($image)) {
			$container =& DIContainerFactory::getContainer();
			
			$uploads =& $container->getComponent("uploadsView");
			$uploadFiles = $uploads->getUploadById($image);
			if (empty($uploadFiles)) {
				return false;
			}

			$imageSize = getimagesize(FILEUPLOADS_DIR. "multimedia/". $uploadFiles[0]["physical_file_name"]);
		} else {
			$imageSize = getimagesize(HTDOCS_DIR.MULTIMEDIA_SAMPLR_JACKET_PATH.MULTIMEDIA_SAMPLR_JACKET_DIR.$image);
		}

		return $imageSize;
	}
	
	/**
	 * 権限判断用のSQL文FROM句を取得する
	 *
     * @return string	権限判断用のSQL文FROM句
	 * @access	public
	 */
	function &_getAuthorityFromSQL() {
		$auth_id = $this->_session->getParameter("_auth_id");

		$sql = "";
		if ($auth_id >= _AUTH_CHIEF) {
			return $sql;
		}

		$sql .= "LEFT JOIN {pages_users_link} PU ".
					"ON A.insert_user_id = PU.user_id ".
					"AND A.room_id = PU.room_id ";
		$sql .= "LEFT JOIN {authorities} AU ".
					"ON PU.role_authority_id = AU.role_authority_id ";
					
		return $sql;
	}
	
	/**
	 * 権限判断用のSQL文WHERE句を取得する
	 * パラメータ用配列に必要な値を追加する
	 *
	 * @param	array	$params	パラメータ用配列
     * @return string	権限判断用のSQL文WHERE句
	 * @access	public
	 */
	function &_getAuthorityWhereSQL(&$params) {
		$auth_id = $this->_session->getParameter("_auth_id");

		$sql = "";
		if ($auth_id >= _AUTH_CHIEF) {
			return $sql;
		}
		
		$sql .= " AND (A.public_flag = ? OR AU.hierarchy < ? OR A.insert_user_id = ? ";
		
		$default_entry = $this->_session->getParameter("_default_entry_flag");
		$hierarchy = $this->_session->getParameter("_hierarchy");
		if ($default_entry == _ON && $hierarchy > $this->_session->getParameter("_default_entry_hierarchy")) {
			$sql .= " OR AU.hierarchy IS NULL) ";
		} else {
			$sql .= ") ";
		}
		
		$params[] = _ON;
		$params[] = $hierarchy;
		$params[] = $this->_session->getParameter("_user_id");

		return $sql;
	}
	
	/**
	 * 動画データ配列を生成する
	 *
	 * @param	array	$recordSet	動画ADORecordSet
	 * @return array	動画データ配列
	 * @access	private
	 */
	function &_makeItemArray(&$recordSet) {
		$items = array();
		while ($row = $recordSet->fetchRow()) {
			$row['edit_authority'] = false;
			if ($this->_hasEditAuthority($row['insert_user_id'])) {
				$row['edit_authority'] = true;
			}
			$tags = $this->getTags($row['item_id']);
			$row['item_tag'] = $this->_makeTagString($tags);
			$row['duration'] = floor($row['duration']/60).":".floor(($row['duration']-60*floor($row['duration']/60)));
			$items[] = $row;
		}

		return $items;
	}
	
	/**
	 * 動画一覧共通のSQL
	 *
	 * @return 　array   	動画件数データ
	 * @access	public
	 */
	function _getItemListSQL(&$params, $where_params, $order_params=null, $count_flag=false) {
		$sql = "";
		if(empty($order_params)) {
			$order_params = array(
				"insert_time" => "DESC"
			);
		}
		
		if($count_flag) {
			$sql = "SELECT COUNT(T.item_id) as count FROM {multimedia_item} T ";
		}else {
			$sql = "SELECT T.* FROM {multimedia_item} T ";
		}
		$sql .= "LEFT JOIN {multimedia_album} A ON T.album_id=A.album_id ";
		$sql .= $this->_getAuthorityFromSQL();
		if(!empty($where_params)) {
			foreach($where_params as $key => $val) {
				if($val == "") {
					continue;
				}
				$where_params["T.".$key] = $val;
				unset($where_params[$key]);
			}
			$sql .= $this->_db->getWhereSQL($params, $where_params);
		}
		$sql .= $this->_getAuthorityWhereSQL($params);
		if(!$count_flag) {
			foreach($order_params as $key => $val) {
				$order_params["T.".$key] = $val;
				unset($order_params[$key]);
			}
			$sql .= $this->_db->getOrderSQL($order_params);
		}
		
		return $sql;
	}
	
	/**
	 * 動画一覧件数を取得する
	 *
	 * @return 　array   	動画件数データ
	 * @access	public
	 */
	function getItemListCount($where_params) {
		if(empty($where_params)) {
			return false;
		}

		$sql = "";
		$params = array();
		$sql = $this->_getItemListSQL($params, $where_params, null, true);
		$result = $this->_db->execute($sql, $params);
		if ( $result === false ) {
			// エラーが発生した場合、エラーリストに追加
			$this->_db->addError();
			return $result;
		}
		return $result[0]['count'];
	}
	
	/**
	 * 動画一覧を取得する
	 *
	 * @param	int	    $multimedia_id	ID
	 * @return 　array   	動画データ
	 * @access	public
	 */
	function getItemList($where_params, $order_params, $begin=null) {
		if(empty($where_params)) {
			return false;
		}

		$sql = "";
		$params = array();
		$sql = $this->_getItemListSQL($params, $where_params, $order_params);

		$result = $this->_db->execute($sql, $params ,MULTIMEDIA_VISIBLE_ITEM_CNT, $begin, true, array($this, "_makeItemArray"));
		if ( $result === false ) {
			// エラーが発生した場合、エラーリストに追加
			$this->_db->addError();
			return $result;
		}
		return $result;
	}
	
	/**
	 * 検索動画一覧を取得する
	 *
	 * @return 　array   	動画データ
	 * @access	public
	 */
	function getSearchItemList($sql, $where_params, $begin=null) {
		$result = $this->_db->execute($sql, $where_params ,MULTIMEDIA_SEARCH_VISIBLE_ITEM_CNT, $begin, true, array($this, "_makeItemArray"));
		if ( $result === false ) {
			// エラーが発生した場合、エラーリストに追加
			$this->_db->addError();
		}
		return $result;
	}
	
	/**
	 * タグデータ配列を取得する
	 *
	 * @return array	タグデータ配列
	 * @access	public
	 */
	function getTags($item_id) {
		$sql = "SELECT R.tag_value, T.used_number, R.tag_id, R.item_id, R.sequence " .
				"FROM {multimedia_item_tag} AS R, {multimedia_tag} AS T " .
				"WHERE R.tag_id = T.tag_id " .
				"AND R.item_id = ? " .
				"ORDER BY R.sequence;";
		
		$params = array(
			$item_id
		);
		$tags = $this->_db->execute($sql, $params);
		if ($tags === false) {
			$this->_db->addError();
			return false;
		}
		return $tags;
	}
	
	/**
	 * 関連動画配列を取得する
	 *
	 * @return array	タグデータ配列
	 * @access	public
	 */
	function getSimilarItems($item_id) {
		$tags = $this->getTags($item_id);
		if(empty($tags)) {
			return $tags;
		}
		
		$params = array();
		$where_sql = "";
		foreach($tags as $tag) {
			$params[] = $tag['tag_id'];
			$where_sql .= " G.tag_id=? OR";
		}
		$where_sql = " ( ".substr($where_sql, 0, -2)." ) ";
		$sql = "SELECT T.*,count(*) as count " .
				"FROM {multimedia_item_tag} AS G, {multimedia_item} AS T ";
		$sql .= "LEFT JOIN {multimedia_album} A ON T.album_id=A.album_id ";
		$sql .= $this->_getAuthorityFromSQL();
		$sql .= "WHERE G.item_id=T.item_id AND T.item_id <> ".$item_id." AND ".$where_sql;
		$sql .= $this->_getAuthorityWhereSQL($params);
		$sql .= " GROUP BY T.item_id ORDER BY count DESC";
		$items = $this->_db->execute($sql, $params, MULTIMEDIA_TAG_ITEMS_NUMBER, 0, true, array($this, "_makeItemArray"));
		if ($items === false) {
			$this->_db->addError();
			return false;
		}
		return $items;
	}

	/**
	 * タグストリングを生成する
	 */
	function &_makeTagString($tags) {
		$string = "";
		if(empty($tags)) {
			return $string;
		}
		$string = "";
		foreach($tags as $tag) {
			$string .= $tag['tag_value'].MULTIMEDIA_TAG_SEPARATOR;
		}
		$string = substr($string, 0, -1);
		
		return $string;
	}
	
	/**
     * ページに関する設定を行います
     *
     * @param int disp_cnt 1ページ当り表示件数
     * @param int now_page 現ページ
     */
    function setPageInfo(&$pager, $data_cnt, $disp_cnt, $now_page = NULL){
    	$pager['data_cnt']    = 0;
    	$pager['total_page']  = 0;
    	$pager['next_link']   = FALSE;
    	$pager['prev_link']   = FALSE;
    	$pager['disp_begin']  = 0;
    	$pager['disp_end']    = 0;
    	$pager['link_array']  = NULL;
    	
    	if(empty($disp_cnt)) {
    		return false;
    	}
    	
    	$pager['data_cnt'] = $data_cnt;
        // now page
        $pager['now_page'] = (NULL == $now_page) ? 1 : $now_page;
        // total page
        $pager['total_page'] = ceil($pager['data_cnt'] / $disp_cnt);
        if($pager['total_page'] < $pager['now_page']) {
        	$pager['now_page'] = 1;
        }
        // link array {{
        if(($pager['now_page'] - MULTIMEDIA_FRONT_AND_BEHIND_LINK_CNT) > 0){
            $start = $pager['now_page'] - MULTIMEDIA_FRONT_AND_BEHIND_LINK_CNT;
        }else{
            $start = 1;
        }
        if(($pager['now_page'] + MULTIMEDIA_FRONT_AND_BEHIND_LINK_CNT) >= $pager['total_page']){
            $end = $pager['total_page'];
        }else{
            $end = $pager['now_page'] + MULTIMEDIA_FRONT_AND_BEHIND_LINK_CNT;
        }
        $i = 0;
        for($i = $start; $i <= $end; $i++){
            $pager['link_array'][] = $i;
        }
        // next link
        if($disp_cnt < $pager['data_cnt']){
            if($pager['now_page'] < $pager['total_page']){
                $pager['next_link'] = TRUE;
            }
        }
        // prev link
        if(1 < $pager['now_page']){
            $pager['prev_link'] = TRUE;
        }
        // begin disp number
        $pager['disp_begin'] = ($pager['now_page'] - 1) * $disp_cnt;
        // end disp number
        $tmp_cnt = $pager['now_page'] * $disp_cnt;
        $pager['disp_end'] = ($pager['data_cnt'] < $tmp_cnt) ? $pager['data_cnt'] : $tmp_cnt;
	}
}
?>