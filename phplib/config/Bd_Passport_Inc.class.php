<?php

/**
 * @file Bd/Passport/Inc.php
 * @date 2015/05/23 21:43:49
 * @brief
 * 线上passport配置文件
 **/

/**
 * @brief    Bd_Passport 非ODP环境的配置文件
 * @todo    配置文件详解
 */
class Bd_Passport_Inc {

    public static $conf = array (
        /**     using in session         */
        'apid'        => 0x0448,
        'is_bae'    => 0,
        'tpl'        => 'dev',
        'secret_key'=> '%Agde*fdf',
        /**     using in loga        */
        'loga_secret_key' => 't5tf$56guhk*u5',
        /**     using in passgate        */
        'app_user'    => 'developer',
        'app_passwd'=> 'developer',
        /**     using in pusrinfo        */
        'aid'        => 88,
        'engine'    => 'socket',
        /**        using in engine-socket     */
        /**        格式仿照galileo资源定位  */
        'server'    => array (
            'cur_idc'    => CURRENT_CONF,
            'session'    => array (
                'service_port'        => 9081,
                'service_conn_type'    =>    0,
                'service_ctimeout'    => 1000,
                'service_rtimeout'    => 1000,
                'service_wtimeout'    => 1000,
                // new bvs IP
                'jx'                => array (
                    array ('ip'        => '10.26.7.72',),
                    array ('ip'        => '10.36.253.87',),
                ),
                'tc'        => array (
                    array ('ip'        => '10.36.7.65',),
                    array ('ip'        => '10.81.211.104',),
                ),
            ),
            'passgate'    => array(
                'service_port'        => 16000,
                'service_conn_type'    =>    0,
                'service_ctimeout'    => 1000,
                'service_rtimeout'    => 1000,
                'service_wtimeout'    => 1000,
                'jx'                => array (
                    array ('ip'        => '10.36.7.29',),
                    array ('ip'        => '10.36.253.86',),
                ),
                'tc'        => array (
                    array ('ip'        => '10.26.7.28',),
                    array ('ip'        => '10.81.211.101',),
                ),
            ),
        ),
    );
}

/**
 * Config for Passport services
 *
 * @package    Passport
 * @version    $Revision: 1.1 $
 */
class PPSApiConfig
{
    const PP_APPID = 1;
    const PP_TPL = 'developer';
    const PP_KEY = '97528e477e0a1e7a5e008c2f47e201eb';
    const PP_PWD = '763fe6753f482ff2';
    const PASS_KEY_SLAT = '!@#$%*^';

    /**
     * 客户端唯一识别码，有反作弊需求的产品线需要配置
     */
    const PP_CLIENTID    = '';

    /**
     * 交互失败重试次数
     */
    const RETRY_TIMES = 3;

    /**
     * 超时设置,单位ms
     */
    const CONNECT_TIMEOUT = 1000;
    const TIMEOUT = 1000;


}
