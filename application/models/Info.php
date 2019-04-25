<?php
/**
 * Info
 * 
 * 知识点 Model类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model;

class Info extends \iwaycms\Base\Model {

    /**
     * 默认类名
     *
     * @const string
     */
    const _DEFAULT_CLASSNAME = 'Info';

	/**
	 * 数据库 表列名
	 * 
	 * @property Array
	 */
	protected $_properties = array(
        'infoId',
        'bookId',
        'type',
        'title',
        'introduce',
        'url',
        'isDel',
        'createTime',
	);

	/**
	 * 修改信息
	 * 
	 * @param Array $info 信息（数组）
	 * @param int $id 信息主键编号 
	 * @return bool 修改是否成功
	 */
	public function update($info, $id = NULL) {
		if ($id == NULL) {
			$id = $this->infoId;
		}	
		return parent::update($info, $id);
	}
	
	/**
	 * 删除信息
	 * 
	 * @param int $id 信息主键编号
	 * @return bool 删除是否成功
	 */
	public function delete($id = NULL) {
		if ($id == NULL) {
			$id = $this->infoId;
		}
		$info = array(
				'isDel' => 1,
				);
		return parent::update($info, $id);
	}

}