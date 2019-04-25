<?php
/**
 * File
 *
 * 文件 映射类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model\Mappers;

class File extends \iwaycms\Base\Mappers
{
    /**
     * 默认模型类名
     *
     * @const string
     */
    const _DEFAULT_MODEL_CLASS = 'File';

    /**
     * 默认DbTable类名
     *
     * @const string
     */
    const _DEFAULT_DBTABLE_CLASS = 'File';

    /**
     * 字段映射
     *
     * @const Array
     */
    const COLS_MAP = array(
			'id' => 'file_id',
			'mark' => 'mark',
			'filenameSrc' => 'filename_src',			
			'size' => 'size',
			'mimeType' => 'mime_type',
			'localPath' => 'local_path',
			'createTime' => 'createtime',
			'isDel' => 'is_del',
	);

    /**
     * 查询字段
     *
     * @const Array
     */
    const WHERE_BY = array(
			'mark',
			'filename_src',
	);

    /**
     * 默认排序字段
     *
     * @const Array
     */
    const ORDER_BY = array(
			'file_id',
	);
 	

	

}