<?php

namespace Modules\Main;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\DiInterface;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{
    /**
     * 注册自定义加载器
     */
    public function registerAutoloaders(DiInterface $di = null)
    {
        $loader = new Loader();

        $loader->registerNamespaces(
            [
                "Modules\\Main\\Controllers" => "../application/modules/main/controllers/",
            ]
        );

        $loader->register();
    }

    /**
     * 注册自定义服务
     */
    public function registerServices(DiInterface $di)
    {
        // Registering a dispatcher
        $di->set(
            "dispatcher",
            function () {
                $dispatcher = new Dispatcher();

                $dispatcher->setDefaultNamespace("Modules\\Main\\Controllers");

                return $dispatcher;
            }
        );

        // Registering the view component
        $di->set(
            "view",
            function () {
                $view = new View();

                $view->setViewsDir("../application/modules/main/views/");

                return $view;
            }
        );
    }
}