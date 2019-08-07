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

        $htmlTitle = '《' . $oModelBook->bookName . '》';
        $htmlKeywords = $oModelBook->bookName . ',' . $oModelBook->bookName . '笔记,' . $oModelBook->bookName . '读书笔记,' . $oModelBook->bookKey;
        $htmlDescription = '《' . $oModelBook->bookName . '》';


        if (empty($oModelBook->bookSubname) == false) {
            $htmlTitle .= ' - ' . $oModelBook->bookSubname;
            $htmlKeywords .= ',' . $oModelBook->bookSubname;
            $htmlDescription .= '-' . $oModelBook->bookSubname;
        }
        if (empty($infoType) == false) {
            $htmlTitle .= ' - ' . $infoType;
        }

        $htmlTitle .= ' - 读书笔记';
        $htmlDescription .= '读书笔记，';

        if (empty($oModelBook->author) == false) {
            $htmlKeywords .= ',' . $oModelBook->author;
            $htmlDescription .= '作者' . $oModelBook->author . '，';
        }
        $htmlDescription .= '包含';
        if (empty($infoType) == false) {
            $htmlKeywords .= ',' . $infoType;
            $htmlDescription .= $infoType . '、';
        } else {
            foreach ($typeList as $nowType) {
                $htmlKeywords .= ',' . $nowType;
                $htmlDescription .= $nowType . '、';
            }
        }
        $htmlDescription .= '等知识分类，';

        $titleList = '';
        $titleString = '';
        if (empty($infos['rowset']) == false) {
            foreach ($infos['rowset'] as $row) {
                $titleList .= ',' . $row['title'];
                $titleString .= $row['title'] . '、';
            }
        }
        $htmlKeywords .= $titleList;
        $htmlDescription .= '包含' . $titleString . '等知识点。';


        $this->view->setVar('htmlTitle', $htmlTitle);
        $this->view->setVar('htmlKeywords', $htmlKeywords);
        $this->view->setVar('htmlDescription', $htmlDescription);
    }

    /**
     * 页面--知识点详情
     *
     * @param int $infoId 知识点编号
     * @return string JSON 类型列表
     */
    public function infoAction($infoId = '')
    {
        // 判空
        if(empty($infoId)){
            // 跳首页
            header('Location: /');
            return;
        }

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

            $htmlTitle = $info->title . ' - ' . $info->type . ' - 《' . $oModelBook->bookName . '》';
            $htmlKeywords = $info->title . ',' . $info->type . ',' . $oModelBook->bookName;

            if (empty($oModelBook->bookSubname) == false) {
                $htmlTitle .= ' - ' . $oModelBook->bookSubname;
                $htmlKeywords .= ',' . $oModelBook->bookSubname;
            }
            $htmlTitle .= ' - 读书笔记';

            if (empty($oModelBook->author) == false) {
                $htmlKeywords .= ',' . $oModelBook->author;
            }

            $introduce = strip_tags($info->introduce);
            $introduce = trim($introduce);
            $introduce = str_replace("\t","",$introduce);
            $introduce = str_replace("\r\n","",$introduce);
            $introduce = str_replace("\r","",$introduce);
            $introduce = str_replace("\n","",$introduce);
            $introduce = str_replace(" ","",$introduce);

            $htmlDescription = $info->title . ',' . $info->type . ',' . $introduce;

            $this->view->setVar('htmlTitle', $htmlTitle);
            $this->view->setVar('htmlKeywords', $htmlKeywords);
            $this->view->setVar('htmlDescription', $htmlDescription);

        } else {
            header('Location: /');
            return;
        }

    }

    public function sitemapAction()
    {
        $this->view->cleanTemplateAfter();

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

        $filter = [
            [
                'field' => 'isDel',
                'method' => '=',
                'value' => '0',
            ],
        ];
        $infoList = \Application\Model\Info::fetchList(0, 1, null, null, $filter);

        if ($infoList['countAll'] > 0) {
            foreach ($infoList['rowset'] as $nowInfo) {
                $nowUrl['url'] = '/book/info/' .  $nowInfo['infoId'];
                $nowUrl['time'] = strtotime($oBook->createTime);
                $urlList[] = $nowUrl;
            }
        }

        $this->view->setVar('urlList', $urlList);

    }
}
  