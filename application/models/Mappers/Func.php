<?php
/**
 * Func
 *
 * 功能信息 映射类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model\Mappers;

class Func extends \iwaycms\Base\Mappers
{
    /**
     * 默认模型类名
     *
     * @const string
     */
    const _DEFAULT_MODEL_CLASS = 'Func';

    /**
     * 默认DbTable类名
     *
     * @const string
     */
    const _DEFAULT_DBTABLE_CLASS = 'Func';

    /**
     * 字段映射
     *
     * @const Array
     */
    const COLS_MAP = array(
			'id' => 'function_id',
            'moduleName' => 'module_name',
			'functionName' => 'function_name',
			'functionModule' => 'module',
			'functionController' => 'controller',
			'functionAction' => 'action',
            'status' => 'status',
            'isDel' => 'is_del',
	);

    /**
     * 查询字段
     *
     * @property Array
     */
    const WHERE_BY = array(
			'function_name',
			'module',
			'controller',
			'action',
	);

    /**
     * 默认排序字段
     *
     * @property Array
     */
    const ORDER_BY = array(
			'function_id',
	);

}