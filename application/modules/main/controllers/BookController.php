<?php
namespace Modules\Main\Controllers;

use Phalcon\Mvc\Controller;

class BookController extends Controller
{
    public function initialize()
    {
        $this->view->setTemplateAfter('default');

        // 书籍清单菜单
        // 最新书籍优先
        $order = [
            [
                'field' => 'createTime',
                'desc' => true,
            ],
        ];
        $filter = [
            [
                'field' => 'isDel',
                'method' => '=',
                'value' => '0',
            ],
        ];
        $result = \Application\Model\Book::fetchList(0,1,$order,null,$filter);
        if ($result['countAll'] > 0) {
            $bookList = array();

            foreach ($result['rowset'] as $row){
                $book = array();
                $book['bookKey'] = $row['bookKey'];
                $book['bookName'] = $row['bookName'];
                $bookList[] = $book;
            }
            $this->view->setVar('bookList', $bookList);
        }

    }

    public function indexAction()
    {
        header('Location: /');
    }

	public function nameAction($bookKey = '', $infoType = '')
    {

        $bookKey = trim($bookKey);
        $infoType = trim($infoType);

        if ($infoType == 'index')
            $infoType = '';
        $filter = [
            [
                'field' => 'isDel',
                'method' => '=',
                'value' => '0',
            ],
            [
                'field' => 'bookKey',
                'method' => '=',
                'value' => $bookKey,
            ],
        ];
        $result = \Application\Model\Book::fetchList(0,1,null,null,$filter);

        $typeList = [];

        if ($result['countAll'] > 0) {
            $oModelBook = \Application\Model\Book::fetchById($result['rowset'][0]['bookId']);
            if (empty($oModelBook) == false) {
                $this->view->setVar('oBook', $oModelBook);

                $infos = $oModelBook->getInfo();

                if (empty($infos) == false) {

                    foreach ($infos['rowset'] as $row) {
                        $typeList[md5($row['type'])] = $row['type'];
                    }
                }

                if (empty($infoType) == false) {
                    $filter = [
                        [
                            'field' => 'type',
                            'method' => '=',
                            'value' => $infoType,
                        ],
                    ];
                    $infos = $oModelBook->getInfo(0,1,null,null,$filter);
                    $this->view->setVar('infoType', $infoType);
                }
                $this->view->setVar('infoList', $infos['rowset']);

                $commentList = $oModelBook->getComment(1);

                if (empty($commentList) == false) {
                    $this->view->setVar('commentList', $commentList['rowset']);
                }

            }
        }
        $this->view->setVar('typeList', $typeList);


    }

    /**
     * 页面--知识点详情
     *
     * @param int $infoId 知识点编号
     * @return string JSON 类型列表
     */
    public function infoAction($infoId = '')
    {
        // 知识点
        $info = \Application\Model\Info::fetchById(intval($infoId));

        if(!empty($info)){

            // 书
            $oModelBook = \Application\Model\Book::fetchById($info->bookId);
            if (empty($oModelBook) == false) {
                $this->view->setVar('oBook', $oModelBook);

                $infos = $oModelBook->getInfo();

                if (empty($infos) == false) {

                    foreach ($infos['rowset'] as $row) {
                        $typeList[md5($row['type'])] = $row['type'];
                    }
                }

                $this->view->setVar('info', $info);

                $commentList = $oModelBook->getComment(1);

                if (empty($commentList) == false) {
                    $this->view->setVar('commentList', $commentList['rowset']);
                }

            }

        $this->view->setVar('typeList', $typeList);

        }

    }

    public function sitemapAction()
    {
        header('Content-Type: text/xml');//这行很重要，php默认输出text/html格式的文件，所以这里明确告诉浏览器输出的格式为xml,不然浏览器显示不出xml的格式

        $filter = [
            [
                'field' => 'isDel',
                'method' => '=',
                'value' => '0',
            ],
        ];
        $result = \Application\Model\Book::fetchList(0, 1, null, null, $filter);

        $urlList = [];

        if ($result['countAll'] > 0) {
            foreach ($result['rowset'] as $nowBook) {
                $oBook = \Application\Model\Book::fetchById($nowBook['bookId']);
                if (empty($oBook) == false) {
                    $nowUrl['url'] = '/' .  $oBook->bookKey;
                    $nowUrl['time'] = strtotime($oBook->createTime);
                    $urlList[] = $nowUrl;

                    $infos = $oBook->getInfo();

                    if ($infos['countAll'] > 0) {
                        $typeList = [];
                        foreach ($infos['rowset'] as $info) {
                            $typeList[md5($info['type'])] = $info['type'];
                        }
                        foreach ($typeList as $nowType) {
                            $nowUrl['url'] = '/' .  $oBook->bookKey . '/' . $nowType;
                            $nowUrl['time'] = strtotime($oBook->createTime);
                            $urlList[] = $nowUrl;

                        }
                    }
                }
            }
        }
        $this->view->setVar('urlList', $urlList);

    }
}
  