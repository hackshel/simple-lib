<?php
/**
 * @file Bd/Passport/Conf.php
 * @brief 
 *  
 **/

/**
 * @brief    Bd_Passport 配置库
 */
class Passport_Conf {
    protected static $_isInit = false;
    protected static $_conf = null;
    
    /**
     * @brief    获取配置项的值
     * @param    string    $strKey    配置项
     * @return    成功返回配置项；失败返回null
     * @author    fanmengzhe
     * @note
     */
    public static function getConf($strKey) {
        if (!self::$_isInit) {
            self::_initConf();
        }
        return isset(self::$_conf[$strKey]) ? self::$_conf[$strKey] : null;
    }

    /**
     * @brief    获取配置项的值
     * @param    string    $strKey        配置项
     * @param    string    $default    默认值
     * @return    成功返回配置项；失败返回默认值
     * @note
     */
    public static function getConfEx($strKey, $default) {
        if (!self::$_isInit) {
            self::_initConf();
        }
        return isset(self::$_conf[$strKey]) ? self::$_conf[$strKey] : $default;
    }

    /**
     * @brief    初始化passport config。判断运行时环境[ODP|PRO]；
     *            若运行在ODP 环境，使用Bd_Conf读取configure文件
     *            若运行在其他环境，使用Bd_Passport_Inc类(Inc.php)作为配置文件
     * @param    null
     * @return    null
     * @note
     */
    protected static function _initConf() {
        if (defined('IS_ODP') && true === IS_ODP) {
            self::$_conf = Bd_Conf::getConf('/passport/');
        } else {
            self::$_conf = Bd_Passport_Inc::$conf;
        }
        self::$_isInit = true;
    }
}



/* vim: set expandtab ts=4 sw=4 sts=4 tw=100 */
?>
