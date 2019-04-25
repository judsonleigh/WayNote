<?php
/**
 * Info
 *
 * 知识点 控制器
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Modules\ManagementPlatform\Controllers;

use Application\Model\Exception;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Controller;

class InfoController extends Controller
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
        $filter = [
            [
                'field' => 'isDel',
                'method' => '=',
                'value' => 0,
            ],
        ];
        $BookList = \Application\Model\Book::fetchList(0,1, null, null, $filter);

        $this->view->setVar('BookList', $BookList);
    }
    
    /**
     * AJAX提交--获取内容列表
     * 
     * @param int $_GET['cid'] 频道编号
     */
    public function listAction($bookId = 0)
    {
        $this->view->disable();

        $returnJson = array();
        $returnJson['errorCode'] = 0;

        $bookId = intval($bookId);

        try {
            if ($bookId > 0) {
                //内容存在
                $oModelBook = \Application\Model\Book::fetchById($bookId);

                $arrayContentList = $oModelBook->getInfo(0, 1, null, null, null);



                $arrayJson = array(
                    'total' => $arrayContentList['countAll'],
                );
                if ($arrayContentList['countNow'] > 0) {
                    //当前页有数据
                    $arrayJson['rows'] = $arrayContentList['rowset'];
                } else {
                    //当前页无数据
                    $arrayJson['rows'] = array();
                }

            } else {
                //内容不存在
                $arrayJson = array(
                    'total' => 0,
                    'rows' => array(),
                );
            }
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

    	echo json_encode($arrayJson);
    }

    /**
     * AJAX提交--添加内容
     *
     * @param Array $_POST 内容信息
     * @param int $_GET['fid'] 父内容编号	(为空添加根内容)
     * @return string JSON
     * @example {"errorMsg":"(错误提示)"}
     */
    public function insertAction()
    {
        $this->view->disable();

        $returnJson = array();
        $returnJson['errorCode'] = 0;

        try {
            $info = $this->request->getPost();
            \Application\Model\Info::insert($info);
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

    	echo json_encode($returnJson);
    }

    /**
     * AJAX--修改内容信息
     *
     * @param Array $_POST 内容信息
     * @param int $_GET['id'] 内容编号
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
            $oNowModel = \Application\Model\Info::fetchById($id);
            $oNowModel->update($info);
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

    	echo json_encode($returnJson);
    }

    /**
     * AJAX--删除内容信息
     *
     * @param int $_POST['id'] 内容编号
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
            $oNowModel = \Application\Model\Info::fetchById($id);
            $oNowModel->delete();
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }
    	echo json_encode($returnJson);
    }
}

