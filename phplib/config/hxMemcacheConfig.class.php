<?php

/**
 * Config for hxMemcache clusters
 *
 * @category    cache
 * @package        bdMemcache
 * @version        $Revision: 1.2 $
 */
class hxMemcacheConfig
{
    /**
     * 是否是长连接
     * @var bool
     */
    const PERSISTENT = false;

    /**
     * 连接超时时间，秒级
     * @var int
     */
    const TIMEOUT = 1;

    /**
     * 连接超时时间，毫秒级，优先级比TIMEOUT配置高
     * @var int
     */
    const TIMEOUT_MS = 100;

    /**
     * 健康检查的重试时间间隔，秒级
     * @var int
     */
    const RETRY_INTERVAL = 1;

    //memcached配置，两个机房的配置都写进去，通过CURRENT_CONF来指定使用哪个机房的配置
    static $arrMemCacheServer = array(
        'default' => array(
            'local' => array(
                array(
                    'host' => 'kdm.hxfilm.com',
                    'port' => 11211,
                    'weight' => 1,
                ),
             ),
            'jx' => array(
                    'host' => 'kdm.hxfilm.com',
                    'port' => 11211,
                    'weight' => 1,
             ),
        ),

    );
}
