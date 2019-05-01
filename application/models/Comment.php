<?php
/**
 * Comment
 * 
 * 评论 Model类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model;

class Comment extends \iwaycms\Base\Model {

    /**
     * 默认类名
     *
     * @const string
     */
    const _DEFAULT_CLASSNAME = 'Comment';

	/**
	 * 数据库 表列名
	 * 
	 * @property Array
	 */
	protected $_properties = array(
        'commentId',
        'bookId',
        'realname',
        'email',
        'content',
        'status',
        'ipAddr',
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
			$id = $this->commentId;
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
			$id = $this->commentId;
		}
		$info = array(
				'isDel' => 1,
				);
		return parent::update($info, $id);
	}

	/**
     * 提交评论
     *
     * @param Array $info 信息（数组）
     * @return bool 修改是否成功
     */
    static public function submit($bookId, $content, $realname = '', $email = '') {
        $bookId = intval($bookId);
        if (empty($bookId) == true) {
            return false;
        }
        $content = trim($content);
        $realname = trim($realname);
        $email = trim($email);
        if (empty($content) == true) {
            return false;
        }
        $info = [
            'bookId' => $bookId,
            'realname' => $realname,
            'email' => $email,
            'content' => $content,
            'ipAddr' => $_SERVER['REMOTE_ADDR'],
        ];

        return self::insert($info);
    }

    /**
     * 评论审核
     *
     * @param int $audit 审核结果 1.审核成功 -1.审核失败
     * @return bool 审核是否成功
     */
    public function audit($audit) {
        $audit = intval($audit);
        $info = [];
        if ($audit == 1) {
            $info['status'] = 1;
        } else {
            $info['status'] = -1;
        }

        return $this->update($info);
    }


}