<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * faq_actionコンポーネント
 *
 * @package     NetCommons Components
 * @author      Ka
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_Components_Action
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

	var $post_trackback_data = array();
	var $tb_result=array();

	/**
	 * コンストラクター
	 *
	 * @access	public
	 */
	function Faq_Components_Action()
	{
		$this->_container =& DIContainerFactory::getContainer();
		$this->_db =& $this->_container->getComponent("DbObject");
	}

	/**
	 *
	 * 質問削除処理
	 * @param  int    faq_id
	 * @return boolean
	 * @access public
	 */
	function delFaq($faq_id) {
		$params = array(
			"faq_id"=>intval($faq_id)
		);
    	$result = $this->_db->deleteExecute("faq", $params);
    	if($result === false) {
    		return false;
    	}

    	$result = $this->_db->deleteExecute("faq_question", $params);
    	if($result === false) {
    		return false;
    	}

    	$result = $this->_db->deleteExecute("faq_category", $params);
    	if($result === false) {
    		return false;
    	}

    	return true;
	}
}
?>
