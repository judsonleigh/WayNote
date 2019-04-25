<?php
/**
 * Admin
 *
 * 管理员 控制器
 *
 * @author Judson-Leigh
 * @version $Id:$
 */

namespace Modules\ManagementPlatform\Controllers;

use Application\Model\Exception;
use Phalcon\Mvc\Controller;

class AdminController extends Controller
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
     */
    public function indexAction()
    {

    }
    
    /**
     * AJAX提交--获取管理员列表
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
    	$filter = array();
    	$filter[] = array(
    			'field' => 'isDel',
    			'value' => '0',
    			'method' => '=',
    	);
    	$listInfo = \Application\Model\Admin::fetchList($rows, $page, null, null, $filter);

        if ($listInfo['countNow'] == 0) {
            $returnJson['rows'] = array();
        } else {
            $returnJson['rows'] = array();
            foreach ($listInfo['rowset'] as $key => $row) {
                $returnJson['rows'][] = array(
                    'adminId' => $row['adminId'],
                    'username' => $row['username'],
                    'realname' => $row['realname'],
                    'type' => $row['type'],
                    'status' => $row['status'],
                    'isDel' => $row['isDel'],
                    'createTime' => $row['createTime'],
                );
            }
        }
    	
    	$returnJson['total'] = $listInfo['countAll'];

        echo json_encode($returnJson);
    }
    
    /**
     * AJAX提交--添加管理员
     *
     * @param Array $_POST 管理员信息
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
    	    $id = \Application\Model\Admin::insert($info);
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

        echo json_encode($returnJson);
    }
    
    /**
     * AJAX--修改管理员信息
     * 
     * @param Array $_POST 管理员信息
     * @param int $_GET['id'] 管理员编号
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
            $oNowModel = \Application\Model\Admin::fetchById($id);
            //判断管编号是否存在
            if ($oNowModel != false) {
                $oNowModel->update($info);
            }
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

        echo json_encode($returnJson);
    }
    
    /**
     * AJAX--修改管理员密码
     *
     * @param Array $_POST['pwd'] 管理员密码
     * @param int $_GET['id'] 管理员编号
     * @return string JSON
     * @example {"errorMsg":"(错误提示)"}
     */
    public function setPassAction($id = 0)
    {
        $this->view->disable();

        $id = intval($id);
        $passwd = trim($this->request->getPost('pwd', null, ''));

        $returnJson = array();
        $returnJson['errorCode'] = 0;

        try {
    	    $oNowModel = \Application\Model\Admin::fetchById($id);
            if (empty($oNowModel) == false) {
                $oNowModel->setPassword($passwd);
            }
    	} catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

        echo json_encode($returnJson);
    }
    
    /**
     * AJAX--删除管理员信息
     * 
     * @param int $_POST['id'] 管理员编号
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

            $oNowModel = \Application\Model\Admin::fetchById($id);
            if (empty($oNowModel) == false) {
                $oNowModel->delete();
            }
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

        echo json_encode($returnJson);
    }

    /**
     * AJAX--用户名可用性检测
     *
     * @param int $_GET['id'] 管理员编号
     * @param int $_GET['id'] 管理员编号
     * @return string JSON
     * @example {"errorMsg":"(错误提示)","success":"(是否成功)"}
     *
     */
    public function checkUsernameAction()
    {
        $this->view->disable();
        $username = trim($this->request->getPost('username', null, ''));

        $returnJson = array();
    	if (\Application\Model\Admin::checkRepeat('username', $username) == true) {
    		$returnJson = false;
    	} else {
    		$returnJson = true;
    	}
    	echo json_encode($returnJson);
    }

}

