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

    }

	public function indexAction()
    {
        //header('Location: /book/home/');
        //test
        header('Location: /book/name/');
    }
}
  