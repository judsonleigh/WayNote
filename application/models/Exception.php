<?php
/**
 * Exception
 * 
 * 抛出异常 Model类
 *
 * @author Judson Leigh
 * @version $Id:$
 */

namespace Application\Model;


class Exception extends \ErrorException {
    
    /**
     * 异常类型
     */
    const TYPE = array(
        'OK' => 0,   //正常
        'UNKNOW' => 100,     //未知
        
        'PARAM_NULL' => 1101,               //输入参数为空
        'PARAM_TYPE_ERR' => 1102,           //输入参数类型错误
        'PARAM_RANGE_ERR' => 1103,          //输入参数范围错误
        'PARAM_NO_RESOURCE' => 1104,        //输入参数的资源不存在
        'PARAM_RESOURCE_EXISTS' => 1105,    //输入参数的资源已存在
        
        'DB_CONN_FAILS' => 2101,            //数据库连接失败 
        'DB_VERIFY_ERR' => 2102,            //用户验证错误
        'DB_SQL_GRAMMAR_ERR' => 2103,       //sql语法错误
        'DB_SQL_NO_AUTHORITY' => 2104,      //sql无权限
        'DB_SQL_INSERT_ERR' => 2105,        //insert错误
        'DB_SQL_UPDATE_ERR' => 2106,        //update错误
        'DB_SQL_DELETE_ERR' => 2107,        //delete错误
        'DB_SQL_SELECT_ERR' => 2108,        //select错误
        'DB_DATA_NULL' => 2109,             //没有数据
        
        'FILE_CANNOT_BE_OPENED' => 2201,    //文件无法打开
        'FILE_CANNOT_BE_READ' => 2202,      //文件不可读
        'FILE_CANNOT_BE_WRITTEN' => 2203,   //文件不可写
        'FILE_CANNOT_BE_DELETE' => 2204,    //文件不可删
        'FILE_UPLOAD_FAILED' => 2205,       //文件上传失败
        
        'URL_CANNOT_BE_OPENED' => 2301,     //URL无法打开
        'URL_CANNOT_BE_READ' => 2302,       //URL不可读
        'URL_CANNOT_BE_WRITTEN' => 2303,    //URL不可写
        
        
        
        'PAY_NO_PLATFORM' => 3101,          //支付平台不存在
        'PAY_REQUEST_ERROR' => 3102,        //支付请求错误
        'PAY_PAID' => 3103,                 //已经支付
        'PAY_AMOUNT_ERROR' => 3104,         //支付金额错误
        'PAY_RECEIPT_FAILURE' => 3105,      //支付回执解析失败
        'PAY_NO_SERVER' => 3106,            //支付业务不存在
        'PAY_NOT_PAID' => 3107,             //未支付
        'PAY_REFUND' => 3108,               //已退款
        'PAY_REQUEST_REFUND_FAIL' => 3109,  //退款申请失败
        'PAY_QUERY_REFUND_FAIL' => 3110,    //退款查询失败
        'PAY_SIGN_FAIL' => 3201,            //数据签名失败
        'PAY_VERIFY_FAIL' => 3202,          //验证签名失败
        
    );
    
    /**
     * 异常类型
     */
    const LEVEL = array(
        'ERROR' => E_ERROR,       //错误
        'WARNING' => E_WARNING,   //警告
        'NOTICE' => E_NOTICE,     //通知
    );

	/**
	 * 异常句柄
	 *
	 * @property mixed
	 */
	protected $_serialNumber = null;
	
	/**
	 * 重载构造方法
	 */
	public function __construct($message, $code = self::TYPE['UNKNOW'] , $severity = self::LEVEL['ERROR'] , $filename = __FILE__ , $lineno = __LINE__ ){
	    $serialNumber = $this->_serialNumber = 'E' . date('ymdHis') . mt_rand(1000000,9999999);
	    $logInfo = array(
            'SN' => $serialNumber,
            'TYPE' => $code,
            'LEVEL' => $severity,
            'INFO' => $message,
            'FILE' => $filename,
            'line' => $lineno,
        );

	    if ($severity == self::LEVEL['ERROR']) {
	        $logLevel = \Application\Model\Log::LEVEL['ERR'];
	    } elseif ($severity == self::LEVEL['WARNING']) {
	        $logLevel = \Application\Model\Log::LEVEL['WARNING'];
	    } elseif ($severity == self::LEVEL['NOTICE']) {
	        $logLevel = \Application\Model\Log::LEVEL['NOTICE'];
	    } else {
            $logLevel = \Application\Model\Log::LEVEL['NOTICE'];
        }

        \Application\Model\Log::log($logInfo,$logLevel);
	    
	    parent::__construct($message, $code, $severity, $filename, $lineno);
	}
	
	/**
	 * 查询异常日志编号
	 */
	public function getSerialNumber(){
	    return $this->_serialNumber;
	}
	

}
