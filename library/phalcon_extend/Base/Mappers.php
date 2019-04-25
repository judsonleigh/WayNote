<?php
/**
 * App_Model_Mappers_Base
 *
 * 映射基础抽象类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace iwaycms\Base;

abstract class Mappers
{
    /**
     * 命名空间
     *
     * @const string
     */
    const _NAMESPACE = '\Application\Model\\';

    /**
     * 默认模型类名
     *
     * @const string
     */
    const _DEFAULT_MODEL_CLASS = '';

    /**
     * 默认DbTable类名
     *
     * @const string
     */
    const _DEFAULT_DBTABLE_CLASS = '';

	/**
	 * 字段映射
	 * 
	 * @const Array
	 */
	const COLS_MAP = array();

	/**
	 * 查询字段
	 * 
	 * @const Array
	 */
    const WHERE_BY = array();
	
	/**
	 * 默认排序字段
	 * 
	 * @const Array
	 */
	const ORDER_BY = array();
	
	/**
	 * 错误信息
	 * 
	 * @property String
	 */
	protected $_errorInfo = '';
	
	/**
	 * 错误代码
	 *
	 * @property String
	 */
	protected $_errorCode = '';
	
	/**
	 * 构造函数
	 * 
	 * @return \Application\Model\Mappers\Base
	 */
	public function __construct() {
		
	}
	
	/**
	 * 析构函数
	 */
	public function __destruct() {
	
	}

    /**
     * 取字段映射
     * 
     * @return Array 字段映射
     */
    public function getColsMap() {
    	return self::COLS_MAP;
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
     * 取最后的错误代码
     *
     * @return int 错误代码
     */
    public function getErrorCode() {
        return $this->_errorCode;
    }
}