<?php
/**
 * App_Model_DbTable_Base
 * 
 * DbTable基础类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace iwaycms\Base;

use Application\Model\Exception;
use Phalcon\Db\Adapter\Pdo\Mysql as Connection;

abstract class DbTable extends \Phalcon\Mvc\Model
{
    /**
     * 默认表名
     *
     * @property string
     */
    protected $_name = '';

    /**
     * 主键
     *
     * @property string
     */
    protected $_primary = '';

    /**
     * 获取数据库表名
     *
     * @return string 默认表名
     */
    public function getDefaultTableName()
    {
        return $this->_name;
    }

    /**
     * 获取数据库表名(重构getDefaultTableName)
     *
     * @return string 默认表名
     */
    public function getSource()
    {
        return $this->getDefaultTableName();
    }

    /**
     * 获取主键
     *
     * @return string 主键名
     */
    public function getPrimary()
    {
        return $this->_primary;
    }



    /**
     * 添加信息
     *
     * @param Array $data
     *            信息（数组）
     * @return int 新ID
     */
    public function insert($data)
    {
        $this->setup(array(
            'notNullValidations' => true
        ));

        if (intval(\Phalcon\Version::get()) < 3) {
            $row = $this->toArray();
            foreach ($row as $field => $value) {
                $this->$field = new \Phalcon\Db\RawValue('default');
            }
        }

        $result = $this->create($data);

        if ($result === false) {
            $messages = $this->getMessages();
            $errorInfo = array();
            foreach ($messages as $message) {
                $errorInfo[] = array(
                    'type' => $message->getType(),
                    //'code' => $message->getCode(),
                    'field' => $message->getField(),
                    'info' => $message->getMessage(),
                );
            }
            throw new \Application\Model\Exception(json_encode($errorInfo),
                \Application\Model\Exception::TYPE['DB_SQL_INSERT_ERR'],
                \Application\Model\Exception::LEVEL['ERROR'],
                __FILE__,
                __LINE__);
            return false;

        } else {
            $primary = $this->_primary;
            return $this->$primary;
        }
    }

    /**
     * 修改信息
     *
     * @param Array $data 信息（数组）
     * @param Array $whiteList 字段列表
     * @param string $where Where条件
     * @return int 新ID
     */
    public function update($data = null, $whiteList = null, $where = null)
    {
        if (empty($where) == true) {
            $result = parent::update($data, $whiteList);
        } else {
            $where = trim($where);
            $dbTable = $this->findFirst($where);
            $result = $dbTable->update($data);
        }

        if ($result === false) {
            $messages = $this->getMessages();
            $errorInfo = array();
            foreach ($messages as $message) {
                $errorInfo[] = array(
                    'type' => $message->getType(),
                    //'code' => $message->getCode(),
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
        return $result;
    }
    
    /**
     * 获取数据库适配器
     *
     * @param Array $data
     *            信息（数组）
     * @return int 新ID
     */
    public function getAdapter()
    {
        $db = new \Application\Model\DbAdapter();
        return $db;
    }
    
    
    /**
     * 获取第一行记录
     *
     * @param string $where OPTIONAL Where查询条件
     * @param string $order OPTIONAL Order排序条件
     * @return array|null 结果集（无记录返回null）
     */
    public function fetchRow($where = null, $order = null)
    {

        $dbParameters = array();

        if (empty($where) == false) {
            $dbParameters[0] = $where;
        }

        if (empty($order) == false) {
            $dbParameters['order'] = $order;
        }

        $data = $this->findFirst($dbParameters);

        $returnInfo = null;

        if ($data === false) {
            $messages = $this->getMessages();
            $errorInfo = array();

            foreach ($messages as $message) {
                $errorInfo[] = array(
                    'type' => $message->getType(),
                    //'code' => $message->getCode(),
                    'field' => $message->getField(),
                    'info' => $message->getMessage(),
                );
            }
            throw new \Application\Model\Exception(json_encode($errorInfo),
                \Application\Model\Exception::TYPE['DB_SQL_SELECT_ERR'],
                \Application\Model\Exception::LEVEL['ERROR'],
                __FILE__,
                __LINE__);
            return null;
        } else {
            if (empty($data) == false) {
                $returnInfo = $data->toArray();
            } else {
                $returnInfo = null;
            }
        }

        return $returnInfo;
    }

    /**
     * Query the first record that matches the specified conditions
     *
     * <code>
     * // What's the first robot in robots table?
     * $robot = Robots::findFirst();
     *
     * echo "The robot name is ", $robot->name;
     *
     * // What's the first mechanical robot in robots table?
     * $robot = Robots::findFirst(
     *     "type = 'mechanical'"
     * );
     *
     * echo "The first mechanical robot name is ", $robot->name;
     *
     * // Get first virtual robot ordered by name
     * $robot = Robots::findFirst(
     *     [
     *         "type = 'virtual'",
     *         "order" => "name",
     *     ]
     * );
     *
     * echo "The first virtual robot name is ", $robot->name;
     * </code>
     *
     *
     * @param string|array $parameters
     * @return Model
     */
    static public function findFirst($parameters = null) {
        try {
            $result = parent::findFirst($parameters);
        } catch (\Phalcon\Mvc\Model\Exception $e) {

            $errorInfo[] = array(
                //'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'info' => $e->getMessage(),
            );
            throw new \Application\Model\Exception(json_encode($errorInfo),
                \Application\Model\Exception::TYPE['DB_SQL_SELECT_ERR'],
                \Application\Model\Exception::LEVEL['ERROR'],
                __FILE__,
                __LINE__);
            return false;
        }

        if ($result == false) {
            return null;
        }
        return $result;
    }


    /**
     * 获取所有记录
     *
     * @param string $where OPTIONAL Where查询条件
     * @param string $order OPTIONAL Order排序条件
     * @param int $count OPTIONAL 查询条数
     * @param int $offset OPTIONAL 查询偏移量
     * @return array|null 结果集（无记录返回null）
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
    	$dbConn = $this->getReadConnection();
    	$strSQL = 'SELECT * FROM ' . $this->_name; 
    	
    	if (empty($where) == false) {
    	    $strSQL .= ' WHERE ' . $where;
    	}
    	
    	if (empty($order) == false) {
    	    $strSQL .= ' ORDER BY ' . $order;
    	}
    	
    	$count = intval($count);
    	if (empty($count) == false) {
    	    $strSQL .= ' LIMIT ';

    	    $offset = intval($offset);
    	    if (empty($offset) == false) {
    	        $strSQL .= $offset . ',' . $count;
    	    } else {
    	        $strSQL .= $count;
    	    }
    	}
    	
    	$result = $dbConn->query($strSQL);

    	if ($result === false) {
            $messages = $this->getMessages();
            $errorInfo = array();
            foreach ($messages as $message) {
                $errorInfo[] = array(
                    'type' => $message->getType(),
                    //'code' => $message->getCode(),
                    'field' => $message->getField(),
                    'info' => $message->getMessage(),
                );
            }
            throw new \Application\Model\Exception(json_encode($errorInfo),
                \Application\Model\Exception::TYPE['DB_SQL_SELECT_ERR'],
                \Application\Model\Exception::LEVEL['ERROR'],
                __FILE__,
                __LINE__);
            return null;
        }
    	$rows = $result->fetchAll();
    	if (empty($rows) == true) {
    	    return null;
        }
        return $rows;
    }
}

