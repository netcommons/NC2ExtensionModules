<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * [[機能説明]]
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Faq_Action_Main_Post extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	var $faq_id = null;
	var $question_name = null;
	var $category_id = null;
	var $question_answer = null;
	var $question_id = null;

	// バリデートによりセット
	var $faq_obj = null;

	// 使用コンポーネントを受け取るため
	var $faqView = null;
	var $db = null;
	//var $whatsnewAction = null;
	var $request = null;
	var $session = null;
	var $configView = null;

	// 値をセットするため

	/**
	 * [[機能説明]]
	 *
	 * @access  public
	 */
	function execute()
	{
		$auth_id = $this->session->getParameter("_auth_id");
		if($auth_id < $this->faq_obj['faq_authority']) {
			return 'error';
		}

		if (!empty($this->question_id)) {
			$params = array(
                    "category_id" => $this->category_id,
                    "question_name" => $this->question_name,
                    "question_answer" => $this->question_answer
			);
			$whereParams = array("question_id"=>$this->question_id);

			$result = $this->db->updateExecute("faq_question", $params,  $whereParams, true);
			if ($result === false) {
				return 'error';
			}
		} else {
            
			$params = array('faq_id'=>$this->faq_id);
			$sql = "SELECT MAX(display_sequence) ".
                    "FROM {faq_question} ".
                    "WHERE faq_id = ?";
			$sequences = $this->db->execute($sql, $params, null, null, false);
			if ($sequences === false) {
				$this->_db->addError();
				return false;
			}
            
			$params = array(
	                "faq_id" => $this->faq_id,
	                "display_sequence" => $sequences[0][0] + 1,
	                "category_id" => $this->category_id,
	                "question_name" => $this->question_name,
	                "question_answer" => $this->question_answer
			);

			$question_id = $this->db->insertExecute("faq_question", $params, true, "question_id");
			if($question_id === false) {
				return "error";
			}

			$this->question_id = $question_id;
		}
		
        $this->request->setParameter('category_id', null);

		return 'success';
	}
}
?>
