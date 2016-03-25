<?php


require_once(dirname(__FILE__).'/autoload.php');

/*
require_once(dirname(__FILE__).'/Passport.php');
require_once(PUBLIC_CONF_PATH .'/Bd_Passport_Inc.class.php');


require_once(PUBLIC_PATH.'/passport/SAPI/PPBase.class.php');
require_once(PUBLIC_PATH.'/passport/SAPI/PPSApi.class.php');
*/


class DevPassport {


    /**
     * @brief    [兼容SAF函数][Session]校验在线状态，获取会话数据
     * @internal param \新增快推账号支持字段quick_user $bool
     * @return    交互失败或$_COOKIE['HXUSS']不合法时，返回false;成功返回关联数组
     */
    public static function checkUserLogin( $hxuss = null ) {

        $ret = Passport::checkUserLogin( $hxuss );

        if (false === $ret) {
            return false;
        }


        return $ret;
    }


   /** 
    * Bdstoken 防止csrf攻击
    * 
    * 不防止恶意脚本的恶意提交，此行为请使用验证码！
    * 
    * @param string $key 用于生成bdstoken的加密key
    * @param string|null $src 用于生成bdstoken的cookie值
    * @return string
    */
    public static function getBdsToken($key, $src = null) {
        if (empty($src)) {
            //$src = isset($_COOKIE['HXUSS']) ? $_COOKIE['HXUSS'] : $_COOKIE['HXUID'];
            $src = isset($_COOKIE['HXUSS']) ? $_COOKIE['HXUSS'] : '';
        }
        $dst = '';
        Passport::getBdsToken($src, $key, null, $dst);
        return $dst;
    }


    /**
     * 登录或密码校验
     * 
     * 详细说明请查看
     *
     */

    public static function login($uname, $password, $login_type = 2, $isphone=false, $isEmail=false , $lang = null,
                            $verifycode = null, $vcodestr = null, $clientip = null ) {
        $res =  PPSApi::getInstance() -> login_v2($uname, $password, $login_type, $isphone, $isEmail,
                        $clientip, $verifycode, $vcodestr, $lang );
        return $res;
    }


    /**
    * passport 新建统一登陆业务逻辑
    **/

    public static function doLogin( $uname , $password , $timestamp , $client='web', $device='', $os_version='' ){
        //decode  传输的用户名密码
        //加密用户信息，用户密码
        //检查时间段，是否是在这时间内
        //生成hxuss
        //
        CLog::warning( 'dologin action -> username -==> ['.$uname .'] and passport -=-->['.$password .']');
        CLog::warning('timestamp --- > ' . $timestamp );
        CLog::warning('device --- > ' . $device );
        CLog::warning('os version --- > ' . $os_version );
        $arrLoginUser = self::Login( strval($uname) , strval($password) );
        //CLog::warning( 'log====>' . json_encode( $arrLoginUser ));
        if ( $arrLoginUser == false ) {
            return false;
        }else{
            // return array for user infos;
            $arrLoginUser['is_login'] = true ;
            $arrLoginUser['is_authlogin'] = true;
            CLog::warning( 'devPassport doLogin class : client ==> ['.$client.']');
            $hxuss = Passport::loginedSession( $arrLoginUser,$timestamp , $client, $device , $os_version );
            CLog::warning('debug ----->'.$hxuss);
            CLog::warning( 'do login return hxuss ===>['.$hxuss.']' );
        }

        if( $client == 'web'){
            CLog::warning( 'do set cookie for web ===> '.$hxuss );

            /**
            * 增加判断根域名，用来区分hxfilm.com 以及huayingjuhe.com
            * 方便增加两套passport 系统同时使用,
            */
            if( strstr($_SERVER['HTTP_HOST'] , CommonConfig::DOMAIN ) == CommonConfig::DOMAIN ){
                setcookie("HXUSS", $hxuss , time()+intval(CommonConfig::USER_LOGIN_TIMEOUT) , '/' , CommonConfig::DOMAIN );
            }else if( strstr($_SERVER['HTTP_HOST'] , CommonConfig::SEC_DOMAIN ) == CommonConfig::SEC_DOMAIN){
                setcookie("HXUSS", $hxuss , time()+intval(CommonConfig::USER_LOGIN_TIMEOUT) , '/' , CommonConfig::SEC_DOMAIN );
            }
            return true;
        }else{
            CLog::warning( 'do return cookie for mobie ===> ' . $hxuss );
            return array( 'state' => true , 'hxuss' => $hxuss );
        }

    }

    /**
    ** passport 登出
    **/

    public static function doLogout( $hxuss = null,$client='web' ){
        if( !isset( $hxuss ) ){
            $hxuss = $_COOKIE['HXUSS'];
        }
        if( empty( $hxuss ) ){
            return false;
        }
        CLog::warning('logout hxuss is ====>'.$hxuss);
        if( Passport::logoutSession( $hxuss ,$client )){
            if( $client != 'mobile'){
                /**
                * 增加判断根域名，用来区分hxfilm.com 以及huayingjuhe.com
                * 方便增加两套passport 系统同时使用,
                */
                if( strstr($_SERVER['HTTP_HOST'] , CommonConfig::DOMAIN ) == CommonConfig::DOMAIN ){
                    setcookie("HXUSS", $hxuss , time()-1 , '/' , CommonConfig::DOMAIN );
                }else if( strstr($_SERVER['HTTP_HOST'] , CommonConfig::SEC_DOMAIN ) == CommonConfig::SEC_DOMAIN){
                    setcookie("HXUSS", $hxuss , time()-1 , '/' , CommonConfig::SEC_DOMAIN );
                }

            }
        }else{
            return false;
        }

        return true;
    }


    public static function passwordReset( $arrData )
    {
   
        if( empty( $arrData )){
            return false;
        }
        if( Passport::passwordRest( $arrData ) ){
            return true;
        }else{
            return false;
        }




    }


}
