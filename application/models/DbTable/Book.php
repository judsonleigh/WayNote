<?php
/**
 * Book
 *
 * 书籍 DbTable类
 *
 * @author Judson Leigh
 * @version $Id:$
 */
namespace Application\Model\DbTable;

class Book extends \iwaycms\Base\DbTable
{
    /**
     * 默认表名
     * 
     * @property string
     */
    protected $_name = 'wt_book';
    
    /**
     * 主键
     * 
     * @property string
     */
    protected $_primary = 'book_id';
    
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
