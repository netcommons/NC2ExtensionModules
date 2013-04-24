<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Blogparts追加アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Blogparts_Install extends Action
{
	var $blogpartsAction = null;
    /**
     * Blogparts追加アクション
     *
     * @access  public
     */
    function execute()
    {
    	$container =& DIContainerFactory::getContainer();
		$request =& $container->getComponent("Request");

        $request->setParameter("parts_name", BLOGPARTS_DEFAULT_NAME);
        $request->setParameter("parts_script", BLOGPARTS_DEFAULT_SCRIPT);
        if (!$this->blogpartsAction->setParts()) {
        	return false;
        }
		return true;
    }
}
?>