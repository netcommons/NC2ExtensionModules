<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Blogparts登録コンポーネント
 *
 * @package     NetCommons Components
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Blogparts_Components_Action
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
	function Blogparts_Components_Action()
	{
		$container =& DIContainerFactory::getContainer();
		$this->_db =& $container->getComponent("DbObject");
		$this->_request =& $container->getComponent("Request");
	}
	/**
	 * Blogpartsデータを削除する
	 *
     * @return boolean	true or false
	 * @access	public
	 */
	function deleteBlogparts() 
	{
		$params = array(
			"parts_id" => $this->_request->getParameter("parts_id")
		);
		
    	if (!$this->_db->deleteExecute("blogparts_parts", $params)) {
    		return false;
    	}

		return true;
	}

	/**
	 * Blogparts用ブロックデータを登録する
	 *
     * @return boolean	true or false
	 * @access	public
	 */
	function setBlock() 
	{
		$blockID = $this->_request->getParameter("block_id");
		
		$params = array($blockID);
		$sql = "SELECT block_id ".
				"FROM {blogparts_block} ".
				"WHERE block_id = ?";
		$blockIDs = $this->_db->execute($sql, $params);
		if ($blockIDs === false) {
			$this->_db->addError();
			return false;
		}

		$params = array(
			"block_id" => $blockID,
			"parts_id" => $this->_request->getParameter("parts_id")
		);
		if (!empty($blockIDs)) {
			$result = $this->_db->updateExecute("blogparts_block", $params, "block_id", true);
		} else {
			$result = $this->_db->insertExecute("blogparts_block", $params, true);
		}
        if (!$result) {
			return false;
		}

		return true;
	}

	/**
	 * Blogparts用パーツデータを登録する
	 *
     * @return boolean	true or false
	 * @access	public
	 */
	function setParts()
	{
		$container =& DIContainerFactory::getContainer();
        $session =& $container->getComponent("Session");
        $site_id = $session->getParameter("_site_id");
        $user_id = $session->getParameter("_user_id");
        $user_name = $session->getParameter("_handle");
        $time = timezone_date();
        
		$params = array(
			"parts_name" => $this->_request->getParameter("parts_name"),
			"parts_script" => $this->_request->getParameter("parts_script")
		);
		$params_footer = array(
			"insert_time" =>$time,
			"insert_site_id" => $site_id,
			"insert_user_id" => $user_id,
			"insert_user_name" => $user_name
		);
		$params_update_footer = array(
			"update_time" =>$time,
			"update_site_id" => $site_id,
			"update_user_id" => $user_id,
			"update_user_name" => $user_name
		);
		$partsID = $this->_request->getParameter("parts_id");
		if (empty($partsID)) {
			$params = array_merge($params, $params_footer, $params_update_footer);
			$result = $this->_db->insertExecute("blogparts_parts",$params, false, "parts_id");
		} else {
			$params = array_merge($params, $params_update_footer);
			$params['parts_id'] = $partsID;
			$result = $this->_db->updateExecute("blogparts_parts", $params, "parts_id", false);
		}
		if (!$result) {
			return false;
		}
		
		if (!empty($partsID)) {
        	return true;
        }
        
		$partsID = $result;
		$this->_request->setParameter("parts_id", $partsID);
        
        $this->setBlock();
        
		return "create";
	}
}
?>