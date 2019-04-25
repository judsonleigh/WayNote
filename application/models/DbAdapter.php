<?php
/**
 * DB
 * 
 * 数据库基础类
 *
 * @author Judson-Leigh
 * @version $Id:$
 */
namespace Application\Model;

class DbAdapter {

	/**
	 * 构造函数
	 * 
	 * @return App_Model_Base
	 */
	public function __construct() {
	
	}
	
	/**
	 * 析构函数
	 */
	public function __destruct() {
	
	}
	
	/**
	 * Quotes a value and places into a piece of text at a placeholder.
	 *
	 * The placeholder is a question-mark; all placeholders will be replaced
	 * with the quoted value.   For example:
	 *
	 * <code>
	 * $text = "WHERE date < ?";
	 * $date = "2005-01-02";
	 * $safe = $sql->quoteInto($text, $date);
	 * // $safe = "WHERE date < '2005-01-02'"
	 * </code>
	 *
	 * @param string  $text  The text with a placeholder.
	 * @param mixed   $value The value to quote.
	 * @return string An SQL-safe quoted value placed into the original text.
	 */
	public function quoteInto($text, $value)
	{
        return str_replace('?', $this->quote($value), $text);
	}
	
	/**
	 * Safely quotes a value for an SQL statement.
	 *
	 * If an array is passed as the value, the array values are quoted
	 * and then returned as a comma-separated string.
	 *
	 * @param mixed $value The value to quote.
	 * @param mixed $type  OPTIONAL the SQL datatype name, or constant, or null.
	 * @return mixed An SQL-safe quoted value (or string of separated values).
	 */
	public function quote($value, $type = null)
	{
	    if (is_array($value)) {
	        foreach ($value as &$val) {
	            $val = $this->quote($val, $type);
	        }
	        return implode(', ', $value);
	    }

	    return $this->_quote($value);
	}
	
	/**
	 * Quote a raw string.
	 *
	 * @param string $value     Raw string
	 * @return string           Quoted string
	 */
	protected function _quote($value)
	{
	    if (is_int($value)) {
	        return $value;
	    } elseif (is_float($value)) {
	        return sprintf('%F', $value);
	    }
	    return "'" . addcslashes($value, "\000\n\r\\'\"\032") . "'";
	}
	
	
}