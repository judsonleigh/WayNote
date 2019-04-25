<?php
/**
 * AdminGroup
 *
 * 管理员组 DbTable类
 *
 * @author Judson Leigh
 * @version $Id:$
 */
namespace Application\Model\DbTable;

class AdminGroup extends \iwaycms\Base\DbTable
{
    /**
     * 默认表名
     * 
     * @property string
     */
    protected $_name = 'wt_admin_group';
    
    /**
     * 主键
     * 
     * @property string
     */
    protected $_primary = 'admin_group_id';   
    
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
