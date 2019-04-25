<?php
/**
 * File
 * 
 * 文件 Model类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model;

class File extends \iwaycms\Base\Model {

    /**
     * 默认类名
     *
     * @const string
     */
    const _DEFAULT_CLASSNAME = 'File';
	
	/**
	 * 数据库 表列名
	 * 
	 * @property Array
	 */
	protected $_properties = array(
			'id',
			'mark',
			'filenameSrc',
			'size',
			'mimeType',
			'localPath',
			'createTime',
			'idDel',
	);
	
	/**
	 * 修改信息
	 * 
	 * @param Array $info 信息（数组）
	 * @param int $id 信息主键编号 
	 * @return bool 修改是否成功
	 */
	public function update($info, $id = NULL) {
		if ($id == NULL) {
			$id = $this->id;	
		}	
		return parent::update($info, $id);
	}
	
	/**
	 * 删除信息
	 * 
	 * @param int $id 信息主键编号
	 * @return bool 删除是否成功
	 */
	public function delete($id = NULL) {
		if ($id == NULL) {
			$id = $this->id;
		}
		$info = array(
				'isDel' => 1,
				);
		return parent::update($info, $id);
	}
	
	/**
	 * 文件下载函数
	 */
	public function download(){
		//获取配置文件下载路径
		$config = $GLOBALS['config'];
		
		$uploadPath = $config['file']['upload_path'];
		$localFileName = $uploadPath . $this->localPath;

		// 判断文件是否存在
		if (! file_exists ( $localFileName )) {
			header ( "Content-type: text/html; charset=utf-8" );

            $errorInfo = 'File not found!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['FILE_CANNOT_BE_OPENED'],
                \Application\Model\Exception::LEVEL['WARNING'],
                __FILE__,
                __LINE__);
            return;
		} else {
			$file = fopen ( $localFileName, "r" );
			$filename = $this->filenameSrc;
			$filename = iconv('UTF-8', 'GBK', $filename);
			
			$dotSize = strpos($filename,'.');
			if($filename == false || $dotSize === 0) {
				$nowTime = date('mdhis').rand(1,999);
				$filename = 'file-' . $nowTime . '.' . pathinfo($this->filenameSrc, PATHINFO_EXTENSION);
			}
			header("Content-type: " . $this->mimeType);
            header("Accept-Ranges: bytes" );
            header("Accept-Length: " . $this->size );
			if ($this->mimeType != 'application/x-shockwave-flash') {
				//非FLASH文件
				$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
				if(strpos($agent, 'android') !== false) {
					//判断是否为安卓系统
					$filename = iconv("gb2312", "UTF-8", $filename);
				}
				header("Content-Disposition: attachment; filename=\"". $filename . "\"");
			}

			echo fread( $file, $this->size );
			fclose( $file );
		}
	}
	
	/**
	 * 取文件内容
	 */
	public function getContent(){
	    //获取配置文件下载路径
	    $config = $GLOBALS['registry']->config;
	    $uploadPath = $config['file']['upload_path'];
	    $localFileName = $uploadPath . $this->localPath;
	
	    // 判断文件是否存在
	    if (! file_exists ( $localFileName )) {
            $errorInfo = 'File not found!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['FILE_CANNOT_BE_OPENED'],
                \Application\Model\Exception::LEVEL['WARNING'],
                __FILE__,
                __LINE__);
            return false;
	    } else {
	        $file = fopen ( $localFileName, "r" );
	        $fileContent = fread( $file, $this->size );
	        fclose( $file );
	        return $fileContent;
	    }
	}
	
	/**
	 * 取文件名Url
	 */
	public function getFileUrl(){
        $config = $GLOBALS['config'];

		if ($config['file']['upload_type'] == 1) {
			//直接返回路径
			$strUrl = $config['file']['upload_url'] . $this->localPath;
			
		} else {
			//返回标识下载地址
			$strUrl = $config['file']['download_path'] . $this->mark;
		}
		return $strUrl;
	}
	
	/**
	 * 取域名Url
	 * @param string 资源名
	 * @return string 资源绝对路径名
	 */
	static public function getIdnUrl($fileurl){
        $config = $GLOBALS['config'];
	    $idnUrl = $config['file']['domain'];
	    return $idnUrl.$fileurl;
	}

    /**
     * 添加信息
     *
     * @param Array $info 信息（数组）
     * @return int 新ID
     */
    static public function insert($info) {
        //提交字段判断
        if ( isset($info['mark']) == FALSE || trim($info['mark']) == '') {
            $this->_errorInfo = '文件标记不许为空！';
            return false;
        }
        if ( isset($info['size']) == FALSE || trim($info['size']) == '') {
            $this->_errorInfo = '文件大小不许为空！';
            return false;
        }
        if ( isset($info['localPath']) == FALSE || trim($info['localPath']) == '') {
            $this->_errorInfo = '服务器存放路径不许为空！';
            return false;
        }
        return parent::insert($info);
    }

    /**
     * 文件上传函数
     *
     * @param array $uploadFile 上传文件数组
     * @return int 文件编号（上传失败 返回False）
     */
    static public function upFileLoad($upLoadFile) {
        // 获取配置文件上传路径

        $config = $GLOBALS['config'];

        if ($upLoadFile['error'] != 0) {
            $errorInfo = 'File upload failed!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['FILE_UPLOAD_FAILED'],
                \Application\Model\Exception::LEVEL['WARNING'],
                __FILE__,
                __LINE__);
            return false;
        }

        //生成文件名  ，文件夹名
        $loaclFileName = date( "hms" ) . rand ( 100000, 999999 );
        $yearDir = date( "Y" );
        $dayDir = date( "md" );

        //本地保存
        $upLoadPath = $config['file']['upload_path'];
        //判断目录是否存在
        if (! file_exists( $upLoadPath . '/' . $yearDir)) {

            mkdir ($upLoadPath . '/' . $yearDir, 0777);
        }

        //判断目录是否存在
        if (! file_exists( $upLoadPath . '/' . $yearDir .'/' . $dayDir)) {
            mkdir ($upLoadPath . '/' . $yearDir . '/' . $dayDir, 0777);
        }

        $fileName = $upLoadFile['name'];
        if ($config['file']['upload_type'] == 1)
        {
            //上传至网站目录下

            $arrayFileInfo = pathinfo($fileName);

            if (! move_uploaded_file($upLoadFile['tmp_name'], $upLoadPath . '/' . $yearDir . '/' . $dayDir . '/' . $loaclFileName . '.' . $arrayFileInfo['extension'] )) {
                $errorInfo = 'File save failed!';
                throw new \Application\Model\Exception($errorInfo,
                    \Application\Model\Exception::TYPE['FILE_CANNOT_BE_WRITTEN'],
                    \Application\Model\Exception::LEVEL['WARNING'],
                    __FILE__,
                    __LINE__);
                return false;
            } else {
                $localPath = '/' . $yearDir . '/' . $dayDir . '/' . $loaclFileName . '.' . $arrayFileInfo['extension'];
            }
        }else
        {
            //上传至不可访问目录
            if (! move_uploaded_file($upLoadFile['tmp_name'], $upLoadPath . '/' . $yearDir . '/' . $dayDir . '/' . $loaclFileName )) {
                $errorInfo = 'File save failed!';
                throw new \Application\Model\Exception($errorInfo,
                    \Application\Model\Exception::TYPE['FILE_CANNOT_BE_WRITTEN'],
                    \Application\Model\Exception::LEVEL['WARNING'],
                    __FILE__,
                    __LINE__);
                return false;
            } else {
                $localPath = '/' . $yearDir . '/' . $dayDir . '/' . $loaclFileName;
            }
        }

        //编码转换
        //$fileName = iconv('GBK', 'UTF-8', $upLoadFile["name"]);
        //返回上传文件信息数组
        $fileMark = base_convert(md5($localPath),16,36); //生成标识码
        $uploadFileInfo = array (
            'mark' => $fileMark,
            'filenameSrc' => $fileName,
            'size' => $upLoadFile["size"],
            'mimeType' => $upLoadFile['type'],
            'localPath' => $localPath,
            'isDel' => 0,
        );

        return self::insert($uploadFileInfo);
    }

    /**
     * 通过文件标识取文件Model
     *
     * @param String $mark 文件标识
     * @return \Application\Model\File 文件Model (错误返回false)
     */
    static public function fetchByMark($mark) {
        $mark = trim($mark);
        if ($mark == '') {
            return false;
        }
        $dbTableClass = self::_NAMESPACE . 'DbTable\\' . self::_DEFAULT_CLASSNAME;
        $strMapperClass = self::_NAMESPACE . 'Mappers\\' . self::_DEFAULT_CLASSNAME;

        $dbTable = new $dbTableClass;
        $db = $dbTable->getAdapter();
        $where = $db->quoteInto($strMapperClass::COLS_MAP['mark'] . ' = ?', $mark);
        $where .= $db->quoteInto(' AND ' . $strMapperClass::COLS_MAP['isDel'] . ' = ?', 0);

        $row = $dbTable->fetchRow($where);
        if ($row != NULL) {
            //有文件
            $id = $row[$strMapperClass::COLS_MAP['id']];
            return self::fetchById($id);
        } else {
            //无文件
            $errorInfo = 'The file specified does not exist!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NO_RESOURCE'],
                \Application\Model\Exception::LEVEL['WARNING'],
                __FILE__,
                __LINE__);
            return false;
        }
    }

    /**
     * 根据编号取信息
     *
     * @param int $id 编号
     * @return \Application\Model\File Model
     */
    static public function fetchById($id) {
        return parent::fetchById($id);
    }
	
}