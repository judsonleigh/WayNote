<?php
/**
 * Myself
 *
 * 个人信息管理 控制器
 *
 * @author Judson-Leigh
 * @version $Id:$
 */

namespace Modules\ManagementPlatform\Controllers;

use Phalcon\Mvc\Controller;

class MyselfController extends Controller
{	
	/**
	 * 控制器初始化方法
	 */
    public function initialize()
    {
        $this->view->setTemplateAfter('default');
        \Application\Model\Admin::permissionVerifyNowAction($this, 1);

    }

    /**
     * 界面--首页
     */
    public function indexAction()
    {

    }

    /**
     * AJAX--修改当前管理员密码
     *
     * @return string JSON
     * @example {"errorMsg":"(错误提示)"}
     */
    public function setPassAction()
    {
        $this->view->disable();

        $returnJson = array();
        $returnJson['errorCode'] = 0;


        //获取新密码
        $passwd = trim($this->request->getPost('pwd', null, ''));


        try {
            $oNowAdmin = \Application\Model\Admin::checkLogin();
            //修改管理员密码
            $oNowAdmin->setPassword($passwd);
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

        echo json_encode($returnJson);
    }

}

