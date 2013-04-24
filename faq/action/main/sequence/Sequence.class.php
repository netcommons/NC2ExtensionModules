<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * タスク順序変更アクションクラス
 *
 * @package     NetCommons
 * @author      Ka
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_Action_Main_Sequence extends Action
{
    // 使用コンポーネントを受け取るため
    var $db = null;
    
    var $faq_id = null;
	var $drag_id = null;
	var $drop_id = null;
	var $position = null;

    /**
     * タスク順序変更アクション
     *
     * @access  public
     */
    function execute()
    {   
        /*
    	$sql = " select ".$this->faq_id;
    	
    	$x = $this->db->execute($sql, array());
    	//if ($x===false){
    		$this->db->addError();
            return "error";
    	//}
    	*/
    	
    	//get seq info
    	$params = array(
            $this->drag_id,
            $this->drop_id,
            $this->faq_id
        );
        
        $sql = "SELECT question_id, display_sequence ".
                "FROM {faq_question} ".
                "WHERE (question_id = ? ".
                "OR question_id = ?) ".
                "AND faq_id = ?";
        $result = $this->db->execute($sql, $params);
        if ($result === false ||
            count($result) != 2) {
            $this->db->addError();
            return "error";
        }
        $sequences[$result[0]["question_id"]] = $result[0]["display_sequence"];
        $sequences[$result[1]["question_id"]] = $result[1]["display_sequence"];
        
        $dragSeq = $sequences[$this->drag_id];
        $dropSeq = $sequences[$this->drop_id];
        
        if ($this->position=='top') {
        	$dropSeq--;
        }
        
    	//update seq info
    	$params = array(
            $this->faq_id,
            $dragSeq,
            $dropSeq
        );
        
        if ($dragSeq > $dropSeq) {
            $sql = "UPDATE {faq_question} ".
                    "SET display_sequence = display_sequence + 1 ".
                    "WHERE faq_id = ? ".
                    "AND display_sequence < ? ".
                    "AND display_sequence > ?";
        } else {
            $sql = "UPDATE {faq_question} ".
                    "SET display_sequence = display_sequence - 1 ".
                    "WHERE faq_id = ? ".
                    "AND display_sequence > ? ".
                    "AND display_sequence <= ?";
        }
        
        $result = $this->db->execute($sql, $params);
        if($result === false) {
            $this->db->addError();
            return "error";
        }
        
        if ($dragSeq > $dropSeq) {
            $dropSeq++;
        }
        $params = array(
            $dropSeq,
            $this->drag_id
        );

        $sql = "UPDATE {faq_question} ".
                "SET display_sequence = ? ".
                "WHERE question_id = ?";
        $result = $this->db->execute($sql, $params);
        if($result === false) {
            $this->db->addError();
            return "error";
        }
		
		return "success";
    }
}
?>