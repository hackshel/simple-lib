<?php

/**
 * Wrapper class for DBProxy
 *
 * @category    DB
 * @package        DBProxy
 * @version        $Revision: 1.2 $
 */
class DBProxyWrapper extends DBProxy
{
    /**
     * Get a bdDBProxyWrapper instance for the specified database.
     *
     * @see bdDBProxy::getInstance()
     *
     * @param string $database
     * @return bdDBProxyWrapper
     */
    public static function getInstance($database, $isBackupDB = false)
    {
        return self::_getInstance(__CLASS__, $database, $isBackupDB);
    }

    /**
     * Query all the result rows, each row as associated array
     * Caller should pass all the argument for format string following the $format parameter
     * example:
     * <code>
     * $ret = $db->queryAllRows('SELECT * FROM tb WHERE uid=%d', $uid);
     * </code>
     *
     * @param string $format    SQL query string template
     * @return bool|array    Return array on success or false on failure
     */
    public function queryAllRows($format)
    {
        $argv = func_get_args();
        $sql = call_user_func_array(array($this, 'buildSqlStr'), $argv);
        return $this->__doSelectQuery($format, $sql, 1);
    }

    /**
     * Query all the result rows, each row as associated array
     * @param string $format    SQL query string template, can be empty,
     *                             just for Log printing when needed
     * @param string $sql        SQL query string
     * @return bool|array        Return array on success or false on failure
     */
    public function queryAllRowsEx($format, $sql)
    {
        return $this->__doSelectQuery($format, $sql, 1);
    }

    /**
     * Query the first row of the result as associated array
     * Caller should pass all the argument for format string following the $format parameter
     * example:
     * <code>
     * $ret = $db->queryFirstRow('SELECT * FROM tb WHERE uid=%d', $uid);
     * </code>
     *
     * @param string $format    SQL query string template
     * @return bool|array        Return array on success or false on failure
     */
    public function queryFirstRow($format)
    {
        $argv = func_get_args();
        $sql = call_user_func_array(array($this, 'buildSqlStr'), $argv);
        return $this->__doSelectQuery($format, $sql, 2);
    }

    /**
     * Query the first row of the result as associated array
     *
     * @param string $format    SQL query string template, can be empty,
     *                             just for Log printing when needed
     * @param string $sql        SQL query string
     * @return bool|array        Return array on success or false on failure
     */
    public function queryFirstRowEx($format, $sql)
    {
        return $this->__doSelectQuery($format, $sql, 2);
    }

    /**
     * Query the specified field value of the first result row
     * Caller should pass all the argument for format string following the $format parameter
     * example:
     * <code>
     * $ret = $db->querySpecifiedField('SELECT uname FROM tb WHERE uid=%d', $uid);
     * </code>
     *
     * @param string $format    SQL query string template
     * @return bool|string        The specified field value on success or false on failure
     */
    public function querySpecifiedField($format)
    {
        $argv = func_get_args();
        $sql = call_user_func_array(array($this, 'buildSqlStr'), $argv);
        return $this->__doSelectQuery($format, $sql, 3);
    }

    /**
     * Query the specified field value of the first result row
     *
     * @param string $format    SQL query string template, can be empty,
     *                             just for Log printing when needed
     * @param string $sql        SQL query string
     * @return bool|string        The specified field value on success or false on failure
     */
    public function querySpecifiedFieldEx($format, $sql)
    {
        return $this->__doSelectQuery($format, $sql, 3);
    }

    /**
     * Do update query according to the SQL query string template and its arguments
     * Caller should pass all the argument for format string following the $format parameter
     * example:
     * <code>
     * $ret = $db->doUpdateQuery('UPDATE tb SET uname=%s WHERE uid=%d', $uname, $uid);
     * </code>
     *
     * @param string $format    SQL query string template
     * @return bool    Return true on success or false on failure
     */
    public function doUpdateQuery($format)
    {
        $argv = func_get_args();
        $sql = call_user_func_array(array($this, 'buildSqlStr'), $argv);
        if (empty($sql)) {
            $this->__buildSqlStrError($format, 2);
            return false;
        }

        if (parent::doUpdateQuery($sql) === false) {
            $this->__sqlQueryError();
            return false;
        }
        return true;
    }

    /**
     *
     * @param string $format    SQL query string template, can be empty,
     *                             just for Log printing when needed
     * @param string $sql        SQL query string
     * @return bool    Return true on success or false on failure
     */
    public function doUpdateQueryEx($format, $sql)
    {
        if (empty($sql)) {
            $this->__buildSqlStrError($format, 2);
            return false;
        }

        if (parent::doUpdateQuery($sql) === false) {
            $this->__sqlQueryError();
            return false;
        }
        return true;
    }

    private function __doSelectQuery($format, $sql, $mode, $log_trace_depth = 1)
    {
        if (empty($sql)) {
            $this->__buildSqlStrError($format, $log_trace_depth + 1);
            return false;
        }

        switch ($mode) {
            case 1:    //select all rows
                $ret = parent::queryAllRows($sql);
                break;

            case 2:    //select first row(or select single row in the other word)
                $ret = parent::queryFirstRow($sql);
                break;

            case 3:    //select the specified field
                $ret = parent::querySpecifiedField($sql);
                break;

            default:
                $ret = false;
                break;
        }

        if ($ret === false) {
            $this->__sqlQueryError($log_trace_depth + 1);
            return false;
        }

        return $ret;
    }

    private function __errorMessage()
    {
        return 'errcode[' . $this->getErrno() . ']errmsg[' .
        $this->getErrmsg() . ']sql[' . $this->getSqlStr() . ']';
    }

    private function __buildSqlStrError($format, $log_trace_depth = 1)
    {
        //为了统一监控及错误查看方便直接打印到php.log里面
        $err_msg = "mysql[build sql str] format[$format] buildSqlStr failed";
        trigger_error($err_msg, E_USER_WARNING);
        CLog::warning($err_msg, 0, null, $log_trace_depth + 1);
    }

    private function __sqlQueryError($log_trace_depth = 1)
    {
        $errmsg = $this->__errorMessage();
        //CLog::selflog('monitor', $errmsg);
        if ($this->getErrno()) {
            trigger_error('mysql[sql execute failed] '.$errmsg, E_USER_WARNING);
        }
        CLog::notice($errmsg, 0, null, $log_trace_depth + 1);
    }

    public function autocommit($mode){
        $this->mysqli->autocommit($mode);
    }

    /**
     * 回滚
     * @return bool
     */
    public function rollback(){
        return $this->mysqli->rollback();
    }

    /**
     * commit
     * @return bool
     */
    public function commit(){
        return $this->mysqli->commit();
    }
}

