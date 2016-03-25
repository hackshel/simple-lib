<?php
/**
 * Config for bdDBProxy clusters
 * 
 * @category    DB
 * @package        DBProxy
 * @version        $Revision: 1.2 $ 
 */
class DBProxyConfig
{
    /**
     * DBProxy集群编号定义
     * @var int
     */
    const DBPROXY_KAMINO_INDEX = 0;
    const DBPROXY_KAMINO_READONLY_INDEX = 1;

    const DBPROXY_ESSELSE_INDEX = 3;
    const DBPROXY_ESSELSE_READONLY_INDEX = 4;

    const DBPROXY_OFFICIAL_INDEX = 5;
    const DBPROXY_OFFICIAL_READONLY_INDEX = 6;


    /**
     * 数据库连接失败时的总重试次数(包括失败、超时等)
     * @var int
     */
    const RETRY_TIMES = 3;

    /**
     * 单个机房数据库连接失败时的重试次数(包括失败、超时等)
     * @var int
     */
    const RETRY_TIMES_PER_IDC = 2;

    /**
     * MySQL 链接超时时间（秒）
     * 用于设置 MYSQLI_OPT_CONNECT_TIMEOUT
     * @var int
     */
    const CONNECTION_TIMEOUT = 1;

    /**
     * 数据库库名与集群编号的映射关系表，说明每个数据库部署在哪个集群上
     * 每增加一个数据库时必须在这里增加一个映射记录，如果不增加映射记录，
     * 则默认认为该数据库部署在第一个集群上
     * @var array
     */
    static $arrDatabaseMap = array(
                'kamino'    => array(self::DBPROXY_KAMINO_INDEX, 'utf8'),
                'Esseles'   => array(self::DBPROXY_ESSELSE_INDEX, 'utf8'),
                'taris'    => array(self::DBPROXY_OFFICIAL_INDEX,'utf8'),
            );

    static $arrBackupDatabaseMap = array(
            'DBPROXY_KAMINO_READONLY_INDEX'     => array(self::DBPROXY_KAMINO_READONLY_INDEX, 'utf8'),
            'DBPROXY_ESSELSE_READONLY_INDEX'    => array(self::DBPROXY_ESSELSE_READONLY_INDEX, 'utf8'),
            'DBPROXY_OFFICIAL_READONLY_INDEX'   => array(self::DBPROXY_OFFICIAL_READONLY_INDEX,'utf8'),
    );

    /**
     * DBProxy集群的机器列表、访问集群时所用的用户名、密码、端口号、失败重试次数
     * @var array
     */
    static $arrDBProxyServer = array(
            self::DBPROXY_KAMINO_INDEX => array( //write db
                'username' => 'kmn_read',
                'password' => 'kamino',
                'port' => 3306,
                'local' => array(
                    '192.168.199.50',
                    ),
                ),
            self::DBPROXY_KAMINO_READONLY_INDEX => array(
                'username' => 'kmn_read',
                'password' => 'kamino',
                'port' => 3306,
                'local' => array(
                    '192.168.199.50',
                    ),
                ),


            self::DBPROXY_ESSELSE_INDEX => array(
                'username' => 'esseles_read',
                'password' => 'esseles',
                'port' => 3306,
                'local' => array(
                    '192.168.199.50',
                    ),
                ),


            self::DBPROXY_ESSELSE_READONLY_INDEX => array(
                'username' => 'esseles_read',
                'password' => 'esseles',
                'port' => 3306,
                'local' => array(
                    '192.168.199.50',
                    ),
                ),

            /** use for  leader vote ，领导审批用的库 */
            self::DBPROXY_OFFICIAL_INDEX => array(
                'username' => 'kmn_read',
                'password' => 'kamino',
                'port' => 3306,
                'local' => array(
                    '192.168.199.50',
                    ),
                ),

            );


}
