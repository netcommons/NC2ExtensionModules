<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * BLOGPARTSメイン画面表示
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Blogparts_View_Main_Init extends Action
{
    // Validatorの値をセットするため
    var $parts_data = null;
    
    /**
     * BLOGPARTSメイン画面表示
     *
     * @access  public
     */
    function execute()
    {
		return "success";
    }
}
?>
