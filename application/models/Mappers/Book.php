<?php
/**
 * Book
 *
 * 书籍 映射类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model\Mappers;

class Book extends \iwaycms\Base\Mappers
{
    /**
     * 默认模型类名
     *
     * @const string
     */
    const _DEFAULT_MODEL_CLASS = 'Book';

    /**
     * 默认DbTable类名
     *
     * @const string
     */
    const _DEFAULT_DBTABLE_CLASS = 'Book';

	/**
	 * 字段映射
	 * 
	 * @const Array
	 */
    const COLS_MAP = array(
			'bookId' => 'book_id',
            'bookKey' => 'book_key',
            'bookName' => 'book_name',
            'bookSubname' => 'book_subname',
            'author' => 'author',
            'pic' => 'pic',
			'readUrl' => 'read_url',
            'isDel' => 'is_del',
			'createTime' => 'create_time',
	);

	/**
	 * 查询字段
	 * 
	 * @property Array
	*/
    const WHERE_BY = array(
            'book_key',
			'book_name',
			'book_subname',
	);
	
    /**
	 * 默认排序字段
	 * 
	 * @property Array
	 */
    const ORDER_BY = array(
			'book_id desc',
	);


	

	


 	
}