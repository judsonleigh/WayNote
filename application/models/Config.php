<?php
/**
 * Config
 * 
 * 系统配置 Model类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model;

class Config extends \iwaycms\Base\Model {

    /**
     * 默认类名
     *
     * @const string
     */
    const _DEFAULT_CLASSNAME = 'Config';

	/**
	 * 数据库 表列名
	 * 
	 * @property Array
	 */
	protected $_properties = array(
			'id',
			'title',
			'marker',
			'value',
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
	 * 根据标识取配置内容
	 *
	 * @param String $marker 标识
	 * @return String 配置内容
	 */
	static public function getValueByMark($marker) {
	    
	    $marker = trim($marker);
	     
	    //判断编号类型
	    if (empty($marker) == true) {
            $errorInfo = 'Marker is empty!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NULL'],
                \Application\Model\Exception::LEVEL['NOTICE'],
                __FILE__,
                __LINE__);
	        return '';
	    }
	    
	    $oModelConfig = self::fetchByMark($marker);
	    if (empty($oModelConfig) == true) {
	        return '';
	    }
	    
	    return $oModelConfig->value;
	}

    /**
     * 添加信息
     *
     * @param Array $info 信息（数组）
     * @return int 新ID
     */
    static public function insert($info) {
        //提交字段判断
        if ( isset($info['title']) == FALSE || trim($info['title']) == '') {
            $errorInfo = 'Title is null!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NULL'],
                \Application\Model\Exception::LEVEL['NOTICE'],
                __FILE__,
                __LINE__);
            return false;
        }

        if ( isset($info['marker']) == FALSE || trim($info['marker']) == '') {
            $errorInfo = 'Marker is null!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NULL'],
                \Application\Model\Exception::LEVEL['NOTICE'],
                __FILE__,
                __LINE__);
            return false;
        }
        $info['createTime'] = date('Y-m-d H:i:s');
        return parent::insert($info);
    }


    /**
     * 根据标识取配置信息
     *
     * @param String $marker 标识
     * @return \Application\Model\Config 系统配置 Model类
     */
    static public function fetchByMark($marker) {
        $dbTableClass = self::_NAMESPACE . 'DbTable\\' . self::_DEFAULT_CLASSNAME;
        $strMapperClass = self::_NAMESPACE . 'Mappers\\' . self::_DEFAULT_CLASSNAME;

        $marker = trim($marker);

        //判断编号类型
        if (empty($marker) == true) {
            $errorInfo = 'Marker is null!';
            throw new \Application\Model\Exception($errorInfo,
                \Application\Model\Exception::TYPE['PARAM_NULL'],
                \Application\Model\Exception::LEVEL['NOTICE'],
                __FILE__,
                __LINE__);
            return false;
        }

        //创建DbTable类
        $dbTable = new $dbTableClass;

        $db = $dbTable->getAdapter();

        $where = $db->quoteInto($strMapperClass::COLS_MAP['marker'] . ' = ?', $marker)
            . $db->quoteInto(' AND ' . $strMapperClass::COLS_MAP['isDel'] . ' = ?', '0');

        $row = $dbTable->fetchRow($where);

        if ($row === false) {
            return false;
        }

        if (empty($row)) {
            return null;
        }
        if(is_array($row) == false){
            $returnArray = $row->toArray();
        }else{
            $returnArray = $row;
        }
        return self::fetchById($returnArray[$strMapperClass::COLS_MAP['id']]);
    }

    /**
     * 根据编号取信息
     *
     * @param int $id 编号
     * @return \Application\Model\Config Model
     */
    static public function fetchById($id) {
        return parent::fetchById($id);
    }
}