<?php
/**
 * Book
 * 
 * 书籍 Model类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model;

class Book extends \iwaycms\Base\Model {

    /**
     * 默认类名
     *
     * @const string
     */
    const _DEFAULT_CLASSNAME = 'Book';

	/**
	 * 数据库 表列名
	 * 
	 * @property Array
	 */
	protected $_properties = array(
        'bookId',
        'bookKey',
        'bookName',
        'bookSubname',
        'author',
        'pic',
        'readUrl',
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
			$id = $this->bookId;
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
			$id = $this->bookId;
		}
		$info = array(
				'isDel' => 1,
				);
		return parent::update($info, $id);
	}

    /**
     * 取书籍下知识点列表
     *
     * @param int $pageLine OPTIONAL 分页行数
     * @param int $pageNum OPTIONAL 当前页数
     * @param Array $order OPTIONAL 排序条件
     * 				$order[n]['field'] string 字段名
     * 				$order[n]['desc'] bool 是否倒序
     * @param String $whereKeyword OPTIONAL 查询条件
     * @param Array $filter OPTIONAL 筛选条件
     * 				$filter[n]['field'] string 字段名
     * 				$filter[n]['value'] string 筛选值
     * 				$filter[n]['method'] string 判断方法 ('='、'>'、'<'、'>='、'<='、'in')
     * @return Array 内容查询结果
     * 				$return['rowset']	array 结果集
     * 				$return['countAll'] int 总计记录数
     * 				$return['countNow'] int 当前页记录数
     * 				$return['pageLine'] int 分页行数
     * 				$return['pageNum'] int 当前页数
     * 				$return['pageCount'] int 总页数
     */
    public function getInfo($pageLine = 0, $pageNum = 1, $order = null, $whereKeyword = null, $filter = null) {
        $strInfoModel = self::_NAMESPACE . 'Info';

        $filter[] = array(
            'field' => 'bookId',
            'value' => $this->bookId,
            'method' => '=',
        );
        $filter[] = array(
            'field' => 'isDel',
            'value' => 0,
            'method' => '=',
        );

        return $strInfoModel::fetchList( $pageLine, $pageNum, $order, $whereKeyword, $filter );
    }

}