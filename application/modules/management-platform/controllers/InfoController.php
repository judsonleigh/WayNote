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
     * AJAX提交--获取知识点列表
     * 
     * @param int $_GET['bookId'] 书籍编号
     * @param int $_POST['page'] 当前页数
     * @param int $_POST['rows'] 每页记录数
     * @param int $_GET['type'] 类型
     * @param int $_GET['keyword'] 关键词
     * @return string JSON
     * @example {"rows":"(当前页记录数组)","total":"(总记录数)"}
     *
     */
    public function listAction($bookId = 0)
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

        $type = trim($this->request->get('type', null, ''));
        $keyword = trim($this->request->get('keyword', null, ''));

        $returnJson = array();
        $returnJson['errorCode'] = 0;

        $bookId = intval($bookId);

        try {
            if ($bookId > 0) {
                //书籍存在
                $oModelBook = \Application\Model\Book::fetchById($bookId);

                $filter = array();
                $filter[] = array(
                    'field' => 'isDel',
                    'value' => '0',
                    'method' => '=',
                );

                if (empty($type) == false) {
                    $filter[] = array(
                        'field' => 'type',
                        'value' => $type,
                        'method' => '=',
                    );
                }

                if (empty($keyword) == false) {
                    $filter[] = array(
                        'field' => 'title',
                        'value' => '%' . $keyword . '%',
                        'method' => 'like',
                    );
                }

                $arrayContentList = $oModelBook->getInfo($rows, $page, null, null, $filter);


                $arrayJson = array(
                    'total' => $arrayContentList['countAll'],
                );
                if ($arrayContentList['countNow'] > 0) {
                    //当前页有数据
                    $returnRowset = array();
                    foreach ($arrayContentList['rowset'] as $row){
                        $row['introduce'] = htmlspecialchars_decode($row['introduce'], ENT_QUOTES);
                        $returnRowset[] = $row;
                    }
                    $arrayJson['rows'] = $returnRowset;
                } else {
                    //当前页无数据
                    $arrayJson['rows'] = array();
                }

            } else {
                //书籍不存在
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
     * AJAX提交--获取书籍类型
     *
     * @param int $_GET['bookId'] 书籍编号
     * @return string JSON
     * @example {"rows":"(当前页记录数组)","total":"(总记录数)"}
     *
     */
    public function getBookTypeAction($bookId = 0)
    {
        $this->view->disable();

        $bookId = intval($bookId);

        $typeList[] = [
            'id' => '',
            'text' => '全部',
        ];

        try {
            if ($bookId > 0) {
                //书籍存在
                $oModelBook = \Application\Model\Book::fetchById($bookId);

                $filter = array();
                $filter[] = array(
                    'field' => 'isDel',
                    'value' => '0',
                    'method' => '=',
                );

                $contentListAll = $oModelBook->getInfo(0, 1, null, null, $filter);

                if ($contentListAll['countAll'] > 0) {
                    $types = [];
                    foreach($contentListAll['rowset'] as $row) {
                        $types[$row['type']] = $row['type'];
                    }

                    foreach($types as $nowType) {
                        $typeList[] = [
                            'id' => $nowType,
                            'text' => $nowType,
                        ];
                    }
                }

            }
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

        echo json_encode($typeList);
    }

    /**
     * AJAX提交--添加知识点
     *
     * @param Array $_POST 知识点信息
     * @param int $_GET['fid'] 父知识点编号	(为空添加根知识点)
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

            //图片上传
            if (isset($_FILES['picFile']) && is_array($_FILES['picFile']) && trim($_FILES['picFile']['name']) != '') {
                $oFileMapper = new \Application\Model\Mappers\File();
                $nowFileId = \Application\Model\File::upFileLoad($_FILES['picFile']);
                $oFileModel = \Application\Model\File::fetchById($nowFileId);
                $info['pic'] = $oFileModel->getFileUrl();
            }

            $info['introduce'] = htmlspecialchars($info['introduce'], ENT_QUOTES);

            \Application\Model\Info::insert($info);
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

    	echo json_encode($returnJson);
    }

    /**
     * AJAX--修改知识点信息
     *
     * @param Array $_POST 知识点信息
     * @param int $_GET['id'] 知识点编号
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

            //图片上传
            if (isset($_FILES['picFile']) && is_array($_FILES['picFile']) && trim($_FILES['picFile']['name']) != '') {
                $oFileMapper = new \Application\Model\Mappers\File();
                $nowFileId = \Application\Model\File::upFileLoad($_FILES['picFile']);
                $oFileModel = \Application\Model\File::fetchById($nowFileId);
                $info['pic'] = $oFileModel->getFileUrl();
            }

            $info['introduce'] = htmlspecialchars($info['introduce'], ENT_QUOTES);

            $oNowModel->update($info);
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

    	echo json_encode($returnJson);
    }

    /**
     * AJAX--删除知识点信息
     *
     * @param int $_POST['id'] 知识点编号
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

