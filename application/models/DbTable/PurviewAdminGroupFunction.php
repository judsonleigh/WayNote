<?php
/**
 * PurviewAdminGroupFunction
 *
 * 管理员组功能权限 DbTable类
 *
 * @author Judson Leigh
 * @version $Id:$
 */
namespace Application\Model\DbTable;

class PurviewAdminGroupFunction extends \iwaycms\Base\DbTable
{
    /**
     * 默认表名
     * 
     * @property string
     */
    protected $_name = 'wt_purview_admin_group_function';
    
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
