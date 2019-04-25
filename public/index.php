<?php
use Phalcon\Loader;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Router;
use Phalcon\DI\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Config\Adapter\Ini as ConfigIni;

try {

    // Register an autoloader
    $loader = new Loader();

    $loader->registerDirs(
        array(
            '../application/controllers/',
        )
    )->register();

    $di = new FactoryDefault();

    //记录配置
    $di->set(
        "config",
        function () {
            //配置文件处理
            if (empty($GLOBALS['config']) == true) {

                $allConfig = @new ConfigIni("../application/configs/application.ini");
                $allConfig = (array)$allConfig;

                $strApplicationEnv = trim(getenv('APPLICATION_ENV'));

                $config = array();
                $phpSettings = array();


                if (isset($allConfig['production'])) {
                    //默认配置存在
                    if (isset($allConfig[$strApplicationEnv . ' : production'])) {
                        //其它环境配置存在,配置合并
                        $allConfig['production']->merge($allConfig[$strApplicationEnv . ' : production']);
                    }
                    $phpSettings = (array)$allConfig['production']->phpSettings;
                    $config = (array)$allConfig['production']->app_config;

                    foreach ($config as $key => $value) {
                        $config[$key] = (array)$value;
                    }
                }

                foreach ($phpSettings as $key => $value) {
                    ini_set($key, $value);
                }

                $GLOBALS['config'] = $config;
            }

            return $GLOBALS['config'];
        }
    );

    $config = $di->get('config');

    $strApplicationModelBasePath = $config['system']['path'] . $config['application']['model_class_path'];
    $strAppBasePath = $config['system']['path'] . $config['application']['base_class_path'];

    $loader->registerNamespaces(array(
        'iwaycms\Base' => $strAppBasePath,
        'Application\Model' => $strApplicationModelBasePath,
        'Application\Model\Mappers' => $strApplicationModelBasePath . '/Mappers',
        'Application\Model\DbTable' => $strApplicationModelBasePath . '/DbTable',
    ));

    $loader->register();

    ini_set("session.cookie_domain", $config['site']['domain']);
    // Create a DI

    //初始化SESSION
    $di->setShared('session', function () {
        $session = new Phalcon\Session\Adapter\Files();
        $session->start();
        return $session;
    });

    //初始化日志
    \Application\Model\Log::init($config['system']['path'] . $config['log']['path']);

    $di->set('profiler',
        function () {
            return
                new
                \Phalcon\Db\Profiler();
        },
        true);

    // Set the database service
    $di->set('db', function() use ($di) {
        //新建一个事件管理器
        $eventsManager = new \Phalcon\Events\Manager();

        //从di中获取共享的profiler实例
        $profiler = $di->getProfiler();

        //监听所有的db事件
        $eventsManager->attach('db', function($event, $connection) use ($profiler) {
            //一条语句查询之前事件，profiler开始记录sql语句
            if ($event->getType() == 'beforeQuery') {
                $profiler->startProfile($connection->getRealSQLStatement());
            }

            //一条语句查询结束，结束本次记录，记录结果会保存在profiler对象中
            if ($event->getType() == 'afterQuery') {
                $profiler->stopProfile();
            }
        });

        $connection = new DbAdapter(array(
            "host"     => $GLOBALS['config']['database']['host'],
            "username" => $GLOBALS['config']['database']['username'],
            "password" => $GLOBALS['config']['database']['password'],
            "dbname"   => $GLOBALS['config']['database']['dbname'],
        ));

        //将事件管理器绑定到db实例中
        $connection->setEventsManager($eventsManager);

        return $connection;
    });




    //创建路由规则
    $di->set('router',function(){
        $router = new Router();


        $router->add("/management-platform/", array(
            'module'     => "management-platform",
            'controller' => "index",
            'action' => "index",
        ));

        $router->add("/management-platform/:controller/", array(
            'module'     => "management-platform",
            'controller' => 1,
            'action' => "index",
        ));

        $router->add("/management-platform/:controller/:action/", array(
            'module'     => "management-platform",
            'controller' => 1,
            'action' => 2,
        ));

        $router->add("/management-platform/:controller/:action/:params", array(
            'module'     => "management-platform",
            'controller' => 1,
            'action' => 2,
            'params' => 3,
        ));
        //英文模块
        $router->add("/en", array(
            'module'     => "en",
            'controller' => "index",
            'action' => "index",
        ));

        $router->add("/en/", array(
            'module'     => "en",
            'controller' => "index",
            'action' => "index",
        ));

        $router->add("/en/:controller", array(
            'module'     => "en",
            'controller' => 1,
            'action' => "index",
        ));
        $router->add("/en/:controller/", array(
            'module'     => "en",
            'controller' => 1,
            'action' => "index",
        ));

        $router->add("/en/:controller/:action", array(
            'module'     => "en",
            'controller' => 1,
            'action' => 2,
        ));
        $router->add("/en/:controller/:action/", array(
            'module'     => "en",
            'controller' => 1,
            'action' => 2,
        ));

        $router->add("/en/:controller/:action/:params", array(
            'module'     => "en",
            'controller' => 1,
            'action' => 2,
            'params' => 3,
        ));
        $router->add("/en/:controller/:action/:params/", array(
            'module'     => "en",
            'controller' => 1,
            'action' => 2,
            'params' => 3,
        ));


        $router->setDefaultModule("main");
        $router->setDefaultController('index');
        $router->setDefaultAction('index');

        return $router;
    });

    // Handle the request
    $application = new Application($di);

    // 注册模块
    $application->registerModules(
        [
            "main" => [
                "className" => "Modules\\Main\\Module",
                "path"      => "../application/modules/main/Module.php",
            ],
            "management-platform"  => [
                "className" => "Modules\\ManagementPlatform\\Module",
                "path"      => "../application/modules/management-platform/Module.php",
            ],
            "en"  => [
                "className" => "Modules\\En\\Module",
                "path"      => "../application/modules/en/Module.php",
    ]
        ]
    );

    // 处理请求
    $response = $application->handle();

    $response->send();

} catch (Exception $e) {

    //$exceptionInfo = "End Exception: " . $e->getMessage();
    //$exceptionInfo .= "\nFile: " . $e->getFile();
    //$exceptionInfo .= "\nLine: " . $e->getLine();
    $returnJson = array();
    $returnJson['errorCode'] = $e->getCode();
    //$returnJson['errorSerialNumber'] = $e->getSerialNumber();
    $returnJson['errorMsg'] = $e->getMessage();
    echo json_encode($returnJson);
}