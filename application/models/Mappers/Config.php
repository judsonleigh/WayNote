<?php
/**
 * Config
 *
 * 系统配置 映射类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model\Mappers;

class Config extends \iwaycms\Base\Mappers
{
    /**
     * 默认模型类名
     *
     * @const string
     */
    const _DEFAULT_MODEL_CLASS = 'Config';

    /**
     * 默认DbTable类名
     *
     * @const string
     */
    const _DEFAULT_DBTABLE_CLASS = 'Config';

    /**
     * 字段映射
     *
     * @const Array
     */
    const COLS_MAP = array(
			'id' => 'config_id',
			'title' => 'title',
			'marker' => 'marker',
			'value' => 'config_value',
            'isDel' => 'is_del',
			'createTime' => 'create_time',
	);

    /**
     * 查询字段
     *
     * @const Array
     */
    const WHERE_BY = array(
			'title',
			'marker',
	);

    /**
	 * 默认排序字段
	 *
	 * @property Array
	 */
	protected $_orderBy = array(
			'id',
	);
 	


}