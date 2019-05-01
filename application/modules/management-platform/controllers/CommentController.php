<?php
/**
 * Comment
 *
 * 评论 控制器
 *
 * @author Judson-Leigh
 * @version $Id:$
 */

namespace Modules\ManagementPlatform\Controllers;

use Application\Model\Exception;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Controller;

class CommentController extends Controller
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
     * AJAX提交--评论列表
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
            $listInfo = \Application\Model\Comment::fetchList($rows, $page, null, null, $filter);

            if ($listInfo['countNow'] == 0) {
                $listInfo['rowset'] = array();
            } else {
                foreach ($listInfo['rowset'] as $key=>$row) {
                    $oBook = \Application\Model\Book::fetchById($row['bookId']);
                    if (empty($oBook) == false) {
                        $listInfo['rowset'][$key]['bookName'] = $oBook->bookName;
                    }
                }
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
     * AJAX--审核评论
     *
     * @param int $_GET['id'] 书籍编号
     * @param int $_GET['audit'] 审核状态 1.审核通过 -1.审核失败
     * @return string JSON
     * @example {"errorMsg":"(错误提示)"}
     */
    public function auditAction($id, $audit)
    {
        $this->view->disable();

        $info = $this->request->getPost();

        $id = intval($id);
        $audit = intval($audit);

        $returnJson = array();
        $returnJson['errorCode'] = 0;
        try {
            $oNowModel = \Application\Model\Comment::fetchById($id);
            $oNowModel->audit($audit);
        } catch (Exception $e) {
            $returnJson['errorCode'] = $e->getCode();
            $returnJson['errorSerialNumber'] = $e->getSerialNumber();
            $returnJson['errorMsg'] = $e->getMessage();
        }

        echo json_encode($returnJson);
    }

}

