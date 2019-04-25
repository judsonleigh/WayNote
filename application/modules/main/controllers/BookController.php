<?php
namespace Modules\Main\Controllers;

use Phalcon\Mvc\Controller;

class BookController extends Controller
{
    public function initialize()
    {

    }

    public function indexAction()
    {
        header('Location: /book/name/');
    }

	public function nameAction($bookKey = '', $infoType = '')
    {
        $bookKey = trim($bookKey);
        $infoType = trim($infoType);
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


            }
        }
        $this->view->setVar('typeList', $typeList);

        $filter = [
            [
                'field' => 'isDel',
                'method' => '=',
                'value' => '0',
            ],
        ];
        $result = \Application\Model\Book::fetchList(0,1,null,null,$filter);
        if ($result['countAll'] > 0) {
            $this->view->setVar('BookList', $result['rowset']);
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
                    $nowUrl['url'] = '/book/name/' .  $oBook->bookKey;
                    $nowUrl['time'] = strtotime($oBook->createTime);
                    $urlList[] = $nowUrl;

                    $infos = $oBook->getInfo();

                    if ($infos['countAll'] > 0) {
                        $typeList = [];
                        foreach ($infos['rowset'] as $info) {
                            $typeList[md5($info['type'])] = $info['type'];
                        }
                        foreach ($typeList as $nowType) {
                            $nowUrl['url'] = '/book/name/' .  $oBook->bookKey . '/' . $nowType;
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
  