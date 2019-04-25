<?php
/**
 * Admin
 *
 * 管理员 映射类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model\Mappers;

class Admin extends \iwaycms\Base\Mappers
{
    /**
     * 默认模型类名
     *
     * @const string
     */
    const _DEFAULT_MODEL_CLASS = 'Admin';

    /**
     * 默认DbTable类名
     *
     * @const string
     */
    const _DEFAULT_DBTABLE_CLASS = 'Admin';

	/**
	 * 字段映射
	 * 
	 * @const Array
	 */
    const COLS_MAP = array(
			'adminId' => 'admin_id',
			'username' => 'username',
			'realname' => 'realname',
			'password' => 'password',
			'type' => 'type',
            'status' => 'status',
            'isDel' => 'is_del',
			'createTime' => 'create_time',
	);

	/**
	 * 查询字段
	 * 
	 * @property Array
	*/
    const WHERE_BY = array(
			'username',
			'realname',
	);
	
    /**
	 * 默认排序字段
	 * 
	 * @property Array
	 */
    const ORDER_BY = array(
			'admin_id desc',
	);


	

	


 	
}