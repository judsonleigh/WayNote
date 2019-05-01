<?php
namespace Modules\Main\Controllers;

use Phalcon\Mvc\Controller;

class CommentController extends Controller
{
    public function initialize()
    {

    }

    public function submitAction()
    {
        $bookId = intval($this->request->getPost('bookId'));
        $realname = trim($this->request->getPost('commentRealname'));
        $email = trim($this->request->getPost('commentEmail'));
        $content = trim($this->request->getPost('commentContent'));

        $result = \Application\Model\Comment::submit($bookId, $content, $realname, $email);

        if (empty($result) == false) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

}
  