<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 質問取得コンポーネント
 *
 * @package     NetCommons Components
 * @author      Ka
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_Components_View
{
	/**
	 * @var DBオブジェクトを保持
	 *
	 * @access	private
	 */
	var $_db = null;

	/**
	 * @var DIコンテナを保持
	 *
	 * @access	private
	 */
	var $_container = null;

	/**
	 * @var ページ
	 *
	 * @access	private
	 */


	/**
	 * コンストラクター
	 *
	 * @access	public
	 */
	function Faq_Components_View()
	{
		$this->_container =& DIContainerFactory::getContainer();
		$this->_db =& $this->_container->getComponent("DbObject");
	}

	/**
	 * ルームIDの質問件数を取得する
	 *
     * @return string	質問件数
	 * @access	public
	 */
	function getFaqCount() {
		$request =& $this->_container->getComponent("Request");
    	$params["room_id"] = $request->getParameter("room_id");
    	$count = $this->_db->countExecute("faq", $params);

		return $count;
	}

	/**
	 * 在配置されている質問IDを取得する
	 *
     * @return string	配置されている質問ID
	 * @access	public
	 */
	function &getCurrentFaqId() {
		$request =& $this->_container->getComponent("Request");
		$params = array($request->getParameter("block_id"));
		$sql = "SELECT faq_id ".
				"FROM {faq_block} ".
				"WHERE block_id = ?";
		$result = $this->_db->execute($sql, $params);
		if ($result === false) {
			$this->_db->addError();
			return $result;
		}

		return $result[0]["faq_id"];
	}

	/**
	 * 質問一覧データを取得する
	 *
     * @return array	質問一覧データ配列
	 * @access	public
	 */
	function &getFaqs() {
		$request =& $this->_container->getComponent("Request");
		$limit = $request->getParameter("limit");
		$offset = $request->getParameter("offset");

		$sortColumn = $request->getParameter("sort_col");
		if (empty($sortColumn)) {
			$sortColumn = "faq_id";
		}
		$sortDirection = $request->getParameter("sort_dir");
		if (empty($sortDirection)) {
			$sortDirection = "DESC";
		}
		$orderParams[$sortColumn] = $sortDirection;

		$params = array($request->getParameter("room_id"));
		$sql = "SELECT faq_id, faq_name, insert_time, insert_user_id, insert_user_name ".
				"FROM {faq} ".
				"WHERE room_id = ? ".
				$this->_db->getOrderSQL($orderParams);
		$result = $this->_db->execute($sql, $params, $limit, $offset);
		if ($result === false) {
			$this->_db->addError();
		}

		return $result;
	}

	/**
	 * 質問が存在するか判断する
	 *
     * @return boolean	true:存在する、false:存在しない
	 * @access	public
	 */
	function faqExists() {
		$request =& $this->_container->getComponent("Request");
		$params = array(
			$request->getParameter("faq_id"),
			$request->getParameter("room_id")
		);
		$sql = "SELECT faq_id ".
				"FROM {faq} ".
				"WHERE faq_id = ? ".
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

	/**
	 * 質問用デフォルトデータを取得する
	 *
     * @return array	質問用デフォルトデータ配列
	 * @access	public
	 */
	function &getDefaultFaq() {
		$configView =& $this->_container->getComponent("configView");
		$request =& $this->_container->getComponent("Request");
		$module_id = $request->getParameter("module_id");
		$config = $configView->getConfig($module_id, false);
		if ($config === false) {
        	return $config;
        }

		$faq = array(
			"display_row" => $config["display_row"]["conf_value"]
		);

		return $faq;
	}

	/**
	 * 質問データを取得する
	 *
     * @return array	質問データ配列
	 * @access	public
	 */
	function &getFaq() {
		$request =& $this->_container->getComponent("Request");
		$configView =& $this->_container->getComponent("configView");

		$sql = "SELECT faq_id, faq_name ";
		$actionChain =& $this->_container->getComponent("ActionChain");
		$actionName = $actionChain->getCurActionName();
		if ($actionName == "faq_view_edit_create" ||
				$actionName == "faq_action_edit_create") {
			$sql .= ",faq_authority ";
		}

		$params = array($request->getParameter("faq_id"));
		$sql .=	"FROM {faq} ".
				"WHERE faq_id = ?";
		$result = $this->_db->execute($sql, $params);
		if ($result === false) {
			$this->_db->addError();
			return $result;
		}

		$module_id = $request->getParameter("module_id");
		$config = $configView->getConfig($module_id, false);
		if ($config === false) {
        	return $config;
        }

        $result[0]['display_row'] = $config["display_row"]["conf_value"];

		return $result[0];
	}

	/**
	 * 現在配置されている質問データを取得する
	 *
     * @return array	配置されている質問データ配列
	 * @access	public
	 */
	function &getCurrentFaq() {
		$request =& $this->_container->getComponent("Request");

		$params = array($request->getParameter("block_id"));
		$sql = "SELECT B.block_id, B.faq_id, B.display_row, B.display_category, ".
					"J.faq_name, J.faq_authority ".
				"FROM {faq_block} B ".
				"INNER JOIN {faq} J ".
				"ON B.faq_id = J.faq_id ".
				"WHERE block_id = ?";
		$result = $this->_db->execute($sql, $params);
		if ($result === false) {
			$this->_db->addError();
			return $result;
		}

		$result[0]['has_post_auth'] = $this->_hasPostAuthority($result[0]);

		return $result[0];
	}

	/**
	 * 投稿権限を取得する
	 *
	 * @param	array	$bbs	質問状態、表示方法、投稿権限の配列
	 * @return boolean	true:権限有り、false:権限無し
	 * @access	public
	 */
	function _hasPostAuthority($faq) {

		$session =& $this->_container->getComponent("Session");
		$auth_id = $session->getParameter("_auth_id");
		if ($auth_id >= $faq["faq_authority"]) {
			return true;
		}

		return false;
	}

	/**
	 * 承認権限を取得する
	 *
	 * @param	array	$insetUserID	登録者ID
	 * @return boolean	true:権限有り、false:権限無し
	 * @access	public
	 */
	function _hasConfirmAuthority()
	{
		$session =& $this->_container->getComponent("Session");
		$_auth_id = $session->getParameter("_auth_id");

		if ($_auth_id >= _AUTH_CHIEF) {
			return true;
		}

	    return false;
	}

	/**
	 * 編集権限を取得する
	 *
	 * @param	array	$insetUserID	登録者ID
	 * @return boolean	true:権限有り、false:権限無し
	 * @access	public
	 */
	function _hasEditAuthority($inset_user_id)
	{
		$session =& $this->_container->getComponent("Session");

		$user_id = $session->getParameter("_user_id");
		$auth_id = $session->getParameter("_auth_id");
		if ($inset_user_id == $user_id || $auth_id >= _AUTH_CHIEF) {
			return true;
		}

		$request =& $this->_container->getComponent("Request");
		$room_id = $request->getParameter("room_id");
		$hierarchy = $session->getParameter("_hierarchy");
		$authCheck =& $this->_container->getComponent("authCheck");
		$insetUserHierarchy = $authCheck->getPageHierarchy($inset_user_id, $room_id);
		if ($hierarchy > $insetUserHierarchy) {
	        return true;
		}

	    return false;
	}

	/**
	 * カテゴリを取得する
	 *
     * @return array	カテゴリデータ配列
	 * @access	public
	 */
	function getCatByFaqId($faq_id) {
		$params = array(
			"faq_id" => intval($faq_id)
		);
		$order_params = array(
    		"display_sequence" =>"ASC"
    	);
		$faq_categories = $this->_db->selectExecute("faq_category", $params, $order_params);

		if ( $faq_categories === false ) {
			// エラーが発生した場合、エラーリストに追加
			$this->_db->addError();
			return $faq_categories;
		}

		return $faq_categories;
	}

	/**
	 * カテゴリを取得する
	 *
     * @return array	カテゴリデータ配列
	 * @access	public
	 */
	function getCatByPostId($question_id) {
		$sql = "SELECT C.* ".
				"FROM {faq_category} C ";
		$sql .= "LEFT JOIN {faq_question} J ".
					"ON (C.faq_id = J.faq_id) AND (C.category_id = J.category_id)";
		$sql .= "WHERE J.question_id = ? ";
		$params = array(
			"question_id" => intval($question_id)
		);
		$category = $this->_db->execute($sql, $params, null, null, true);

		if ( $category === false || !isset($category[0])) {
			// エラーが発生した場合、エラーリストに追加
			$this->_db->addError();
			return false;
		}

		return $category[0];
	}

	/**
	 * 質問数を取得する
	 * @TODO
     * @return int	質問数
	 * @access	public
	 */
	function getQuestionCount($faq_id, $category_id) {
		$sql = "";
		$sql .= "SELECT count(*) question_count ";
		$sql .= " FROM {faq_question} P ";
		//$sql .= $this->_getAuthorityFromSQL();
		$sql .= " WHERE P.faq_id=? ";
		$params[] = intval($faq_id);

		if(!empty($category_id)) {
			$sql .= " AND P.category_id=? ";
			$params[] = intval($category_id);
		}

		//$sql .= $this->_getAuthorityWhereSQL($params);
		$result = $this->_db->execute($sql, $params);
		if ($result === false) {
			// エラーが発生した場合、エラーリストに追加
			$this->_db->addError();
			return $result;
		}
		return $result[0]['question_count'];
	}

	/**
	 * 質問一覧を取得する
	 *
     * @return array	日誌一覧データ
	 * @access	public
	 */
	function getQuestionList($faq_id, $category_id, $disp_cnt, $begin) {
		$params[] = $faq_id;

		$sql = "SELECT P.* ".
				"FROM {faq_question} P ";
		//$sql .= $this->_getAuthorityFromSQL();
		$sql .= "WHERE P.faq_id = ? ";
		if(!empty($category_id)) {
			$params[] = $category_id;
			$sql .= "AND P.category_id = ? ";
		}
		//$sql .= $this->_getAuthorityWhereSQL($params);
		$sql .= " ORDER BY display_sequence ASC, insert_time DESC";
    	$questions = $this->_db->execute($sql, $params, $disp_cnt, $begin, true, array($this,"_getPostFetchcallback"));
		if ( $questions === false ) {
			// エラーが発生した場合、エラーリストに追加
			$this->_db->addError();
			return false;
		}

		return $questions;
	}

	/**
	 * 質問詳細を取得する
	 *
     * @return array	質問詳細データ
	 * @access	public
	 */
	function getQuestion($question_id) {
		$params[] = $question_id;
		$sql = "SELECT P.* ".
				"FROM {faq_question} P ";
		$sql .= $this->_getAuthorityFromSQL();
		$sql .= "WHERE P.question_id = ? ";
		//$sql .= $this->_getAuthorityWhereSQL($params);

		$question = $this->_db->execute($sql, $params, null, null, true, array($this,"_getPostFetchcallback"));
		if ( $question === false ) {
			// エラーが発生した場合、エラーリストに追加
			$this->_db->addError();
			return false;
		}


		return $question;
	}

	/**
	 * fetch時コールバックメソッド
	 * @param result adodb object
	 * @return array
	 * @access	private
	 */
	function &_getPostFetchcallback($result) {
		$ret = array();
		while ($row = $result->fetchRow()) {
			$row['question_name'] = $row['question_name'];
			$row['has_edit_auth'] = $this->_hasEditAuthority($row['insert_user_id']);
            $row['has_confirm_auth'] = $this->_hasConfirmAuthority();
			$ret[] = $row;
		}
		return $ret;
	}

	/**
	 * 権限判断用のSQL文FROM句を取得する
	 *
     * @return string	権限判断用のSQL文FROM句
	 * @access	public
	 */
	function &_getAuthorityFromSQL()
	{
		$session =& $this->_container->getComponent("Session");
		$authId = $session->getParameter("_auth_id");

		$sql = "";
		if ($authId >= _AUTH_CHIEF) {
			return $sql;
		}

		$sql .= "LEFT JOIN {pages_users_link} PU ".
					"ON P.insert_user_id = PU.user_id ".
					"AND P.room_id = PU.room_id ";
		$sql .= "LEFT JOIN {authorities} A ".
					"ON A.role_authority_id = PU.role_authority_id ";

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
	function &_getAuthorityWhereSQL(&$params)
	{
		$session =& $this->_container->getComponent("Session");
		$authId = $session->getParameter("_auth_id");
		$date = timezone_date(strtr(date("Ymd"), array("/"=>""))."000000", true, "YmdHis");

		$sql = "";
		if ($authId >= _AUTH_CHIEF) {
			return $sql;
		}

		$sql .= "AND ((P.status = ? AND P.agree_flag = ? AND P.journal_date <= ? ) OR A.hierarchy < ? OR P.insert_user_id = ? ";

		$defaultEntry = $session->getParameter("_default_entry_flag");
		$hierarchy = $session->getParameter("_hierarchy");
		if ($defaultEntry == _ON && $hierarchy > $session->getParameter("_default_entry_hierarchy")) {
			$sql .= " OR A.hierarchy IS NULL) ";
		} else {
			$sql .= ") ";
		}

		//$sql .= " AND P.journal_date <= ? ";

		//$request =& $this->_container->getComponent("Request");
		//$params[] = $request->getParameter("room_id");
		$params[] = JOURNAL_POST_STATUS_REREASED_VALUE;
		$params[] = JOURNAL_STATUS_AGREE_VALUE;
		$params[] = $date;
		$params[] = $hierarchy;
		$params[] = $session->getParameter("_user_id");

		return $sql;
	}


	/**
	 * 投稿権限チェック
	 *
	 * @access	public
	 */
	function hasPostAuth($faq_id)
	{
		$session =& $this->_container->getComponent("Session");
		$_user_id = $session->getParameter("_user_id");
		$_auth_id = $session->getParameter("_auth_id");

		if ($_auth_id >= _AUTH_CHIEF) {
			return true;
		}
        $result = $this->_db->selectExecute("faq", array("faq_id"=>$faq_id));
		if ($result === false || !isset($result[0])) {
	       	return false;
		}
		if ($_auth_id >= $result[0]["faq_authority"]) {
			return true;
		}
		return false;
	}

	/**
	 * 携帯用ブロックデータを取得
	 *
	 * @access	public
	 */
	function getBlocksForMobile($block_id_arr)
	{
    	$sql = "SELECT faq.*, block.block_id" .
    			" FROM {faq} faq" .
    			" INNER JOIN {faq_block} block ON (faq.faq_id=block.faq_id)" .
    			" WHERE block.block_id IN (".implode(",", $block_id_arr).")" .
    			" ORDER BY block.insert_time DESC, block.faq_id DESC";

        return $this->_db->execute($sql, null, null, null, true);
	}

}
?>
