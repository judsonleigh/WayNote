<?php
/**
 * Config
 *
 * 系统配置 DbTable类
 *
 * @author Judson Leigh
 * @version $Id:$
 */
namespace Application\Model\DbTable;

class Config extends \iwaycms\Base\DbTable
{
    /**
     * 默认表名
     * 
     * @property string
     */
    protected $_name = 'wt_config';
    
    /**
     * 主键
     * 
     * @property string
     */
    protected $_primary = 'config_id';   
    
    /**
     * 获取数据库表名
     * 
     * @return string
     */    
    public function getDefaultTableName() {
    	return $this->_name;
    }
    
    /**
     * 获取主键
     * 
     * @return string
     */
    public function getPrimary() {
    	return $this->_primary;
    }
    
}
