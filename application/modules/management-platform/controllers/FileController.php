<?php
/**
 * File
 *
 * 文件 控制器
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Modules\ManagementPlatform\Controllers;

use Phalcon\Mvc\Controller;

class FileController extends Controller
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
        $this->view->disable();
    }
    
    /**
     * AJAX提交--文件上传
     */
    public function uploadAction()
    {

        $this->view->disable();

    	$returnJson = array();
    	if (isset($_FILES['imgFile']) && is_array($_FILES['imgFile'])) {
	  		$nowFileId = \Application\Model\File::upFileLoad($_FILES['imgFile']);

	  		if ($nowFileId > 0) {
	  			$returnJson['error'] = 0;
	  			$oFileModel = \Application\Model\File::fetchById($nowFileId);
	  			$returnJson['url'] = $oFileModel->getFileUrl();
	  		} else {
	  			$returnJson['error'] = 1;
	  			$returnJson['message'] = \Application\Model\File::getErrorInfo();
	  		}
    	} else {
    		$returnJson['error'] = 1;
    		$returnJson['message'] = '文件没有上传！';
    	}
    	echo json_encode($returnJson);
    }
    
    /**
     * AJAX提交--Ckeditor 文件上传
     */
    public function uploadCkAction()
    {
        $this->view->disable();

    	$callback = $this->request->get('CKEditorFuncNum');
    	
    	$errorMessage = '';
    	$uploadUrl = '';
    	if (isset($_FILES['upload']) && is_array($_FILES['upload'])) {

    		$nowFileId = \Application\Model\File::upFileLoad($_FILES['upload']);
    		
    		if ($nowFileId > 0) {
    			$oFileModel = \Application\Model\File::fetchById($nowFileId);
    			$uploadUrl = $oFileModel->getFileUrl();

    		} else {
    			$errorMessage = \Application\Model\File::getErrorInfo();
    		}
    		
    	} else {
    		$errorMessage = '文件没有上传！';
    	}
    	echo '<script type="text/javascript">';
    	if ($errorMessage != '') {
    		echo 'window.parent.CKEDITOR.tools.callFunction(' . $callback . ',"","' . $errorMessage . '");';
    	} else {
    		echo 'window.parent.CKEDITOR.tools.callFunction(' . $callback . ',"' . $uploadUrl . '");';
    	}
    	echo '</script>';
    }
}

