<?php
/**
 * RelationAdminGroup
 *
 * 管理员组关系 DbTable类
 *
 * @author Judson Leigh
 * @version $Id:$
 */
namespace Application\Model\DbTable;

class RelationAdminGroup extends \iwaycms\Base\DbTable
{
    /**
     * 默认表名
     * 
     * @property string
     */
    protected $_name = 'wt_relation_admin_group';
    
    /**
     * 主键
     * 
     * @property string
     */
    protected $_primary = 'id';   
    
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
