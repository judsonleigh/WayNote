<?php

namespace Modules\ManagementPlatform;

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
                "Modules\\ManagementPlatform\\Controllers" => "../application/modules/management-platform/controllers/",
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

                $dispatcher->setDefaultNamespace("Modules\\ManagementPlatform\\Controllers");

                return $dispatcher;
            }
        );

        // Registering the view component
        $di->set(
            "view",
            function () {
                $view = new View();

                $view->setViewsDir("../application/modules/management-platform/views/");

                return $view;
            }
        );
        $config = $di->get('config');



        $di->set(
            "adminNavigation",
            function () {
                //加载后台菜单配置

                if (empty($GLOBALS['adminNavigation']) == true) {
                    $config = \Phalcon\DI::getDefault()->get('config');

                    $strXmlFile = $config['system']['path'] . $config['management-platform']['navigation_xml'];
                    $navigation = simplexml_load_file($strXmlFile);
                    $GLOBALS['adminNavigation'] = $navigation->navigation;
                }
                return $GLOBALS['adminNavigation'];
            }
        );

        $di->set(
            "language",
            function () {
                //加载后台菜单配置

                if (empty($GLOBALS['language']) == true) {
                    $config = \Phalcon\DI::getDefault()->get('config');
                    $arrayLangKey = explode('|', $config['language']['key']);
                    $arrayLangTitle = explode('|', $config['language']['title']);

                    $arrayLanguage = array();
                    foreach ($arrayLangKey as $key=>$value) {
                        $arrayLanguage[$value] = $arrayLangTitle[$key];
                    }
                    $GLOBALS['language'] = $arrayLanguage;
                }
                return $GLOBALS['language'];
            }
        );
    }



}