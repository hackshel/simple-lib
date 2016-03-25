<?php
/**
 * @file IError.php
 * @brief 
 *  
 **/

/**
 * @brief   Passport IError接口
 */
interface Passport_IError {
    /**
     *@breif indicate server talk error
     */
    public function isError();
    /**
     *@breif Get Error Code.Function name is same with that in PHP-Exception.
     */
    public function getCode();
    /**
     *@breif Get Error Message.Function name is same with that in PHP-Exception.
     */
    public function getMessage();
}




/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
?>
