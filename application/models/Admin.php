<?php
/**
 * Admin
 * 
 * 管理员 Model类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model;

class Admin extends \iwaycms\Base\Model {

    /**
     * 默认类名
     *
     * @const string
     */
    const _DEFAULT_CLASSNAME = 'Admin';

	/**
	 * 数据库 Admin表列名
	 * 
	 * @property Array
	 */
	protected $_properties = array(
			'adminId',
			'username',
			'realname',
			'type',
            'status',
            'isDel',
            'createTime',
	);

    /**
     * 功能权限
     *
     * @property Array
     */
    protected $_purviewFunction = array();

    /**
     * 管理员类型
     */
    const TYPE = array(
        'COMMON' => 0,  //普通管理员
        'SUPER' => 1,   //超级管理员
    );

    /**
     * 管理员类型
     */
    const STATUS = array(
        'DISABLE' => 0,   //无效
        'ENABLE' => 1,    //有效
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
			$id = $this->adminId;	
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
			$id = $this->adminId;
		}
		$info = array(
				'isDel' => 1,
				);
		return parent::update($info, $id);
	}
	
	/**
	 * 修改密码
	 *
	 * @param String $password 密码
	 * @return bool 修改是否成功
	 */
	public function setPassword($password) {
		if (empty($password)) {
            $errorInfo = 'Password is empty!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NULL'],
                \Application\Model\Exception::LEVEL['NOTICE'],
                __FILE__,
                __LINE__);
			return false;
		}

    	$info = array();
		$info['password'] = self::encryptionPassword($password);
		return $this->update($info);
	}
	
	/**
	 * 设置登录状态
	 *
	 * @return bool 修改是否成功
	 */
	public function setLogin() {

		if ($this->type == self::TYPE['COMMON']) {
			//普通管理员取权限
			$arrayAdminGroupModels = $this->getAdminGroup();

			//取管理员组有权限的功能列表
			$arrayFunctionList = array();
			foreach ($arrayAdminGroupModels as $adminGroupModel) {
				$arrayFunctionModelList = $adminGroupModel->getFunctionList();


                foreach ($arrayFunctionModelList as $oFunctionModel) {
					
					$arrayFunctionList[$oFunctionModel->functionModule][$oFunctionModel->functionController][$oFunctionModel->functionAction] = $oFunctionModel->functionName; 
				}
			}
            $this->_purviewFunction = $arrayFunctionList;
		}

        \Phalcon\DI::getDefault()->get('session')->set('oAdmin', $this);

	}
	
	/**
	 * 取当前管理员功能权限列表
	 *
	 * @return Array 功能权限列表
	 */
	public function getPurviewFunction() {
		if (empty($this->_purviewFunction) == false) {
			//已经登录
			return $this->_purviewFunction;
		} else {
			//未登录
			return NUll;
		}
	}
	
	/**
	 * 取管理员组列表
	 * 
	 * @return Array 管理员组Model列表
	 */
	public function getAdminGroup() {
		//创建管理员组关系DbTable类
		$dbTableClass = self::_NAMESPACE . 'DbTable\RelationAdminGroup';
		$dbTable = new $dbTableClass;
		$db = $dbTable->getAdapter();
		
		//设置查询条件
		$where = $db->quoteInto('admin_id = ?', $this->adminId);	//必须为当前
		$where .= $db->quoteInto(' AND status = ?', 1);		//状态必须为有效
		$order = 'admin_group_id';
		$rowset = $dbTable->fetchAll($where, $order);

        if ($rowset === false) {
            $messages = $this->getMessages();
            $errorInfo = array();
            foreach ($messages as $message) {
                $errorInfo[] = array(
                    'type' => $message->getType(),
                    'code' => $message->getCode(),
                    'field' => $message->getField(),
                    'info' => $message->getMessage(),
                );
            }
            throw new \Application\Model\Exception(json_encode($errorInfo),
                \Application\Model\Exception::TYPE['DB_SQL_SELECT_ERR'],
                \Application\Model\Exception::LEVEL['ERROR'],
                __FILE__,
                __LINE__);
            return false;
        }

		$adminGroupList = $rowset;

		if (empty($adminGroupList) == true) {
            $adminGroupList = array();
        }
		
		$strMapperClass = self::_NAMESPACE . 'Mappers\AdminGroup';
		$mapperAdminGroup = new $strMapperClass();
		
		$arrayReturn = array();
		
		//循环提取管理员Model合并到数组
		foreach ($adminGroupList as $adminGroupRow) {
            $oModelAdminGroup = $mapperAdminGroup->fetchById($adminGroupRow['admin_group_id']);
            if (empty($oModelAdminGroup) == false && $oModelAdminGroup->status == 1 && $oModelAdminGroup->isDel == 0) {
                $arrayReturn[] = $oModelAdminGroup;
            }
		}

		return $arrayReturn;
	}

    /**
     * 加密密码
     *
     * @param String $password 密码
     * @return String 加密后的密码
     */
	static public function encryptionPassword($password) {
        $password = trim($password);
        if (empty($password)) {
            $errorInfo = 'Password is empty!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NULL'],
                \Application\Model\Exception::LEVEL['NOTICE'],
                __FILE__,
                __LINE__);
            return null;
        }
        $config = \Phalcon\DI::getDefault()->get('config');

        return md5(md5($password) . $config['password']['key']);
    }

    /**
     * 当前Action权限验证
     *
     * @param \Phalcon\Mvc\Controller $oController 控制器对象
     * @param int $method 验证方法（0.不验证 1.仅登录验证 2.登录与权限验证）
     * @param String $jumpUrl OPTIONAL 跳转地址
     * @return void
     */
    static public function permissionVerifyNowAction($oController, $method, $jumpUrl = '') {

        if (get_parent_class($oController) != 'Phalcon\Mvc\Controller') {
            //控制器对象错误
            $errorInfo = 'Controller class error!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_TYPE_ERR'],
                \Application\Model\Exception::LEVEL['WARNING'],
                __FILE__,
                __LINE__);
            return;
        }

        $method = intval($method);
        if ($method == 0) {
            //不验证
            return;
        }



        $jumpUrl = trim($jumpUrl);
        if ($jumpUrl == '') {
            $jumpUrl = '/' . $oController->router->getModuleName() . '/';
        }

        if ($method > 2 || $method < 0) {
            //参数错误
            $oController->response->redirect($jumpUrl);
            return;
        }

        $oNowAdmin = \Application\Model\Admin::checkLogin();
        if (empty($oNowAdmin)) {
            //未登录
            $oController->response->redirect($jumpUrl);
            return;
        }

        if($oNowAdmin->type == \Application\Model\Admin::TYPE['SUPER']) {
            //超级管理员无需验证
            return;
        }

        $result = self::verifyAction($oController);

        //验证是否有权限
        if ($result != true) {
            $oController->response->redirect($jumpUrl);
        }

        return;
    }

    /**
     * Action权限验证
     *
     * @param \Phalcon\Mvc\Controller $oController 控制器对象
     * @param String $action OPTIONAL Action (默认为当前Action)
     * @param String $controller OPTIONAL Controller (默认为当前Controller)
     * @param String $module OPTIONAL Module (默认为当前Module)
     * @return bool 是否有权限
     */
    static public function verifyAction($oController , $action = '', $controller = '', $module = '') {
        $action = trim($action);
        $controller = trim($controller);
        $module = trim($module);

        $strClassName = get_parent_class($oController);

        if ($strClassName != 'Phalcon\Mvc\Controller' && $strClassName != 'Phalcon\Mvc\View\Engine') {
            //控制器对象错误
            $errorInfo = 'Controller class error!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_TYPE_ERR'],
                \Application\Model\Exception::LEVEL['WARNING'],
                __FILE__,
                __LINE__);
            return false;
        }

        if (empty($action) == true) {
            $action = $oController->router->getActionName();

        }
        if (empty($controller) == true) {
            $controller = $oController->router->getControllerName();
        }
        if (empty($module) == true) {
            $module = $oController->router->getModuleName();
        }

        $oNowAdmin = \Application\Model\Admin::checkLogin();

        if (empty($oNowAdmin)) {
            //未登录
            return false;
        }

        if($oNowAdmin->type == \Application\Model\Admin::TYPE['SUPER']) {
            //超级管理员
            return true;
        }

        //取功能权限列表
        $arrayPurviewFunction = $oNowAdmin->getPurviewFunction();
        if (isset($arrayPurviewFunction[$module][$controller][$action]) == false) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * 根据编号取信息
     *
     * @param int $id 编号
     * @return \Application\Model\Admin Model
     */
    static public function fetchById($id) {
        return parent::fetchById($id);
    }

    /**
     * 取列表
     *
     * @param int $pageLine OPTIONAL 分页行数
     * @param int $pageNum OPTIONAL 当前页数
     * @param Array $order OPTIONAL 排序条件
     *          <pre>
     * 				$order[n]['field'] string 字段名
     * 				$order[n]['desc'] bool 是否倒序
     *          </pre>
     * @param String $whereKeyword OPTIONAL 查询条件
     * @param Array $filter OPTIONAL 筛选条件
     *          <pre>
     * 				$filter[n]['field'] string 字段名
     * 				$filter[n]['value'] string 筛选值
     * 				$filter[n]['method'] string 判断方法 ('='、'>'、'<'、'>='、'<='、'in'、'like'、'not like')
     *          </pre>
     * @return Array 查询结果
     *          <pre>
     * 				$return['rowset']	Zend_Db_Table_Rowset_Abstract 结果集
     * 				$return['countAll'] int 总计记录数
     * 				$return['countNow'] int 当前页记录数
     * 				$return['pageLine'] int 分页行数
     * 				$return['pageNum'] int 当前页数
     * 				$return['pageCount'] int 总页数
     *          </pre>
     */
    static public function fetchList($pageLine = 0, $pageNum = 1, $order = null, $whereKeyword = null, $filter = null) {

        $filter[] = array(
            'field' => 'isDel',
            'method' => '=',
            'value' => '0',
        );
        return parent::fetchList($pageLine, $pageNum, $order, $whereKeyword, $filter);
    }

    /**
     * 添加信息
     *
     * @param Array $info 信息（数组）
     * @return int 新ID
     */
    static public function insert($info) {
        //提交字段判断
        if ( isset($info['username']) == FALSE || trim($info['username']) == '') {
            $errorInfo = 'Username is empty!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NULL'],
                \Application\Model\Exception::LEVEL['NOTICE'],
                __FILE__,
                __LINE__);
            return false;
        }
        if ( isset($info['realname']) == FALSE || trim($info['realname']) == '') {
            $errorInfo = 'Realname is empty!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NULL'],
                \Application\Model\Exception::LEVEL['NOTICE'],
                __FILE__,
                __LINE__);
            return false;
        }
        $info['createTime'] = date('Y-m-d H:i:s');
        return parent::insert($info);
    }

    /**
     * 帐号验证
     *
     * @param String $username 用户名
     * @param String $password 密码
     * @return \Application\Model\Admin 管理员Model （错误返回False）
     */
    static public function checkAccount($username, $password) {
        //提交字段判断
        if ( isset($username) == FALSE || trim($username) == '') {
            $errorInfo = 'Username is empty!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NULL'],
                \Application\Model\Exception::LEVEL['NOTICE'],
                __FILE__,
                __LINE__);
            return false;
        }
        if ( isset($password) == FALSE || trim($password) == '') {
            $errorInfo = 'Password is empty!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NULL'],
                \Application\Model\Exception::LEVEL['NOTICE'],
                __FILE__,
                __LINE__);
            return false;
        }

        $dbTableClass =  self::_NAMESPACE . 'DbTable\\' . self::_DEFAULT_CLASSNAME;
        $mapperClass =  self::_NAMESPACE . 'Mappers\\' . self::_DEFAULT_CLASSNAME;


        //生成加密后的密码
        $password = self::encryptionPassword($password);

        $dbTable = new $dbTableClass;

        $db = $dbTable->getAdapter();

        $where = $db->quoteInto($mapperClass::COLS_MAP['username'] . ' = ?', $username)
            . $db->quoteInto(' AND ' . $mapperClass::COLS_MAP['password'] . ' = ?', $password)
            . ' AND ' . $mapperClass::COLS_MAP['status'] . ' = 1'
            . ' AND ' . $mapperClass::COLS_MAP['isDel'] . ' = 0';

        $row = $dbTable->fetchRow($where);

        if ($row === false) {
            $messages = $dbTable->getMessages();
            $errorInfo = array();
            foreach ($messages as $message) {
                $errorInfo[] = array(
                    'type' => $message->getType(),
                    'code' => $message->getCode(),
                    'field' => $message->getField(),
                    'info' => $message->getMessage(),
                );
            }
            throw new \Application\Model\Exception(json_encode($errorInfo),
                \Application\Model\Exception::TYPE['DB_SQL_SELECT_ERR'],
                \Application\Model\Exception::LEVEL['ERROR'],
                __FILE__,
                __LINE__);
            return false;
        }

        if ($row != NULL) {
            $oModelAdmin = self::fetchById($row['admin_id'] );
        } else {
            return false;
        }

        return $oModelAdmin;
    }

    /**
     * 登录状态验证
     *
     * @return \Application\Model\Admin 管理员Model （未登录返回NULL）
     */
    static public function checkLogin() {

        $oAdmin = \Phalcon\DI::getDefault()->get('session')->get('oAdmin');

        if (isset($oAdmin)) {
            //已经登录
            return $oAdmin;
        } else {
            //未登录
            return NUll;
        }
    }


    /**
     * 注销
     *
     * @return void
     */
    static public function logout() {
        $oAdmin = \Phalcon\DI::getDefault()->get('session')->remove('oAdmin');
    }
}