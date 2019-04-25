<?php
/**
 * Config
 *
 * 系统配置 控制器
 *
 * @author Judson-Leigh
 * @version $Id:$
 */

namespace Modules\ManagementPlatform\Controllers;

use Application\Model\Exception;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Controller;

class ConfigController extends Controller
{

    /**
     * 控制器初始化方法
     */
    public function initialize()
    {
        $this->view->setTemplateAfter('default');
        \Application\Model\Admin::permissionVerifyNowAction($this, 2);
    }

    /**
     * 界面--首页
     *
     */
    public function indexAction()
    {

    }

    /**
     * AJAX提交--获取系统配置列表
     *
     * @param int $_POST['page'] 当前页数
     * @param int $_POST['rows'] 每页记录数
     * @return string JSON
     * @example {"rows":"(当前页记录数组)","total":"(总记录数)"}
     */
    public function listAction()
    {
        $this->view->disable();

        $page = intval($this->request->getPost('page', null, 1));
        if ($page <= 0) {
            $page = 1;
        }
        $rows = intval($this->request->getPost('rows', null, 20));
        if ($rows <= 0) {
            $rows = 20;
        }

        $returnJson = array();
        $returnJson['errorCode'] = 0;

        try {
            $filter = array();
            $filter[] = array(
                'field' => 'isDel',
                'value' => '0',
                'method' => '=',
            );
            $listInfo = \Application\Model\Config::fetchList($rows, $page, null, null, $filter);

            if ($listInfo['countNow'] == 0) {
                $listInfo['rowset'] = array();
            }

            $returnJson['rows'] = $listInfo['rowset'];
            $returnJson['total'] = $listInfo['countAll'];
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

        echo json_encode($returnJson);
    }

    /**
     * AJAX提交--添加系统配置
     *
     * @param Array $_POST 系统配置信息
     * @return string JSON
     * @example {"errorMsg":"(错误提示)"}
     */
    public function insertAction()
    {
        $this->view->disable();

        $info = $this->request->getPost();

        $returnJson = array();
        $returnJson['errorCode'] = 0;

        try {
            $id = \Application\Model\Config::insert($info);
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

        echo json_encode($returnJson);
    }

    /**
     * AJAX--修改系统配置信息
     *
     * @param Array $_POST 系统配置信息
     * @param int $_GET['id'] 系统配置编号
     * @return string JSON
     * @example {"errorMsg":"(错误提示)"}
     */
    public function updateAction($id = 0)
    {
        $this->view->disable();

        $info = $this->request->getPost();

        $id = intval($id);

        $returnJson = array();
        $returnJson['errorCode'] = 0;
        try {
            $oNowModel = \Application\Model\Config::fetchById($id);
            $oNowModel->update($info);
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

        echo json_encode($returnJson);
    }

    /**
     * AJAX--删除系统配置信息
     *
     * @param int $_POST['id'] 系统配置编号
     * @return string JSON
     * @example {"errorMsg":"(错误提示)","success":"(是否成功)"}
     *
     */
    public function deleteAction()
    {
        $this->view->disable();

        $id = intval($this->request->getPost('id', null, 0));

        $returnJson = array();
        $returnJson['errorCode'] = 0;
        try {
            $oNowModel = \Application\Model\Config::fetchById($id);
            $oNowModel->delete();
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }
        echo json_encode($returnJson);
    }

}

