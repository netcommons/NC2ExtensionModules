<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Blogparts取得コンポーネント
 *
 * @package     NetCommons Components
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Blogparts_Components_View
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
	 * コンストラクター
	 *
	 * @access	public
	 */
	function Blogparts_Components_View()
	{
		$container =& DIContainerFactory::getContainer();
		$this->_db =& $container->getComponent("DbObject");
		$this->_request =& $container->getComponent("Request");
	}

	/**
	 * Blogpartsが存在するか判断する
	 *
     * @return boolean	true:存在する、false:存在しない
	 * @access	public
	 */
	function blogpartsExists() 
	{
		$params = array(
			$this->_request->getParameter("parts_id")
		);
		$sql = "SELECT parts_id ".
				"FROM {blogparts_parts} ".
				"WHERE parts_id = ? ";
		$blogpartsIDs = $this->_db->execute($sql, $params);
		if ($blogpartsIDs === false) {
			$this->_db->addError();
			return $blogpartsIDs;
		}
		
		if (count($blogpartsIDs) > 0) {
			return true;
		}

		return false;
	}

	/**
	 * Blogparts件数を取得する
	 *
     * @return string	Blogparts件数
	 * @access	public
	 */
	function getBlogpartsCount() 
	{
		$count = $this->_db->countExecute("blogparts_parts");
		return $count;
	}

	/**
	 * 現在配置されているBlogpartsIDを取得する
	 *
     * @return string	配置されているBlogpartsID
	 * @access	public
	 */
	function &getCurrentBlogpartsID() 
	{
		$params = array($this->_request->getParameter("block_id"));		
		$sql = "SELECT parts_id ".
				"FROM {blogparts_block} ".
				"WHERE block_id = ?";
		$blogpartsIDs = $this->_db->execute($sql, $params);
		if ($blogpartsIDs === false) {
			//errorメッセージを追加
			$this->_db->addError();
			return $blogpartsIDs;
		}

		return $blogpartsIDs[0]["parts_id"];
	}

	/**
	 * Blogparts一覧データを取得する
	 *
     * @return array	Blogparts一覧データ配列
	 * @access	public
	 */
	function &getBlogpartsList() 
	{
		$sortColumn = $this->_request->getParameter("sort_col");
		if (empty($sortColumn)) {
			$sortColumn = "parts_id";
		}
		$sortDirection = $this->_request->getParameter("sort_dir");
		if (empty($sortDirection)) {
			$sortDirection = "DESC";
		}
		$orderParams[$sortColumn] = $sortDirection;

		$sql = "SELECT parts_id, parts_name, insert_time, insert_user_id, insert_user_name ".
				"FROM {blogparts_parts} ".
				$this->_db->getOrderSQL($orderParams);
		$blogpartsList = $this->_db->execute($sql);
		if ($blogpartsList === false) {
			$this->_db->addError();
			return false;
		}
		
		return $blogpartsList;
	}

	/**
	 * カレントのBlogpartsデータを取得する
	 *
     * @return string	カレントBlogpartsデータ
	 * @access	public
	 */
	function getCurPartsData()
	{
		$sql = "SELECT P.parts_id, P.parts_name, P.parts_script ".
				"FROM {blogparts_parts} P ".
				"INNER JOIN {blogparts_block} B ".
				"ON B.parts_id = P.parts_id ".
				"WHERE B.block_id = ? ";
		$params = array($this->_request->getParameter("block_id"));
		$result = $this->_db->execute($sql, $params);
		if ($result === false) {
			$this->_db->addError();
			return false;
		}
		return $result;
	}
	
	/**
	 * Blogpartsのデータを取得する
	 *
     * @return array	partsデータ配列
	 * @access	public
	 */
	function getPartsData()
	{
		$sql = "SELECT parts_id, parts_name, parts_script ".
				"FROM {blogparts_parts} ".
				"WHERE parts_id= ? ";
		$params = array($this->_request->getParameter("parts_id"));
		$result = $this->_db->execute($sql, $params);
		if ($result === false) {
			$this->_db->addError();
			return false;
		}
		return $result;
	}

}
?>