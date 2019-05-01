<?php
/**
 * Comment
 *
 * 评论 映射类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model\Mappers;

class Comment extends \iwaycms\Base\Mappers
{
    /**
     * 默认模型类名
     *
     * @const string
     */
    const _DEFAULT_MODEL_CLASS = 'Comment';

    /**
     * 默认DbTable类名
     *
     * @const string
     */
    const _DEFAULT_DBTABLE_CLASS = 'Comment';

	/**
	 * 字段映射
	 * 
	 * @const Array
	 */
    const COLS_MAP = array(
			'commentId' => 'comment_id',
            'bookId' => 'book_id',
			'realname' => 'realname',
			'email' => 'email',
			'content' => 'content',
			'status' => 'status',
            'ipAddr' => 'ip_addr',
            'isDel' => 'is_del',
			'createTime' => 'create_time',
	);

	/**
	 * 查询字段
	 * 
	 * @property Array
	*/
    const WHERE_BY = array(
            'realname',
			'email',
			'describe',
	);
	
    /**
	 * 默认排序字段
	 * 
	 * @property Array
	 */
    const ORDER_BY = array(
			'comment_id desc',
	);


	

	


 	
}