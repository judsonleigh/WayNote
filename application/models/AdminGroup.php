<?php
/**
 * AdminGroup
 * 
 * 管理员组 Model类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model;

class AdminGroup extends \iwaycms\Base\Model {

    /**
     * 默认类名
     *
     * @const string
     */
    const _DEFAULT_CLASSNAME = 'AdminGroup';

	/**
	 * 数据库 Admin表列名
	 * 
	 * @property Array
	 */
	protected $_properties = array(
			'id',
			'name',
			'status',
            'isDel',
    );

	/**
	 * 修改信息
	 * 
	 * @param Array $info 信息（数组）
	 * @param int $id OPTIONAL 信息主键编号 
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
	 * @param int $id OPTIONAL 信息主键编号
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
	 * 取所属管理员列表
	 *
	 * @return Array 管理员Model列表
	 */
	public function getAdminList() {
		//创建管理员组关系DbTable类
		$dbTableClass = self::_NAMESPACE . 'DbTable\RelationAdminGroup';
		$dbTable = new $dbTableClass;
		$db = $dbTable->getAdapter();
		
		//设置查询条件
		$where = $db->quoteInto('admin_group_id = ?', $this->id);	//必须为当前组
		$where .= $db->quoteInto(' AND status = ?', 1);		//状态必须为有效
		$order = 'admin_id';
		$rowset = $dbTable->fetchAll($where, $order);

		if (empty($rowset) == false) {
            $adminList = $rowset;
        } else {
            $adminList = array();
        }

		$arrayReturn = array();
		
		//循环提取管理员Model合并到数组
        $strMapperClass = self::_NAMESPACE . 'Admin';
        foreach ($adminList as $adminRow) {
			$arrayReturn[] = $strMapperClass::fetchById($adminRow['admin_id']);
		}
		
		return $arrayReturn;
	}
	
	/**
	 * 加入管理员
	 * @param int $adminId 管理员编号
	 * @return bool 加入是否成功
	 */
	public function addAdmin($adminId) {
		$strMapperClass = self::_NAMESPACE . 'Admin';
		//取管理员Model
		$modelAdmin = $strMapperClass::fetchById($adminId);
		if ($modelAdmin == false) {
			$this->_errorInfo = '管理员编号不存在！';
            $errorInfo = 'Administrator id does not exist!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NO_RESOURCE'],
                \Application\Model\Exception::LEVEL['WARNING'],
                __FILE__,
                __LINE__);
			return false;
		}
		//取当前管理员组管理员Model列表
		$adminModelList = $this->getAdminList();
		foreach ($adminModelList as $adminModel) {
			if ($adminModel->adminId == $adminId) {
				//管理员已被加入
				$this->_errorInfo = '管理员已被加入当前管理员组！';
				return false;
			}
		}
		
		//创建管理员组关系DbTable类
		$dbTableClass = self::_NAMESPACE . 'DbTable\RelationAdminGroup';
		$dbTable = new $dbTableClass;
		$db = $dbTable->getAdapter();
		
		//设置查询条件
		$where = $db->quoteInto('admin_group_id = ?', $this->id);
		$where .= $db->quoteInto(' AND admin_id = ?', $adminId);
		//查询管理员组关系记录是否存在
		$rowset = $dbTable->fetchAll($where);
		$adminList = $rowset;
		if (empty($adminList)) {
			//记录不存在，进行添加
			$data = array(
					'admin_id' => $adminId,
					'admin_group_id'  => $this->id,
					'status' => '1',
			);
			//插入数据库
			if ($dbTable->insert($data) == 0) {
                $this->_errorInfo = '数据库插入失败！';
                $errorInfo = 'Database insert failed!';
                throw new \Application\Model\Exception($errorInfo,
                    \Application\Model\Exception::TYPE['DB_SQL_INSERT_ERR'],
                    \Application\Model\Exception::LEVEL['ERROR'],
                    __FILE__,
                    __LINE__);
                return false;
			}
		} else {
			//记录存在，进行修改		

			$data = array(
					'status' => '1',
			);
			$where = $db->quoteInto('admin_group_id = ?', $this->id);
			$where .= $db->quoteInto(' AND admin_id = ?', $adminId);
			if($dbTable->update($data, null, $where) == 0) {
				$this->_errorInfo = '数据库修改失败！';
                $errorInfo = 'Database update failed!';
                throw new \Application\Model\Exception($errorInfo,
                    \Application\Model\Exception::TYPE['DB_SQL_UPDATE_ERR'],
                    \Application\Model\Exception::LEVEL['ERROR'],
                    __FILE__,
                    __LINE__);
				return false;
			}
		}
		return true;
	}
	
	/**
	 * 移除管理员
	 * @param int $adminId 管理员编号
	 * @return bool 移除是否成功
	 */
	public function removeAdmin($adminId) {
		$strModelClass = self::_NAMESPACE . 'Admin';
		//取管理员Model
		$modelAdmin = $strModelClass::fetchById($adminId);
		if ($modelAdmin == false) {
			$this->_errorInfo = '管理员编号不存在！';
			return false;
		}
		//取当前管理员组管理员Model列表
		$adminModelList = $this->getAdminList();
		$isJoin = false;		//管理员是否已加入
		foreach ($adminModelList as $adminModel) {
			if ($adminModel->adminId == $adminId) {
				//管理员已被加入
				$isJoin = true;
			}
		}
		//判断当前管理员是否已加入管理员组
		if ($isJoin == false) {
			$this->_errorInfo = '管理员未加入当前管理员组！';
            $errorInfo = 'The administrator has not joined the current administrator group!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NO_RESOURCE'],
                \Application\Model\Exception::LEVEL['WARNING'],
                __FILE__,
                __LINE__);
            return false;
		}
		
		//创建管理员组关系DbTable类
		$dbTableClass = self::_NAMESPACE . 'DbTable\RelationAdminGroup';
		$dbTable = new $dbTableClass;
		$db = $dbTable->getAdapter();
		
		$data = array(
				'status' => '-1',
		);
		$where = $db->quoteInto(' admin_group_id = ?', $this->id);
		$where .= $db->quoteInto(' AND admin_id = ?', $adminId);
        $rs = $dbTable->update($data, null, $where);

		//移除管理员
		if($rs == 0) {
			$this->_errorInfo = '数据库修改失败！';
            $errorInfo = 'Database update failed!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['DB_SQL_UPDATE_ERR'],
                \Application\Model\Exception::LEVEL['ERROR'],
                __FILE__,
                __LINE__);
			return false;
		}
		
		return true;
	}
	
	/**
	 * 取有权限的功能列表
	 *
	 * @return Array 功能Model列表
	 */
	public function getFunctionList() {
		//创建管理员组关系DbTable类
		$dbTableClass = self::_NAMESPACE . 'DbTable\PurviewAdminGroupFunction';
		$dbTable = new $dbTableClass;
		$db = $dbTable->getAdapter();
		
		//设置查询条件
		$where = $db->quoteInto('admin_group_id = ?', $this->id);	//必须为当前组
		$where .= $db->quoteInto(' AND status = ?', 1);		//状态必须为有效
		$order = 'function_id';
		$rowset = $dbTable->fetchAll($where, $order);
		$functionList = $rowset;

		if (empty($functionList) == true) {
            $functionList = array();
        }
		
		$strModelClass = self::_NAMESPACE . 'Func';

		$arrayReturn = array();
		
		//循环提取管理员Model合并到数组
		foreach ($functionList as $functionRow) {
			$arrayReturn[] = $strModelClass::fetchById($functionRow['function_id']);
		}
		
		return $arrayReturn;
	}
	
	
	/**
	 * 加入功能权限
	 * @param int $functionId 功能编号
	 * @return bool 加入是否成功
	 */
	public function addFunction($functionId) {
		$strModelClass = self::_NAMESPACE . 'Func';

		//取管理员Model
		$modelFunction = $strModelClass::fetchById($functionId);
		if ($modelFunction == false) {
			$this->_errorInfo = '功能编号不存在！';
            $errorInfo = 'Function id does not exist!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NO_RESOURCE'],
                \Application\Model\Exception::LEVEL['WARNING'],
                __FILE__,
                __LINE__);
			return false;
		}

		//取当前管理员组功能Model列表
		$functionModelList = $this->getFunctionList();
		foreach ($functionModelList as $functionModel) {
			if ($functionModel->id == $functionId) {
				//功能已有权限
				$this->_errorInfo = '此功能已有权限！';
                $errorInfo = 'This function has permission!';
                throw new \Application\Model\Exception($errorInfo,
                    \Application\Model\Exception::TYPE['PARAM_RESOURCE_EXISTS'],
                    \Application\Model\Exception::LEVEL['WARNING'],
                    __FILE__,
                    __LINE__);
				return false;
			}
		}
		
		//创建管理员组功能权限DbTable类
		$dbTableClass = self::_NAMESPACE . 'DbTable\PurviewAdminGroupFunction';

		$dbTable = new $dbTableClass;
		$db = $dbTable->getAdapter();
		
		//设置查询条件
		$where = $db->quoteInto('admin_group_id = ?', $this->id);
		$where .= $db->quoteInto(' AND function_id = ?', $functionId);
		//查询管理员组功能权限记录是否存在
		$rowset = $dbTable->fetchAll($where);
		$functionList = $rowset;
		if (empty($functionList)) {
			//记录不存在，进行添加
			$data = array(
					'function_id' => $functionId,
					'admin_group_id'  => $this->id,
					'status' => '1',
			);
			//插入数据库
			if ($dbTable->insert($data) == 0) {
				$this->_errorInfo = '数据库插入失败！';
                $errorInfo = 'Database insert failed!';
                throw new \Application\Model\Exception($errorInfo,
                    \Application\Model\Exception::TYPE['DB_SQL_INSERT_ERR'],
                    \Application\Model\Exception::LEVEL['ERROR'],
                    __FILE__,
                    __LINE__);
				return false;
			}
		} else {
			//记录存在，进行修改
		
			$data = array(
					'status' => '1',
			);
			$where = $db->quoteInto('admin_group_id = ?', $this->id);
			$where .= $db->quoteInto(' AND function_id = ?', $functionId);
			if($dbTable->update($data, null,$where) == 0) {
				$this->_errorInfo = '数据库修改失败！';
                $errorInfo = 'Database update failed!';
                throw new \Application\Model\Exception($errorInfo,
                    \Application\Model\Exception::TYPE['DB_SQL_UPDATE_ERR'],
                    \Application\Model\Exception::LEVEL['ERROR'],
                    __FILE__,
                    __LINE__);
                return false;
			}
		}
		return true;
	}
	
	/**
	 * 删除功能权限
	 * @param int $functionId 功能编号
	 * @return bool 删除是否成功
	 */
	public function removeFunction($functionId) {
		$strModelClass = self::_NAMESPACE . 'Func';
		//取功能Model
		$modelFunction = $strModelClass::fetchById($functionId);
		if ($modelFunction == false) {
			$this->_errorInfo = '功能编号不存在！';
            $errorInfo = 'Function id does not exist!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NO_RESOURCE'],
                \Application\Model\Exception::LEVEL['WARNING'],
                __FILE__,
                __LINE__);
            return false;
		}
		//取当前管理员组功能Model列表
		$functionModelList = $this->getFunctionList();
		$isJoin = false;		//功能是否已加入
		foreach ($functionModelList as $functionModel) {
			if ($functionModel->id == $functionId) {
				//功能已被加入
				$isJoin = true;
			}
		}
		//判断当前功能是否已加入管理员组
		if ($isJoin == false) {
			$this->_errorInfo = '当前管理员组没有此功能权限！';
            $errorInfo = 'This function is not available for the current administrator group!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NO_RESOURCE'],
                \Application\Model\Exception::LEVEL['WARNING'],
                __FILE__,
                __LINE__);
			return false;
		}
	
		//创建管理员组功能权限DbTable类
		$dbTableClass = self::_NAMESPACE . 'DbTable\PurviewAdminGroupFunction';
		$dbTable = new $dbTableClass;
		$db = $dbTable->getAdapter();
	
		$data = array(
				'status' => '-1',
		);
		$where = $db->quoteInto('admin_group_id = ?', $this->id);
		$where .= $db->quoteInto(' AND function_id = ?', $functionId);
		//移除权限
		if($dbTable->update($data, null,$where) == 0) {
			$this->_errorInfo = '数据库修改失败！';
            $errorInfo = 'Database update failed!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['DB_SQL_UPDATE_ERR'],
                \Application\Model\Exception::LEVEL['ERROR'],
                __FILE__,
                __LINE__);
            return false;
		}
	
		return true;
	}

    /**
     * 添加信息
     *
     * @param Array $info 信息（数组）
     * @return int 新ID
     */
    static public function insert($info) {
        //提交字段判断
        if ( isset($info['name']) == FALSE || trim($info['name']) == '') {
            $errorInfo = 'Administrator group name is empty!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NULL'],
                \Application\Model\Exception::LEVEL['NOTICE'],
                __FILE__,
                __LINE__);
            return false;
        }
        return parent::insert($info);
    }

    /**
     * 根据编号取信息
     *
     * @param int $id 编号
     * @return \Application\Model\AdminGroup Model
     */
    static public function fetchById($id) {
        return parent::fetchById($id);
    }
}