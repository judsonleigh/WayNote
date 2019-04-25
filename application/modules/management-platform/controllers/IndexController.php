<?php
namespace Modules\ManagementPlatform\Controllers;

use Application\Model\Exception;
use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    /**
     * 控制器初始化方法
     */
    public function initialize()
    {
    }

    /**
     * 界面--登录首页
     */
	public function indexAction()
	{

    }

    /**
     * AJAX提交--管理员身份验证
     *
     * @param Array $_POST 管理员信息
     * @return string JSON
     * @example {"errorMsg":"(错误提示)"}
     */
    public function loginAction()
    {
        $this->view->disable();

        $username = trim($this->request->getPost('username'));
        $password = trim($this->request->getPost('password'));

        //创建管理员Mapper


        //$oMapperAdmin = new \Application\Model\Mappers\Admin();

        $returnJson = array();
        $returnJson['errorCode'] = 0;
        try {
            //验证管理员用户名与用户名
            $oModelAdmin = \Application\Model\Admin::checkAccount($username, $password);

            if ($oModelAdmin == false) {
                //用户名密码错误！
                $returnJson['status'] = 0;
                $returnJson['errorMsg'] = '用户名与密码错误！';
            } else {
                //用户名密码验证正确
                $oModelAdmin->setLogin();
                $returnJson['status'] = 1;
            }
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

        echo json_encode($returnJson);
    }

    /**
     * 界面--注销
     *
     */
    public function logoutAction()
    {
        $this->view->disable();
        //管理员注销登录信息
        \Application\Model\Admin::logout();
        $this->response->redirect('/' . $this->router->getModuleName() . '/');
    }
}
  