<?php
/**
 * @file Passport.php
 * @version 1.0.4.0
 * @brief 先申请权限，然后确定tpl, appid(session), aid(uinfo)
 *
 **/

/**
 * @brief    Passport 基础库对外接口类
 */
class Passport {
    protected static $_errno     = 0;
    protected static $_errmsg     = '';

    /**
     * @brief    [Passgate]根据uid获取用户信息
     * @param    var        $varUserID    用户UID[整型|整型数组]
     * @param    array    $arrFields    指定获取的字段
     * @return    用户不存在或交互失败返回false，否则返回关联数组
     * @note
    **/
    public static function getInfoByuid($varUserID, $arrFields) {
        $ins = Bd_Passport_Passgate::getInstance();
        $ret = $ins->getUserInfoByUid($varUserID, $arrFields);
        if ($ins->isError()) {
            self::$_errno    = $ins->getCode();
            self::$_errmsg    = $ins->getMessage();
            return false;
        } else {
            return $ret;
        }
    }

    /**
     * @brief    [Passgate]根据uname获取用户信息
     * @param    var        $varUserName    用户名[字符串|字符串数组]
     * @param    array    $arrFields        指定获取的字段
     * @return    用户不存在或交互失败返回false，否则返回关联数组
     * @note
    **/
    public static function getInfoByun($varUserName, $arrFields) {
        $ins = Bd_Passport_Passgate::getInstance();
        $ret = $ins->getUserInfoByUname($varUserName, $arrFields);
        if ($ins->isError()) {
            self::$_errno    = $ins->getCode();
            self::$_errmsg    = $ins->getMessage();
            return false;
        } else {
            return $ret;
        }
    }

    /**
     * [Passgate]根据uname获取uid
     * @param string|array $varUserName
     */
    public static function getInfoBySecureemail($secureemail) {
        $ins = Bd_Passport_Passgate::getInstance();
        $ret = $ins->getUidBySecureemail($secureemail);
        if ($ins->isError()) {
            self::$_errno    = $ins->getCode();
            self::$_errmsg    = $ins->getMessage();
            return false;
        } else {
            return $ret;
        }
    }

    /**
     * [Passgate]根据mobile获取uid
     * @param string $securemobile mobile信息
     * @return array|bool
     */
    public static function getInfoBySecureMobile($securemobile) {
        $ins = Bd_Passport_Passgate::getInstance();
        $ret = $ins->getUidBySecureMobile($securemobile);
        if ($ins->isError()) {
            self::$_errno    = $ins->getCode();
            self::$_errmsg    = $ins->getMessage();
            return false;
        } else {
            return $ret;
        }
    }


    /**
     * @brief    [兼容SAF函数][废弃中]
     * @param    NULL
     * @return    true
     * @note
     */
    public static function init($key = null, $product_key = null) {
        return true;
    }


    /**
     * @brief    [兼容SAF函数][Session]校验在线状态，获取会话数据
     * @param    bool   $quick_user ，支持快推账号的开关，
     * @param    string $hxuss      如果不传这从cookie中取
     * @param bool      $weak_hxuss 弱bduss登录
     * @param bool      $voice  声纹登录
     * @return    交互失败或$_COOKIE['HXUSS']不合法时，返回false;成功返回关联数组
     * @date      2015/02/26
     */
    public static function checkUserLogin( $hxuss = null ) {
        $hxuss = empty($hxuss) ? $_COOKIE['HXUSS'] : $hxuss;
        return empty($hxuss) ? false : self::getData( $hxuss );
    }

    /**
     * @brief    [Session]校验在线状态，获取会话数据
     * @brief    [Session]数据读取，从memcache 中读取
     * @param    string $hxuss $_COOKIE['HXUSS']
     * @return    用户不存在或交互失败返回false，否则返回关联数组
     */

    public static function getData($hxuss){
        CLog::warning( 'passport.php  session ===>' . $hxuss );
        $ins = Passport_Session::getInstance();
        $ret = $ins->memData( $hxuss );

        if ($ins->isError()) {
            self::$_errno    = $ins->getCode();
            self::$_errmsg    = $ins->getMessage();
            return false;
        } else {
            return $ret;
        }
    }


    public static function loginedSession( $user_info,$timestamp,$client='web' ,$device='', $os_version='' )
    {
        $ins = Passport_Session::getInstance();
        //CLog::warning('Passport loginedSession: client ===>['.$client.']' );
        $ret = $ins->login_v3( $user_info ,$timestamp ,0,$client , $device, $os_version );
        if( $ret == false ){
            return false;
        }

        return $ret;

    }


    public static function logoutSession( $hxuss, $client='web' )
    {
        $ins = Passport_Session::getInstance();
        $ret = $ins->logout_v2( $hxuss ,$client);
        return $ret ;
    }

    public static function passwordRest( $arrData )
    {
 
        $passwd = md5( $arrData['username'] . $arrData['password'] . PPSApiConfig::PASS_KEY_SLAT );
        $new_passwd = md5( $arrData['username'] . $arrData['new_password'] . PPSApiConfig::PASS_KEY_SLAT );
 


        $acctUserCore = AcctUsersCore::getInstance();
        $user_info = $acctUserCore->checkUser( $arrData['username'] , $passwd );
        if( false == $user_info ){
            return false;
        }
        if( $user_info['passwd'] == $new_passwd ){
            throw new DeveloperApiException( DeveloperErrorDescs::EC_REST_PASSWORD_SAME_PASS );
        }else{
            $chgStatus = $acctUserCore->changePassword( $arrData['username'],$new_passwd, $arrData['timestmp'] );
            if( false == $chgStatus ){
                return false;
            }else{
                return true;
            }
        }

       /*
        $ins = Passport_Session::getInstance();
        $ret = $ins->passwordReset();
        return $ret ;
        */
    }



    /**
     * @brief    [Session]修改会话数据
     * @param    string    $bduss    $_COOKIE['BDUSS']
     * @param    string    $gdata    公有数据(产品线无权修改，请传空字符串)
     * @param    string    $gmask    公有数据掩码(同上)
     * @param    string    $pdata    私有数据
     * @param    string    $pmask    私有数据掩码
     * @return    用户不存在或交互失败返回false，否则返回关联数组
     * @note
    **/
    public static function modData($bduss , $gdata , $gmask , $pdata , $pmask) {
        $ins = Bd_Passport_Session::getInstance();
        $ret = $ins->modData($bduss , $gdata , $gmask , $pdata , $pmask);
        if ($ins->isError()) {
            self::$_errno    = $ins->getCode();
            self::$_errmsg    = $ins->getMessage();
            return false;
        } else {
            return $ret;
        }
    }

    /**
     * @brief    [Session][统一安全认证]校验在线状态，获取会话数据
     * @param    string    $bduss    $_COOKIE['BDUSS']
     * @param    string    $stoken    产品线域名下的COOKIE['STOKEN']
     * @return    用户不存在或交互失败返回false，否则返回关联数组
     * @note
    **/
    public static function authGetData($bduss , $stoken){
        $ins = Bd_Passport_Session::getInstance();
        $ret = $ins->authGetData($bduss , $stoken);
        if ($ins->isError()) {
            self::$_errno    = $ins->getCode();
            self::$_errmsg    = $ins->getMessage();
            return false;
        } else {
            return $ret;
        }
    }

    /**
     * @brief [统一安全认证]前端接口的回调url
     * @param  string $url
     * @param  string $bduss
     * @return string
     */
    public static function getAuthUrl($url, $bduss = '') {
        $url    = urlencode($url);
        $tpl    = Bd_Passport_Inc::$conf['tpl'];
        $domain = PASSPORT_DOMAIN;
        $url    = "{$domain}/v2/?loga&tpl={$tpl}&jump=1&u=$url";
        if (!empty($bduss)) {
            $loga_secret_key = Bd_Passport_Inc::$conf['loga_secret_key'];
            Bd_Passport::getBdsToken($bduss, $loga_secret_key, $tpl, $logastoken);
            $url .= "&logastoken={$logastoken}";
        }
        return $url;
    }

    /**
     * @brief [统一安全认证]解密得到的auth
     * @param string $hash
     * @return string
     * @date 2012/03/29
     */
    public static function decodeAuth($hash) {
        $tpl = Bd_Passport_Inc::$conf['tpl'];
        $key = Bd_Passport_Inc::$conf['secret_key'];
        return Bd_Passport_STokenCrypt::url_decrypt($tpl, $key, $hash);
    }

    /**
     * @brief passport登陆url
     * @param  string $url
     * @return string
     * @date 2012/05/21
     */
    public static function getLoginUrl($url) {
        $url    = urlencode($url);
        $tpl    = Bd_Passport_Inc::$conf['tpl'];
        $domain = PASSPORT_DOMAIN;
        $url    = "{$domain}/v2/?login&tpl={$tpl}&jump=1&u=$url";
        return $url;
    }

    /**
     * @brief    [Session][统一安全认证]修改会话数据
     * @param    string    $bduss    $_COOKIE['BDUSS']
     * @param    string    $stoken    产品线域下的COOKIE['STOKEN']
     * @param    string    $gdata    公有数据(产品线无权修改，请传空字符串)
     * @param    string    $gmask    公有数据掩码(同上)
     * @param    string    $pdata    私有数据
     * @param    string    $pmask    私有数据掩码
     * @return    用户不存在或交互失败返回false，否则返回关联数组
     * @note
     * @date 2011/05/27 14:20:35
    **/
    public static function authModData($bduss , $stoken , $gdata , $gmask , $pdata , $pmask) {
        $ins = Bd_Passport_Session::getInstance();
        $ret = $ins->authModData($bduss , $stoken , $gdata , $gmask , $pdata , $pmask);
        if ($ins->isError()) {
            self::$_errno    = $ins->getCode();
            self::$_errmsg    = $ins->getMessage();
            return false;
        } else {
            return $ret;
        }
    }


    /**
     * @brief [不推荐使用][Util]从$_COOKIE['BDUSS']中计算UID和UNAME
     * @param string $bduss $_COOKIE['BDUSS']
     * @return array('uid'=> 5,'uname'=>'maling')
     * @date 2011/10/26 21:25:48
     * @note
     * 此函数不能用于校验会话登录状态
     */
    public static function getUserInfoFromCookie($bduss = null) {
        if (is_null($bduss)) {
            if (!isset($_COOKIE['BDUSS'])) {
                return false;
            }
            $bduss = $_COOKIE['BDUSS'];
        }
        return Bd_Passport_Util::decodeBduss($bduss);
    }



    /**
     * @brief    [Session]解析会话数据的公有数据字段
     * @param    string    $gdata    Session返回的公有数据字段(gdata)
     * @return    array
     * @date    2011/10/26 21:25:48
     * @note
     */
    public static function parseGData($gdata) {
        return Bd_Passport_Session::getInstance()->parseGData($gdata);
    }


    /**
     * @brief [不推荐使用][Session]获取一个数据为空的会话ID
     * @param    NULL
     * @return 用户不存在或交互失败返回false，否则返回关联数组
     * @note
    **/
    public static function getSid(){
        $ins = Bd_Passport_Session::getInstance();
        $ret = $ins->getSid();
        if ($ins->isError()) {
            self::$_errno    = $ins->getCode();
            self::$_errmsg    = $ins->getMessage();
            return false;
        } else {
            return $ret;
        }
    }


    /**
     * @brief    [废弃中][Session]根据长度初始化缓冲区，返回其对应的数据和掩码(均为空)
     * @param    array    &$arrData    空的数组变量
     * @param    int        $len        需要初始化的缓冲区的长度
     * @return    array    $arrData    = array('data'=>'', 'mask'=>'');
     * @date    2011/10/27 10:57:04
     * @note
     */
    public static function initDataBuf(&$arrData, $len) {
        $len = intval($len);
        $arrData = array(
            'data'    => str_repeat(chr(0) , $len),
            'mask'    => str_repeat(chr(0) , $len),
        );
    }


    /**
     * @brief    [推荐使用][Session]根据长度初始化缓冲区，返回其对应的数据和掩码(均为空)
     * @param    int        $len        需要初始化的缓冲区的长度
     * @return    array    $arrData    = array('data'=>'', 'mask'=>'');
     * @date    2011/10/27 10:57:04
     * @note
     */
    public static function initDataBufEx($len) {
        $len = intval($len);
        return array(
            'data'    => str_repeat(chr(0), $len),
            'mask'    => str_repeat(chr(0), $len),
        );
    }

    /**
     * @brief    [推荐使用][Session]给定数据初始化缓冲区，返回其对应的数据和掩码
     * @param    string    $strData    给定的字符串私有数据
     * @return    array    $arrData    = array('data'=>'', 'mask'=>'');
     * @date    2011/10/27 10:57:04
     * @note
     */
    public static function initDataBufWithData($strData) {
        $strData = strval($strData);
        $len = strlen($strData);
        return array(
            'data'    => $strData,
            'mask'    => str_repeat(chr(255), $len),
        );
    }

    /**
     * @brief    [Session]按字节修改缓冲区数据和掩码(修改会话数据使用)
     * @param    array    $arrData
     * @param    int        $byte        待修改的字节下标，从0开始
     * @param    int        $value        待修改的字节的值[0-255]
     * @return    array    $arrData    = array('data'=>'', 'mask'=>'');
     * @note    为了链式调用，引用传参并返回该数组。
     */
    public static function modDataBufByByte(&$arrData, $byte, $value) {
        if (!isset ($arrData['data']) || !isset($arrData['mask'])) {
            return false;
        }
        $byte = intval($byte);
        $len = strlen($arrData['data']);
        if ($byte >= $len) {
            return false;
        }

        $value = intval($value);
        if ($value > 255 || $value < 0) {
            return false;
        }

        $arrData['data'][$byte] = chr($value);
        $arrData['mask'][$byte] = chr(255);
        return $arrData;
    }

    /**
     * @brief    [Session]按Bit修改缓冲区数据和掩码(修改会话数据使用)
     * @param    array    $arrData
     * @param    int        $bit        待修改的Bit下标，从1开始
     * @param    int        $value        待修改的Bit的值[0,1]
     * @return    array    $arrData    = array('data'=>'', 'mask'=>'');
     * @note    为了链式调用，引用传参并返回该数组。
     */
    public static function modDataBufByBit(&$arrData , $bit , $value) {
        if (!isset ($arrData['data']) || !isset($arrData['mask'])) {
            return false;
        }
        $bit = intval($bit);
        $len = strlen($arrData['data']);
        if ($bit > $len * 8) {
            return false;
        }
        if (!in_array($value , array(0 , 1))) {
            return false;
        }
        $value = intval($value);

        $byte = intval(($bit - 1) / 8);
        $offset = ($bit - 1) % 8;

        $arrData['data'][$byte] = chr(ord($arrData['data'][$byte]) & (~(1 << $offset)) | ($value << $offset));
        $arrData['mask'][$byte] = chr(ord($arrData['mask'][$byte]) | (1 << $offset));
        return true;
    }




    /**
     * @brief    [Session]获取用户SESSION信息
     * @param    array    $arrUids    用户ID数组
     * @return    用户不存在或交互失败返回false，否则返回关联数组
     * @note
     * @date    2011/05/27 14:20:35
    **/
    public static function uidGetInfo($arrUids){
        $ins = Bd_Passport_Session::getInstance();
        $ret = $ins->uidGetInfo($arrUids);
        if ($ins->isError()) {
            self::$_errno    = $ins->getCode();
            self::$_errmsg    = $ins->getMessage();
            return false;
        } else {
            return $ret;
        }
    }


    /**
     * @brief    [Session]获取用户SESSION信息
     * @param    array    $arrUids    用户ID数组
     * @return    用户不存在或交互失败返回false，否则返回关联数组
     * @note
     * @date 2011/05/27 14:20:35
    **/
    public static function uidGetInfoStat($arrUids){
        $ins = Bd_Passport_Session::getInstance();
        $ret = $ins->uidGetInfoStat($arrUids);
        if ($ins->isError()) {
            self::$_errno    = $ins->getCode();
            self::$_errmsg    = $ins->getMessage();
            return false;
        } else {
            return $ret;
        }
    }


    /**
     * @brief    [Session]获取用户SESSION信息
     * @param    array    $arrUids    用户ID数组
     * @return    用户不存在或交互失败返回false，否则返回关联数组
     * @note
    **/
    public static function uidGetInfoTime($arrUids){
        $ins = Bd_Passport_Session::getInstance();
        $ret = $ins->uidGetInfoTime($arrUids);
        if ($ins->isError()) {
            self::$_errno    = $ins->getCode();
            self::$_errmsg    = $ins->getMessage();
            return false;
        } else {
            return $ret;
        }
    }

    /**
     * @brief    [Util]生成bdstoken
     * @param    string    $src    传入的md5串1，登陆用户为bduss，未登陆用户为baiduid。不允许为空
     * @param    string    $key    传入的md5串2,每个产品线使用的加密串，要求不小于16字节，并且包括数字，字母以及特殊字符。不允许为空。
     * @param    string    $srcex    传入的md5串3，扩展使用，由各产品线决定其内容。不使用必须传入null。
     * @param    string    $dst    成功返回时dst为md5串，失败时dst为错误信息
     * @return a bool   成功：true  失败: false
     * @version 1.0.0
    **/
    public static function getBdsToken($src, $key, $srcex, &$dst){
        return Passport_Util::getBdsToken($src, $key, $srcex, $dst);
    }

    /**
     * @brief    [Util]校验bdstoken
     * @param    string    $token    传入的待检查的token
     * @param    string    $src    传入的md5串1，已登陆用户为bduss,未登陆用户为baiduid
     * @param    string    $key    传入的md5串2，设置的密钥串， 详细参考getBdsToken的说明
     * @param    string  $srcex    传入的md5串3，扩展使用，详细参考getBdsToken的说明
     * @return    a bool or string  检查通过：true；检查错误：错误说明
     * @version 1.0.0
     *            token，src，key，srcex参数类型必须为string;
     *            srcex不作为计算md5参数时必须传入参数null;
     **/
    public static function checkBdsToken($token, $src, $key, $srcex){
        return Bd_Passport_Util::checkBdsToken($token, $src, $key, $srcex);
    }




    /**
     * @brief    获取错误码
     * @param    NULL
     * @return    int
     * @note
     */
    public static function getCode() {
        return self::$_errno;
    }

    /**
     * @brief    获取错误信息
     * @param    NULL
     * @return
     * @note
     */
    public static function getMessage() {
        return self::$_errmsg;
    }
}

?>
