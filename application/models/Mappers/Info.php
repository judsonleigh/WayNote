<?php
/**
 * Info
 *
 * 知识点 映射类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model\Mappers;

class Info extends \iwaycms\Base\Mappers
{
    /**
     * 默认模型类名
     *
     * @const string
     */
    const _DEFAULT_MODEL_CLASS = 'Info';

    /**
     * 默认DbTable类名
     *
     * @const string
     */
    const _DEFAULT_DBTABLE_CLASS = 'Info';

	/**
	 * 字段映射
	 * 
	 * @const Array
	 */
    const COLS_MAP = array(
			'infoId' => 'info_id',
			'bookId' => 'book_id',
			'type' => 'type',
			'title' => 'title',
			'introduce' => 'introduce',
            'url' => 'url',
            'isDel' => 'is_del',
			'createTime' => 'create_time',
	);

	/**
	 * 查询字段
	 * 
	 * @property Array
	*/
    const WHERE_BY = array(
            'type',
			'title',
			'describe',
	);
	
    /**
	 * 默认排序字段
	 * 
	 * @property Array
	 */
    const ORDER_BY = array(
			'info_id desc',
	);


	

	


 	
}