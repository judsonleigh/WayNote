<?php
/**
 * AdminGroup
 *
 * 管理员组 控制器
 *
 * @author Judson-Leigh
 * @version $Id:$
 */

namespace Modules\ManagementPlatform\Controllers;

use Phalcon\Mvc\Controller;

class AdminGroupController extends Controller
{	
	/**
	 * 控制器初始化方法
	 */
    public function initialize() {
        $this->view->setTemplateAfter('default');
        \Application\Model\Admin::permissionVerifyNowAction($this, 2);
    }

    /**
     * 界面--首页
     */
    public function indexAction() {
		
    	
    }
    
    /**
     * AJAX提交--获取管理员组列表
     * 
     * @param int $_POST['page'] 当前页数
     * @param int $_POST['rows'] 每页记录数
     * @return string JSON 
     * @example {"rows":"(当前页记录数组)","total":"(总记录数)"}
     */
    public function listAction() {
        $this->view->disable();


        $page = intval($this->request->getPost('page', null, 1));
        if ($page <= 0) {
            $page = 1;
        }
        $rows = intval($this->request->getPost('rows', null, 20));
        if ($rows <= 0) {
            $rows = 20;
        }

    	//返回Json数组
    	$returnJson = array();
    	$filter = array();
    	$filter[] = array(
    			'field' => 'isDel',
    			'value' => '0',
    			'method' => '=',
    	);
    	//取管理员组列表
    	$listInfo = \Application\Model\AdminGroup::fetchList($rows, $page, null, null, $filter);

    	if ($listInfo['countNow'] == 0) {
            $listInfo['rowset'] = array();
        }
    	
    	$returnJson['rows'] = $listInfo['rowset'];
    	$returnJson['total'] = $listInfo['countAll'];
    	
    	echo json_encode($returnJson);
    }
    
    /**
     * AJAX提交--添加管理员组
     *
     * @param Array $_POST 管理员组信息
     * @return string JSON 
     * @example {"errorMsg":"(错误提示)"}
     */
    public function insertAction() {
        $this->view->disable();

    	//取所有参数
        $info = $this->request->getPost();
        $returnJson = array();
        $returnJson['errorCode'] = 0;

        try {
            //插入数据库
            \Application\Model\AdminGroup::insert($info);
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }
    	
    	echo json_encode($returnJson);
    }
    
    /**
     * AJAX--修改管理员组信息
     * 
     * @param Array $_POST 管理员组信息
     * @param int $_GET['id'] 管理员组编号
     * @return string JSON 
     * @example {"errorMsg":"(错误提示)"}
     */
    public function updateAction($id = 0) {
        $this->view->disable();

        $info = $this->request->getPost();
    	//取管理员组编号
        $id = intval($id);
    	$returnJson = array();
        $returnJson['errorCode'] = 0;

        try {
            $oNowModel = \Application\Model\AdminGroup::fetchById($id);
            //修改管理员组信息
            $oNowModel->update($info);
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

        echo json_encode($returnJson);
    }
    
    /**
     * AJAX--删除管理员组信息
     * 
     * @param int $_POST['id'] 管理员组编号
     * @return string JSON 
     * @example {"errorMsg":"(错误提示)","success":"(是否成功)"}
     *
     */
    public function deleteAction() {
        $this->view->disable();

    	//取管理员组编号
        $id = intval($this->request->getPost('id', null, 0));
    	$returnJson = array();
        $returnJson['errorCode'] = 0;

        try {
            $oNowModel = \Application\Model\AdminGroup::fetchById($id);
            //修改管理员组信息
            $oNowModel->delete();
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

        echo json_encode($returnJson);
    }
    
    /**
     * AJAX提交--获取未加入当前管理员组的管理员列表
     *
     * @param int $_GET['id'] 管理员组编号
     * @return string JSON
     * @example {"rows":"(当前页记录数组)","total":"(总记录数)"}
     */
    public function getUnjoinAdminListAction($id = 0) {
        $this->view->disable();

    	//返回Json数组
    	$returnJson = array();
        $returnJson['errorCode'] = 0;

    	//编号类型转换
    	$id = intval($id);

        try {
            $oModelAdminGroup = \Application\Model\AdminGroup::fetchById($id);

            //取管理员组中管理员列表
            $arrayGroupAdminList = $oModelAdminGroup->getAdminList();
            //管理员编号索引数组
            $arrayIndexGroupAdminList = array();
            //转管理员编号索引数组
            foreach ($arrayGroupAdminList as $oModelAdmin) {
                $arrayIndexGroupAdminList[$oModelAdmin->adminId] = $oModelAdmin;
            }

            $filter = array();
            //设置只取未删除的管理员
            $filter[] = array(
                'field' => 'status',
                'value' => '0',
                'method' => '>=',
            );
            $order = array();
            //设置按管理员编号排序
            $order[] = array(
                'field' => 'adminId',
                'desc' => true,
            );

            //取管理员列表
            $listInfo = \Application\Model\Admin::fetchList(0, 1, $order, null, $filter);
            //
            //循环处理管理员列表
            foreach ($listInfo['rowset'] as $nowKey => $nowAdmin) {
                if (isset($arrayIndexGroupAdminList[$nowAdmin['adminId']]) == false) {
                    //管理员未加入，则显示
                    $returnJson['rows'][] = $nowAdmin;
                }
            }
            $returnJson['total'] = count($returnJson['rows']);

        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

        echo json_encode($returnJson);
    }
    
    /**
     * AJAX提交--获取当前管理员组管理员列表
     *
     * @param int $_GET['id'] 管理员组编号
     * @return string JSON
     * @example {"rows":"(当前页记录数组)","total":"(总记录数)"}
     */
    public function getGroupAdminListAction($id = 0)
    {
        $this->view->disable();

        //返回Json数组
        $returnJson = array();
        $returnJson['errorCode'] = 0;

        //编号转换
        $id = intval($id);

        try {
            //取管理员组Model
            $oModelAdminGroup = \Application\Model\AdminGroup::fetchById($id);

            //取管理员组中管理员列表
            $arrayGroupAdminList = $oModelAdminGroup->getAdminList();

            $arrayIndexGroupAdminList = array();
            //将对象转数组
            foreach ($arrayGroupAdminList as $oModelAdmin) {
                $row['adminId'] = $oModelAdmin->adminId;
                $row['username'] = $oModelAdmin->username;
                $row['realname'] = $oModelAdmin->realname;
                $row['status'] = $oModelAdmin->status;
                $arrayIndexGroupAdminList[] = $row;
            }

            //传入Json 数组
            $returnJson['rows'] = $arrayIndexGroupAdminList;
            $returnJson['total'] = count($arrayIndexGroupAdminList);
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

        echo json_encode($returnJson);
    }
    
    /**
     * AJAX提交--设置管理员组管理员
     *
     * @param int $_POST['id'] 管理员组编号
     * @param int $_POST['adminId'] 管理员编号
     * @param int $_POST['type'] 设置类型 0.移除 1.加入
     * @return string JSON
     * @example {"errorMsg":"(错误提示)","success":"(是否成功)"}
     */
    public function setGroupAdminAction() {
        $this->view->disable();
    	//取传入的管理员组编号
        $id = intval($this->request->getPost('id', null, 0));
        if ($id <= 0) {
            $id = 0;
        }
    	//取传入的管理员编号
        $adminId = intval($this->request->getPost('adminId', null, 0));
        if ($adminId <= 0) {
            $adminId = 0;
        }
    	//取传入的设置类型
        $type = intval($this->request->getPost('type', null, 0));
        if ($type <= 0) {
            $type = 0;
        }

    	
    	//返回Json数组
    	$returnJson = array();
        $returnJson['errorCode'] = 0;

        //判断设置类型是否正确
    	if ($type != 0 && $type != 1) {
            $returnJson['errorCode'] = 100;
            $returnJson['errorSerialNumber'] = 0;
            $returnJson['errorMsg'] = '设置类型错误';
            echo json_encode($returnJson);
    		return;
    	}
    	try {
            //取管理员组Model
            $oModelAdminGroup = \Application\Model\AdminGroup::fetchById($id);
            //判断管理员组编号是否存在
            if ($oModelAdminGroup == false) {
                $returnJson['errorMsg'] = '编号不存在！';
                echo json_encode($returnJson);
                return;
            }
            //判断设置类型
            if ($type == 1) {
                //加入
                $oModelAdminGroup->addAdmin($adminId);
            } else {
                //移除
                $oModelAdminGroup->removeAdmin($adminId);
            }
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

        echo json_encode($returnJson);
    	
    }

    /**
     * AJAX提交--获取功能列表
     *
     * @param int $_GET['id'] 管理员组编号
     * @return string JSON
     * @example {"rows":"(当前页记录数组)","total":"(总记录数)"}
     */
    public function getFunctionListAction($id = 0)
    {
        $this->view->disable();

        //返回Json数组
        $returnJson = array();
        $returnJson['errorCode'] = 0;

        //编号转换
        $id = intval($id);

        try {
            //取当前管理员组Model
            $oModelAdminGroup = \Application\Model\AdminGroup::fetchById($id);
            //取当前有权限的功能Model列表
            $arrayFunctionModelList = $oModelAdminGroup->getFunctionList();
            $arrayFunctionModelIndex = array();    //功能信息索引数组
            if (empty($arrayFunctionModelList) == false) {
                //有权限的功能Model列表不为空，将其进行索引
                foreach ($arrayFunctionModelList as $row) {
                    $arrayFunctionModelIndex[$row->id] = $row;
                }
            }

            //状态过滤
            $filter = array();
            $filter[] = array(
                'field' => 'status',
                'value' => '1',
                'method' => '=',
            );
            $arrayFunctionList = \Application\Model\Func::fetchList(0, 1, null, null, $filter);

            $returnJson['rows'] = array();
            foreach ($arrayFunctionList['rowset'] as $row) {
                if (isset($arrayFunctionModelIndex[$row['id']])) {
                    $row['purview'] = '●';
                } else {
                    $row['purview'] = '';
                }
                $returnJson['rows'][] = $row;
            }

            //传入Json 数组
            $returnJson['total'] = $arrayFunctionList['countAll'];
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }
        echo json_encode($returnJson);
    }
    
    /**
     * AJAX提交--设置管理员组功能权限
     *
     * @param int $_POST['id'] 管理员组编号
     * @param int $_POST['functionId'] 功能编号
     * @param int $_POST['type'] 设置类型 0.删除 1.加入
     * @return string JSON
     * @example {"errorMsg":"(错误提示)","success":"(是否成功)"}
     */
    public function setFunctionAction($id = 0,$functionId = 0,$type = 0) {
        $this->view->disable();

        //取传入的管理员组编号
    	$id = intval($id);
    	//取传入的功能编号
        $functionId = intval($functionId);
    	//取传入的设置类型
        $type = intval($type);
    	 
    	//返回Json数组
    	$returnJson = array();
        $returnJson['errorCode'] = 0;

        //判断设置类型是否正确
    	if ($type != 0 && $type != 1) {
            $returnJson['errorCode'] = 100;
            $returnJson['errorSerialNumber'] = 0;
            $returnJson['errorMsg'] = '设置类型错误';
            echo json_encode($returnJson);
    		return;
    	}

    	try {
    	    //取管理员组Model
            $oModelAdminGroup = \Application\Model\AdminGroup::fetchById($id);
            //判断设置类型
            if ($type == 1) {
                //加入
                $oModelAdminGroup->addFunction($functionId);
            } else {
                //移除
                $oModelAdminGroup->removeFunction($functionId);
            }
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }
    	 
        echo json_encode($returnJson);
    }
}

