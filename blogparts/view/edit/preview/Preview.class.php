<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * BLOGPARTS参照画面表示
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Blogparts_View_Edit_Preview extends Action
{
    // Validatorの値をセットするため
	var $parts_data = null;
	
    /**
     * BLOGPARTS参照画面表示
     *
     * @access  public
     */
    function execute()
    {
		return "success";
    }
}
?>
