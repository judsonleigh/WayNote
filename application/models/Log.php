<?php
/**
 * Log
 * 
 * 日志 Model类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model;

class Log {
	/**
	 * 日志级别
	 */
	const LEVEL = array(
	    'EMERG' => LOG_EMERG,       //紧急情况
	    'ALERT' => LOG_ALERT,       //应该被立即改正的问题，如系统数据库破坏
	    'CRIT' => LOG_CRIT,         //重要情况，如硬盘错误
	    'ERR' => LOG_ERR,           //错误 
	    'WARNING' => LOG_WARNING,   //警告信息 
	    'NOTICE' => LOG_NOTICE,     //不是错误情况，但是可能需要处理 
	    'INFO' => LOG_INFO,         //情报信息
	    'DEBUG' => LOG_DEBUG,       //调试日志
	);
	
	/**
	 * 记录日志
	 * 
	 * @param String $logInfo 日志信息
	 * @param int $level OPTIONAL 日志级别
	 *
	 * @return bool 是否记录成功
	 */
    static public function log($logInfo, $level = self::LEVEL['DEBUG']) {
         if (empty($GLOBALS['logger']) == false && get_class($GLOBALS['logger']) == 'Phalcon\Logger\Adapter\File') {

            if (is_string($logInfo) == false) {
                $logInfo = json_encode($logInfo);
            }
            $level = intval($level);
            $GLOBALS['logger']->log($level, $logInfo);
            return true;
        } else {
            return false;
        }

	}

	
	/**
	 * 初始化日志
     *
	 * @param String $logPath 日志路径
     * @return mixed 开启日志
	 */
	static public function init($logPath) {
        $logPath = trim($logPath);
        if (empty($GLOBALS['logger']) == true || get_class($GLOBALS['logger']) != 'Phalcon\Logger\Adapter\File') {
            $logFilename = 'application-' . date('ymd') . '.log';

            $GLOBALS['logger'] = new \Phalcon\Logger\Adapter\File($logPath . $logFilename);
        }

	    return $GLOBALS['logger'];
	}
	


	
}