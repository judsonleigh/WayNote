<?php

namespace Modules\Main;
use Exception;
use Phalcon\Dispatcher;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Dispatcher\Exception as DispatchException;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\DiInterface;
//use Phalcon\Mvc\Dispatcher;
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

                $eventsManager = new EventsManager();
                $eventsManager->attach(
                    'dispatch:beforeException',
                    function (Event $event, $dispatcher, Exception $exception) {

                        // Handle 404 exceptions
                        if ($exception instanceof DispatchException) {

                            $dispatcher->forward(
                                [
                                    'controller' => 'book',
                                    'action'     => 'name',
                                    'params' => [
                                        $dispatcher->getControllerName(),
                                        $dispatcher->getActionName(),
                                    ]

                                ]
                            );

                            return false;
                        }

                        // Alternative way, controller or action doesn't exist
                        switch ($exception->getCode()) {
                            case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                            case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                                $dispatcher->forward(
                                    [
                                        'controller' => 'book',
                                        'action'     => 'name',
                                        'params' => [
                                            $dispatcher->getControllerName(),
                                            $dispatcher->getActionName(),
                                        ]

                                    ]
                                );

                                return false;
                        }
                    }
                );

                $dispatcher = new MvcDispatcher();
                $dispatcher->setDefaultNamespace("Modules\\Main\\Controllers");

                // Bind the EventsManager to the dispatcher
                $dispatcher->setEventsManager($eventsManager);

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