<?php
/**
 * AdminGroup
 *
 * 管理员组 映射类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model\Mappers;

class AdminGroup extends \iwaycms\Base\Mappers
{
    /**
     * 默认模型类名
     *
     * @const string
     */
    const _DEFAULT_MODEL_CLASS = 'AdminGroup';

    /**
     * 默认DbTable类名
     *
     * @const string
     */
    const _DEFAULT_DBTABLE_CLASS = 'AdminGroup';

    /**
     * 字段映射
     *
     * @const Array
     */
    const COLS_MAP = array(
			'id' => 'admin_group_id',
			'name' => 'admin_group_name',
			'status' => 'status',
            'isDel' => 'is_del',
    );

    /**
     * 查询字段
     *
     * @property Array
     */
    const WHERE_BY = array(
			'admin_group_name',
	);

    /**
     * 默认排序字段
     *
     * @property Array
     */
    const ORDER_BY = array(
			'admin_group_id desc',
	);
	

}