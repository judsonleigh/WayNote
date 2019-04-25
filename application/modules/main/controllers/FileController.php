<?php
namespace Modules\Main\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Logger\Adapter\File as FileAdapter;

class FileController extends Controller
{
    public function initialize()
    {

    }
    
    public function indexAction()
    {

    }

    /**
     * 界面--文件下载
     * 
     * @param String $_GET['m'] 文件标识
     */
    public function downloadAction($mark)
    {
    	if ($mark != '') {
    		//标记不为空
    		$oFileModel = \Application\Model\File::fetchByMark($mark);

    		$oFileModel->download();
    	
    		if ($oFileModel != false) { 
    			$oFileModel->download();
    		} else {
    		    $this->response->setStatusCode(404, "Not Found")->sendHeaders();
    		}
    	} else {
    		$this->response->setStatusCode(404, "Not Found")->sendHeaders();
    	}
    }
    
    /*
     * 保存上传图片
     */
    public function uploadAction(){
        $pic_path = "";
        if (isset($_FILES['uploadfile']) && is_array($_FILES['uploadfile'])) {
            $nowFileId = \Application\Model\File::upFileLoad($_FILES['uploadfile']);
            $oFileModel = \Application\Model\File::fetchById($nowFileId);
            $pic_path = $oFileModel->getFileUrl();
        } else {
            $returnJson['error'] = 1;
            $returnJson['message'] = '文件没有上传！';
        }
    
        echo json_encode(array("path"=>$pic_path));
        exit;
    }
}

