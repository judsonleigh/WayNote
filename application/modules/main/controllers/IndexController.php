<?php
namespace Modules\Main\Controllers;

use Application\Model\Channel;
use Application\Model\Config;
use Application\Model\Content;
use Phalcon\Mvc\Controller;

class IndexController extends Controller
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
            $this->view->setVar('bookDetails', $result['rowset']);
        }

    }

}
  