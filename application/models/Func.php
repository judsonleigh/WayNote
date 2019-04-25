<?php
/**
 * Function
 * 
 * 功能信息 Model类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model;

class Func extends \iwaycms\Base\Model {

    /**
     * 默认类名
     *
     * @const string
     */
    const _DEFAULT_CLASSNAME = 'Func';

	/**
	 * 数据库 表列名
	 * 
	 * @property Array
	 */
	protected $_properties = array(
			'id',
            'moduleName',
			'functionName',
			'functionModule',
			'functionController',
			'functionAction',
            'status',
			'isDel',
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
			$id = $this->id;	
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
			$id = $this->id;
		}
		$info = array(
				'isDel' => 1,
				);
		return parent::update($info, $id);
	}

    /**
     * 添加信息
     *
     * @param Array $info 信息（数组）
     * @return int 新ID
     */
    static public function insert($info) {
        //提交字段判断
        if ( isset($info['functionName']) == FALSE || trim($info['functionName']) == '') {
            $errorInfo = 'Function name is empty!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NULL'],
                \Application\Model\Exception::LEVEL['NOTICE'],
                __FILE__,
                __LINE__);
            return false;
        }
        if ( isset($info['functionModule']) == FALSE || trim($info['functionModule']) == '') {
            $errorInfo = 'Function module is empty!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NULL'],
                \Application\Model\Exception::LEVEL['NOTICE'],
                __FILE__,
                __LINE__);
            return false;
        }
        return parent::insert($info);
    }
}