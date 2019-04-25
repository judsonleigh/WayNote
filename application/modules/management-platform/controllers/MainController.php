<?php
/**
 * Main
 *
 * 主框架 控制器
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Modules\ManagementPlatform\Controllers;

use Phalcon\Mvc\Controller;

class MainController extends Controller
{
    public function initialize()
    {
        $this->view->setTemplateAfter('default');
        \Application\Model\Admin::permissionVerifyNowAction($this, 2);
    }

	public function indexAction()
	{
	}

    public function testAction()
    {

    }
}