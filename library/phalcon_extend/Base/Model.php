<?php
/**
 * App_Model_Base
 * 
 * Model基础抽象类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace iwaycms\Base;

abstract class Model {

	/**
	 * 命名空间
	 * 
	 * @const string
	 */
    const _NAMESPACE = '\Application\Model\\';

	/**
	 * 默认类名
	 * 
	 * @const string
	 */
    const _DEFAULT_CLASSNAME = '';

	/**
	 * 类属性值
	 *
	 * @property Array
	 */
	protected $_propertyValues = array();
	
	/**
	 * 错误信息
	 * 
	 * @property String
	 */
	protected $_errorInfo = '';

	/**
	 * 构造函数
	 * 
	 * @return \Application\Model\Base
	 */
	public function __construct() {
	
	}
	
	/**
	 * 析构函数
	 */
	public function __destruct() {
	
	}
	
	/**
	 * 设置未定义属性值
	 * 
	 * @param String $property 属性名
	 * @param mixed $value 数值
	 */
	public function __set($property, $value) {
		$this->_propertyValues[$property] = $value;
	}
	
	/**
	 * 取未定义属性值
	 * 
	 * @param String $property 属性名
	 * @return mixed 数值
	 */
	public function __get($property) {
		return $this->_propertyValues[$property];
	}

    /**
     * 设置未定义属性值
     *
     * @param String $property 属性名
     */
    public function __isset($property) {
        return isset($this->_propertyValues[$property]);
    }

	/**
	 * 修改信息
	 * 
	 * @param Array $info 信息
	 * @param int $id 信息主键编号
	 * @return bool 修改是否成功
	 */
	public function update($info, $id) {
        $dbTableClass = self::_NAMESPACE . 'DbTable\\' . static::_DEFAULT_CLASSNAME;
        $mapperClass = self::_NAMESPACE . 'Mappers\\' . static::_DEFAULT_CLASSNAME;

		$dbTableClass::setup(array(
		    'notNullValidations' => false,
		));
		
		$dbTable = new $dbTableClass;
		
		$dbPrimary = $dbTable->getPrimary();

		$data = array();
		foreach ($mapperClass::COLS_MAP as $property => $field) {
			if (isset($info[$property]) && $dbPrimary != $field) {
				$data[$field] = str_replace('\'', '\\\'', $info[$property]);
                $this->_propertyValues[$property] = $info[$property];
			}
		}

		$dbTable = $dbTable->findFirst("$dbPrimary = '$id'");
        if ($dbTable === false) {
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

		$result = $dbTable->update($data);
		
		if ($result === false) {
			$this->_errorInfo = '修改错误！';
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
                \Application\Model\Exception::TYPE['DB_SQL_UPDATE_ERR'],
                \Application\Model\Exception::LEVEL['ERROR'],
                __FILE__,
                __LINE__);

			return false;
		}

		return true;
	}
	
	/**
	 * 删除信息
	 * 
	 * @param int $id 信息主键编号
	 * @return bool 删除是否成功
	 */
	public function delete($id) {
        $dbTableClass = self::_NAMESPACE . 'DbTable\\' . static::_DEFAULT_CLASSNAME;
		$dbTable = new $dbTableClass;
		$dbPrimary = $dbTable->getPrimary();
		
		$dbTable = $dbTable->findFirst("$dbPrimary = '$id'");
        if ($dbTable === false) {
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
		$result = $dbTable->delete();
        if ($result === false) {
            $this->_errorInfo = '删除错误！';
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
                \Application\Model\Exception::TYPE['DB_SQL_DELETE_ERR'],
                \Application\Model\Exception::LEVEL['ERROR'],
                __FILE__,
                __LINE__);

            return false;
        }

        return true;
    }
	
	/**
	 * 取最后的错误信息
	 * 
	 * @return String 错误信息
	 */
	public function getErrorInfo() {
		return $this->_errorInfo;
	}
	
	/**
	 * 取对象属性数组
	 *
	 * @return array 对象属性数组
	 */
	public function getProperties() {
	    return $this->_propertyValues;
	}

    /**
     * 根据编号取信息
     *
     * @param int $id 编号
     * @return \Application\Model\Base Model
     */
    static public function fetchById($id) {
        $dbTableClass = self::_NAMESPACE . 'DbTable\\' . static::_DEFAULT_CLASSNAME;
        $modelClass = self::_NAMESPACE . static::_DEFAULT_CLASSNAME;
        $mapperClass = self::_NAMESPACE . 'Mappers\\' . static::_DEFAULT_CLASSNAME;

        //判断编号类型
        if (is_numeric($id) == false) {
            $errorInfo = 'Id(' . $id . ') error！';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_TYPE_ERR'],
                \Application\Model\Exception::LEVEL['ERROR'],
                __FILE__,
                __LINE__);
            return false;
        }

        //创建DbTable类
        $dbTable = new $dbTableClass;

        //从SQL数据库查询
        $rowset = $dbTable->find(array($id));

        $result = $rowset->toArray();

        if (count( $result ) <= 0) {
            $errorInfo = 'Record does not exist！(id=' . $id . ')';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NO_RESOURCE'],
                \Application\Model\Exception::LEVEL['WARNING'],
                __FILE__,
                __LINE__);
            return false;
        }
        $row = $result[0];

        //创建Model类
        $nowModel = new $modelClass;

        foreach ($mapperClass::COLS_MAP as $property => $field) {
            $nowModel->$property = $row[$field];
        }

        return $nowModel;
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
     * 				$return['rowset']	Array 结果集
     * 				$return['countAll'] int 总计记录数
     * 				$return['countNow'] int 当前页记录数
     * 				$return['pageLine'] int 分页行数
     * 				$return['pageNum'] int 当前页数
     * 				$return['pageCount'] int 总页数
     *          </pre>
     */
    static public function fetchList($pageLine = 0, $pageNum = 1, $order = null, $whereKeyword = null, $filter = null) {
        $dbTableClass = self::_NAMESPACE . 'DbTable\\' . static::_DEFAULT_CLASSNAME;
        $mapperClass = self::_NAMESPACE . 'Mappers\\' . static::_DEFAULT_CLASSNAME;

        $returnArray = Array();
        $returnArray['pageLine'] = $pageLine;

        //创建DbTable类
        $dbTable = new $dbTableClass;
        $dbConn = $dbTable->getReadConnection();

        //获取数据表名
        $tableName = $dbTable->getDefaultTableName();

        $whereKeyword = trim($whereKeyword);

        //查询总数输入变量
        $arrayCountVals = array();

        //筛选条件传入的变量
        $arrayFilterVals = array();

        //设置筛选条件
        $whereFilter = '';
        if (empty($filter) == false) {
            $arrayColsMap = $mapperClass::COLS_MAP;
            foreach ($filter as $filterArray) {

                if (trim( $filterArray['field'] )  !== '' && trim( $filterArray['value'] )  !== '' && trim( $filterArray['method'] )  !== '' && isset($arrayColsMap[$filterArray['field']])) {
                    if ($whereFilter != '') {
                        $whereFilter .= ' and ';
                    }
                    $filterArray['value'] = str_replace('\'', '\\\'', $filterArray['value']);
                    if ($filterArray['method'] != 'in') {
                        $whereFilter .= $mapperClass::COLS_MAP[$filterArray['field']] . ' ' . $filterArray['method']. ' ?';
                        $arrayFilterVals[] =  $filterArray['value'];


                    } else {
                        $whereFilter .= $mapperClass::COLS_MAP[$filterArray['field']] . ' in (' . $filterArray['value'] . ')';
                    }

                }
            }
        }
        //查询条件传入的变量
        $arraySearchVals = array();

        //设置查询条件
        $whereSearch = '';
        if ($whereKeyword != null && $whereKeyword != '') {
            foreach ($mapperClass::WHERE_BY as $field) {
                if ($whereSearch != '') {
                    $whereSearch .= ' or ';
                }
                $whereSearch .= "$field like ?";
                $whereKeyword = str_replace('\'', '\\\'', $whereKeyword);
                $arraySearchVals[] = "%$whereKeyword%";
            }
        }

        //取记录总数
        $strCountSQL = 'SELECT count(*) as count FROM ' . $tableName;

        if ($whereFilter != '' || $whereSearch != '') {

            $strCountSQL .= ' WHERE ';

            if ($whereFilter != '') {
                $strCountSQL .= $whereFilter;
                $arrayCountVals = $arrayFilterVals;
            }

            if ($whereSearch != '') {
                if ($whereFilter != '') {
                    $strCountSQL .= ' and ';
                }
                $strCountSQL .= "($whereSearch)";
                $arrayCountVals = array_merge($arrayCountVals, $arraySearchVals);
            }


        }

        $result = $dbConn->query($strCountSQL, $arrayCountVals);
        $row = $result->fetch();

        $returnArray['countAll'] = $row['count'];
        if ($returnArray['countAll'] > 0) {
            //有列表信息

            //查询SQL
            $strSQL = 'SELECT ';
            //查询输入变量
            $arrayVals = array();

            //字段列表
            $strFields = '';
            foreach ($mapperClass::COLS_MAP as $key=>$field) {
                if ($strFields != '') {
                    $strFields .= ',';
                }
                $strFields .= "$field AS $key";
            }

            $strSQL .= $strFields . ' FROM ' . $tableName;

            if ($whereFilter != '' || $whereSearch != '') {

                $strSQL .= ' WHERE ';

                if ($whereFilter != '') {
                    $strSQL .= $whereFilter;
                    $arrayVals = $arrayFilterVals;
                }

                if ($whereSearch != '') {
                    if ($whereFilter != '') {
                        $strSQL .= ' and ';
                    }
                    $strSQL .= "($whereSearch)";
                    $arrayVals = array_merge($arrayVals, $arraySearchVals);
                }
            }

            //判断是否需要排序
            if ($order != null && count($order) > 0) {
                $strOrderBy = '';
                $arrayColsMap = $mapperClass::COLS_MAP;
                foreach ($order as $orderArray) {
                    if ($strOrderBy != '') {
                        $strOrderBy .= ',';
                    }
                    if (trim( $orderArray['field'] )  != '') {
                        $field = $orderArray['field'];
                        if (isset($arrayColsMap[$field]) == true) {
                            $field = $arrayColsMap[$field];
                        }
                        $strOrderBy .= $field;
                        //判断是否倒序
                        if (isset($orderArray['desc']) && $orderArray['desc'] == true) {
                            $strOrderBy .= ' desc';
                        }
                    }
                }
                $strSQL .= ' ORDER BY ' . $strOrderBy;
            } else {
                if (empty($mapperClass::ORDER_BY) == false) {
                    $strSQL .= ' ORDER BY ' . implode(',', $mapperClass::ORDER_BY);
                }
            }

            //判断是否需要分页
            if ($pageLine >= 1 && $pageLine != null) {
                //需要分页
                if ($pageNum < 1 || $pageNum == null) {
                    $pageNum = 1;
                }
                //取总页数
                $returnArray['pageCount'] = ceil($returnArray['countAll'] / $pageLine);
                if ($returnArray['pageCount'] < $pageNum) {
                    $returnArray['pageNum'] = $pageNum = $returnArray['pageCount'];
                } else {
                    $returnArray['pageNum'] = $pageNum;
                }
                $strSQL .= ' LIMIT ' . (($pageNum - 1) * $pageLine) . ",$pageLine";
            } else {
                //无需分页
                $returnArray['pageCount'] = 1;
                $returnArray['pageNum'] = 1;
            }

            $result = $dbConn->query($strSQL, $arrayVals);


            $rows = $result->fetchAll();

            $returnArray['rowset'] = $rows;
            $returnArray['countNow'] = count($returnArray['rowset']);
        } else {
            //无列表信息
            $returnArray['rowset'] = null;
            $returnArray['countNow'] = 0;
            $returnArray['pageNum'] = 1;
            $returnArray['pageCount'] = 1;
        }
        return $returnArray;
    }


    /**
     * 添加信息
     *
     * @param Array $info 信息（数组）
     * @return int 新ID
     */
    static public function insert($info) {
        $dbTableClass = self::_NAMESPACE . 'DbTable\\' . static::_DEFAULT_CLASSNAME;
        $mapperClass = self::_NAMESPACE . 'Mappers\\' . static::_DEFAULT_CLASSNAME;
        $dbTable = new $dbTableClass;

        $data = array();
        foreach ($mapperClass::COLS_MAP as $key => $fieldName) {
            if (isset($info[$key])) {
                $data[$fieldName] = str_replace('\'', '\\\'', $info[$key]);
            }
        }
        $id = $dbTable->insert($data);
        return $id;
    }


    /**
     * 判断字段值是否重复
     *
     * @param String $field 字段名
     * @param String $value 数值
     * @return bool 字段值是否存在
     */
    static public function checkRepeat($field, $value) {
        $dbTableClass = self::_NAMESPACE . 'DbTable\\' . static::_DEFAULT_CLASSNAME;
        $mapperClass = self::_NAMESPACE . 'Mappers\\' . static::_DEFAULT_CLASSNAME;

        $arrayColsMap = $mapperClass::COLS_MAP;

        if (isset($arrayColsMap[$field])) {
            $dbTable = new $dbTableClass;
            $value = str_replace('\'', '\\\'', $value);
            $result = $dbTable->find($mapperClass::COLS_MAP[$field] . " = '$value'");
            if ($result === false) {
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

            if (empty($result->toArray()) == false) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
}