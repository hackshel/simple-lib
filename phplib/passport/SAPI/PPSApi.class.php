<?php

require_once(dirname(__FILE__) . '/PPBase.class.php');

/**
 * Passport SAPI interface
 * 注意，只保证login接口可用，如需使用其余接口上线前请注意验证
 *
 * @package        Passport
 * @modify        支持返回英文错误码, 
 * @modify        将login接口迁移到v2版SAPIi 20150525 
 * @version        $Revision: 1.2 $
 */
class PPSApi extends PPBase
{
    /**
     * 密码强弱级别
     */
    const PASSWD_NORMAL        = 0;
    const PASSWD_WEAK        = 1;
    const PASSWD_SO_WEAK    = 2;

    /**
     * 错误码定义
     */
    const PP_EC_SUCCESS        = 0;
    const PP_EC_PARAM        = -1;
    const PP_EC_SIGNATURE    = -2;
    const PP_EC_TPL_APPID    = -3;
    const PP_EC_INVALID_IP    = -4;
    const PP_EC_EXPIRED_CERT = -5;
    const PP_EC_INVALID_CERT_ID = -6;
    const PP_EC_DEPRECATED_INTERFACE = 140008;
    const PP_EC_UNKNOWN        = -9999;

    const PP_EC_INVALID_UNAME        = 1;
    const PP_EC_UNAME_NOT_EXIST        = 2;
    const PP_EC_INVALID_DPASS       = 3;
    const PP_EC_INVALID_PASSWD        = 4;
    const PP_EC_TOO_MANY_LOGIN_FAILUARE  =5;
    const PP_EC_CAPTCHA_NOT_MATCH    = 6;
    const PP_EC_NEED_PASSWORD       = 9;
    const PP_EC_COULDNT_LOGIN        = 16;
    const PP_EC_LOGIN_PROTECT       = 17;
    const PP_EC_TOO_MANY_LOGIN        = 20;
    const PP_EC_EMPTY_PHONE         = 257;
    const PP_EC_INVALID_PASSWD_CHAR    = 120013;
    const PP_EC_NEED_ACTIVATE        = 110024;
    const PP_EC_NEED_CAPTCHA        = 257;

    /**
     * 半账号相关错误码
     */
    const PP_EC_INCOM_SUCCESS = 110000;
    const PP_EC_INCOM_INVALID_IP = 220001;
    const PP_EC_INCOM_EMPTY_USERID = 220002;
    const PP_EC_INCOM_INVALID_USERID = 220003;

    /**
     * 团购APP接入支付宝账号,绑定手机号并下发短信
     */
    const PP_EC_BIND_SUCCESS = 140001; //成功
    const PP_EC_BIND_NOT_PASS_USER = 400014; //非账号用户
    const PP_EC_BIND_EMPTY_PHONE = 200005;// 手机号为空
    const PP_EC_BIND_INVALID_PHONE = 230051; //手机号格式错误
    const PP_EC_BIND_USED_PHONE = 400003;// 手机号已经使用
    const PP_EC_USER_CONFLICT = 400401; //登陆合并时，手机号用户名同时满足登陆要求

    const PP_EC_DEFAULT                = 'default';

    protected static $error_descs = array(
        'zh_cn'    =>    array(
            self::PP_EC_SUCCESS        => '成功',
            self::PP_EC_PARAM        => '接口参数错误',
            self::PP_EC_SIGNATURE    => '签名错误',
            self::PP_EC_TPL_APPID    => '未找到的tpl+appid组合',
            self::PP_EC_INVALID_IP    => '访问方IP未授权',
            self::PP_EC_EXPIRED_CERT => '证书已失效',
            self::PP_EC_INVALID_CERT_ID => '指定的cert_id不存在',
            self::PP_EC_DEPRECATED_INTERFACE => '接口版本太老，需要升级至新版接口',
            self::PP_EC_UNKNOWN      => '登陆时发生未知错误，请重新输入',
            self::PP_EC_LOGIN_PROTECT => '您已开启登陆保护，请按照短信提示操作',

            self::PP_EC_INVALID_UNAME        => '用户名格式错误',
            self::PP_EC_UNAME_NOT_EXIST        => '用户不存在',
            self::PP_EC_INVALID_DPASS       => '动态密码错误',
            self::PP_EC_INVALID_PASSWD        => '登录密码错误',
            self::PP_EC_CAPTCHA_NOT_MATCH    => '验证码不匹配，请重新输入验证码',
            self::PP_EC_NEED_PASSWORD       => '密码输入为空',
            self::PP_EC_TOO_MANY_LOGIN_FAILUARE => '您的登陆错误次数过多',
            self::PP_EC_EMPTY_PHONE         => '手机号为空',
            self::PP_EC_COULDNT_LOGIN        => '对不起，您现在无法登陆(或注册时发生未知错误)',
            self::PP_EC_TOO_MANY_LOGIN        => '对不起，您的账号多点登陆超过上限',
            self::PP_EC_NEED_ACTIVATE        => '对不起，您的帐号还未激活',
            self::PP_EC_INVALID_PASSWD_CHAR    => '登陆密码格式错误',
            self::PP_EC_NEED_CAPTCHA        => '请输入验证码',
            self::PP_EC_DEFAULT                => '登陆时发生未知错误，请稍后重新输入',
            self::PP_EC_USER_CONFLICT       => '请选择使用手机号或用户名登陆',
        ),
        'en_us'    =>    array(
            self::PP_EC_SUCCESS        => 'Login success',
            self::PP_EC_PARAM        => 'Service temporarily unavailable, please retry later',
            self::PP_EC_SIGNATURE    => 'Service temporarily unavailable, please retry later',
            self::PP_EC_TPL_APPID    => 'Service temporarily unavailable, please retry later',
            self::PP_EC_INVALID_IP    => 'Service temporarily unavailable, please retry later',
            self::PP_EC_EXPIRED_CERT => 'Service temporarily unavailable, please retry later',
            self::PP_EC_INVALID_CERT_ID => 'Service temporarily unavailable, please retry later',
            self::PP_EC_DEPRECATED_INTERFACE => 'Service temporarily unavailable, please retry later',
            self::PP_EC_UNKNOWN      => 'Service temporarily unavailable, please retry later',

            self::PP_EC_INVALID_UNAME        => 'Invalid username or password',
            self::PP_EC_UNAME_NOT_EXIST        => 'Invalid username or password',
            self::PP_EC_INVALID_DPASS       => 'Invalid username or password',
            self::PP_EC_INVALID_PASSWD        => 'Invalid username or password',
            self::PP_EC_CAPTCHA_NOT_MATCH    => 'Invalid verification code',
            self::PP_EC_NEED_PASSWORD       => 'Invalid username or password',
            self::PP_EC_TOO_MANY_LOGIN_FAILUARE => 'Too many illegal login attempt',
            self::PP_EC_EMPTY_PHONE         => 'Invalid cellphone number',
            self::PP_EC_COULDNT_LOGIN        => 'Your account is locked',
            self::PP_EC_TOO_MANY_LOGIN        => 'Multiple login limit reached',
            self::PP_EC_NEED_ACTIVATE        => 'Please activate your account via email/sms',
            self::PP_EC_INVALID_PASSWD_CHAR    => 'Invalid username or password',
            self::PP_EC_NEED_CAPTCHA        => 'Please enter the verification code',
            self::PP_EC_DEFAULT                => 'Service temporarily unavailable, please retry later',
            self::PP_EC_USER_CONFLICT       => 'Please choose username or phone login',
        ),

        self::PP_EC_INCOM_SUCCESS => '成功',
        self::PP_EC_INCOM_INVALID_IP => 'IP没有授权',
        self::PP_EC_INCOM_EMPTY_USERID => 'userid为空',
        self::PP_EC_INCOM_INVALID_USERID => '非半账号userid',

        self::PP_EC_BIND_EMPTY_PHONE => 'empty phone number ',
        self::PP_EC_BIND_INVALID_PHONE => 'invalid phone number',
        self::PP_EC_BIND_USED_PHONE =>'already used phone number',
        self::PP_EC_BIND_SUCCESS =>'success',
        self::PP_EC_BIND_NOT_PASS_USER => 'invalid passport user',
    );

    protected static $instance = array();

    /**
     * 错误信息的语言版本，'en_us'为英文，'zh_cn'为中文
     */
    protected $lang;

    /**
     * @return PPSApi
     */
    public static function getInstance($lang = NULL)
    {
        $lang = (isset($lang) && strtolower($lang) == 'en_us') ? 'en_us' : 'zh_cn';
        if (!isset(self::$instance[$lang])) {
            self::$instance[$lang] = new PPSApi($lang);
        }
        return self::$instance[$lang];
    }

    public function __construct($lang)
    {
        $this->lang = (isset($lang) && strtolower($lang) == 'en_us') ? 'en_us' : 'zh_cn';
        parent::__construct();
    }

    /**
     * 用于团购APP接入支付宝账号，暗绑用户绑定手机号进行正常话(只对暗绑用户有效)，成功后下发短信密码给用户，
     * 该api目前只用于团购项目，如有其他需要，请先和pass沟通
     */
    public function bindingUserSMS($userid,$mobile){
        if(empty($userid)||empty($mobile)){
            return false;
        }
        $params = array(
            'userid' => $userid,
            'phone'  => $mobile,
        );
        return $this->call_method('/v2/sapi/bindingsms', self::HTTP_POST, $params);
    }

    /**
     *
     * pass 敏感信息接口，用于账号接入在进行明绑、暗绑时通知pass敏感信息
     * @param int $userid
     * @param string $os_uname
     * @param string $ostype_name 第三方平台对应的名称（feixin=>16,weibo=>2,qq=>15,tqq=>4,renren=>1）
     * @param int $bind_act 1代表绑定操作，0代表解绑操作
     * @param string $clientip
     * @return array|bool
     */
    public function accountSensitiveInfo($userid,$os_uname,$ostype_name,$bind_act,$clientip=''){
        //qq登录用户的第三方用户名可能为空，不做判断了
        if(empty($userid)||empty($ostype_name)){
            return false;
        }
        if(empty($clientip)){
            $clientip = CLog::getClientIP();
        }
        $params = array(
            'userid' => $userid,
            'sid'   =>1,
            'key' => md5('135aa1d49659cd476a0fee949c597e8ae'),
            'other_name'=>$os_uname,
            'clientip' =>$clientip,
            'type' => $ostype_name,
            'ctype'=>$bind_act,
        );
        return $this->call_method("/v2/?accountassociation",self::HTTP_POST,$params);
    }

    /**
     * 在账号互通过程中，将第三方验证过的邮箱绑定给pass,该接口用于第三方账号接入中
     * @param string $email
     * @param string $bduss
     * @param string $clientip
     * @param string $from
     * @return array
     */
    public function bindEmailInfo($email,$bduss,$clientip,$from=''){
        $params = array(
            'email' =>  $email,
            'bduss' =>  $bduss,
            'clientip'  =>  $clientip,
        );
        !empty($from) && $params['from'] = $from;
        return $this->call_method("/v2/?regpcsbindinfo",self::HTTP_POST,$params);
    }

    /**
     *
     * 将第三方验证过的手机号绑定改pass用户，
     * @param string $mobile
     * @param string $bduss
     * @param string $clientip
     * @param string $from
     * @return array
     */
    public function bindMobileInfo($mobile,$bduss,$clientip,$from=''){
        $params = array(
            'phone' =>  $mobile,
            'bduss' =>  $bduss,
            'clientip'  =>  $clientip,
        );
        !empty($from) && $params['from'] = $from;
        return $this->call_method("/v2/?regpcsbindinfo",self::HTTP_POST,$params);
    }

    /**
     *
     * 选择绑定手机号和者email信息
     * @param string $mobile
     * @param string $email
     * @param string $bduss
     * @param string $clientip
     * @param string $from
     * @return array
     */
    public function bindPcsUserInfo($mobile,$email,$bduss,$clientip,$from=''){
        $params = array(
            'phone' =>  $mobile,
            'email' => $email,
            'bduss' =>  $bduss,
            'clientip'  =>  $clientip,
        );
        !empty($from) && $params['from'] = $from;
        return $this->call_method("/v2/?regpcsbindinfo",self::HTTP_POST,$params);
    }


    /**
     * 判断用户名、密码是否匹配
     *
     * @param string $uname        用户名
     * @param string $password    密码
     * @param bool $isphone        是否手机号登陆, 0:用户名或邮箱登陆, 1:手机登录
     * @param string $clientip    客户端IP，根据反作弊策略和接口发起位置可选
     * @return array|false    匹配成功则返回数组，否则返回false
     */
    public function login_verfy($uname, $password, $isphone = false, $clientip = null)
    {
        $result = $this->login($uname, $password, 1, $isphone, $clientip);
        if ($result === false || $result['errno'] != self::PP_EC_SUCCESS) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 登录接口
     *
     * @param string $uname        用户名
     * @param string $password    密码明文,目前尚不支持通过证书加密密码的方式，但SAPI本身是支持的
     * @param int $login_type    登录行为类型 , 1:仅校验密码, 2:普通登录, 3:持久登录（记住登录状态）
     * @param bool $isphone        是否手机号登陆, false:用户名或邮箱登陆, true:手机登录
     * @param string $clientip    客户端IP，根据反作弊策略和接口发起位置可选
     * @param string $verifycode    用户输入的验证码内容，当要求验证码时必选
     * @param string $vcodestr    验证码凭据key，当要求验证码时必选
     * @param bool $isdpass     是否是动态密码登录。使用短信动态密码登录时， 需要和$isphone同时为true
     * @param bool $loginmerge  是否是用户名、邮箱、手机号整合登陆
     * @param string $clientid    客户端唯一标识码
     * @return array|false        登录失败返回false（除了除非验证码策略），否则返回如下数组:
     * array(
     *  'errno' => 错误号
     *     'needvcode' => 是否需要验证码, 值枚举：1|0
     *     'vcodestr' => 验证码凭据key
     *  'uid' => 用户id, 仅当登录成功时返回
     *  'uname' => 用户名, 仅当登录成功时返回
     *  'bduss' => bduss, 仅当登录成功且非验证时返回
     *  'ptoken' => ptoken, 仅当登录成功且非验证时返回
     *  'stoken' => stoken, 仅当登录成功且非验证时返回
     *  'weakpass' => 是否弱密码, 0-正常，1-弱密码，2-极弱密码
     *  )
     */
    public function login($uname, $password, $login_type = 2, $isphone = false,
                         $quick_user = false, $clientip = null, $verifycode = null,
                         $vcodestr = null, $isdpass = false, $loginmerge = false, $clientid = '')
    {
        if (empty($clientip)){
            $clientip = CLog::getClientIP();
        }
        $params = array(
            'username' => $uname,
            'password' => base64_encode($password),
            'crypttype' => 1, //表示明文传递
            'login_type' => $login_type,
            'isphone' => intval($isphone),
            'isdpass' => intval($isdpass),
            //反作弊策略
            'clientid' => $clientid,
            'clientip' => $clientip,
            //验证码策略
            'verifycode' => $verifycode,
            'vcodestr' => $vcodestr,
            //编码格式
            'ie'    => 'utf8',
        );
        //为手游快推提供服务，默认不传这个字段。
        $quick_user && $params['quick_user'] = 1;
        $loginmerge && $params['loginmerge'] = 'true';
        return $this->call_method('/v2/sapi/login', self::HTTP_POST, $params);
    }

    /**
    * @return user_info ，如果失败，返回false ，否则，返回用户信息数组
    **/

    public function login_v2($uname, $password, $login_type, $isphone, $isEmail,
                            $clientip, $verifycode, $vcodestr, $isdpass    )
    {
        //hash user password
        $passwd = md5( $uname . $password . PPSApiConfig::PASS_KEY_SLAT );

        /*
        print_r( $uname ."\n");
        print_r($password."\n" );

        print_r( $passwd ."\n" );

        print_r( PPSApiConfig::PASS_KEY_SLAT ."\n");
        */

        $acctUserCore = AcctUsersCore::getInstance();
        $user_info = $acctUserCore->checkUser( $uname , $passwd );
        unset($user_info['passwd'] );

        if( $user_info != false ){
            /** 用户权限关联信息 **/
            $acctUserRightRelCore = AcctUserRightRelCore::getInstance();
            $user_right = $acctUserRightRelCore->getUserRights( $user_info['id'] );

            /** 用户组关联信息 **/
            $acctUserGroupRelCore = AcctUserGroupRelCore::getInstance();
            $userGroupRel = $acctUserGroupRelCore->getUserGroups( $user_info['id'] );

            /**用户和院线关联关系
            * 返回数组或者返回 fasle
            **/
            $acctUserChainRelCore = AcctUserChainRelCore::getInstance();
            $userChainRel = $acctUserChainRelCore->getUserChainRelByID( $user_info['id'] );

            if( false == $userGroupRel ){
                exit;
            }else{
                /** 获得group_id  数组 **/
                $group_ids = array();
                foreach( $userGroupRel as $rel){
                    if( !in_array( $rel['group_id'] , $group_ids) ){
                        array_push( $group_ids , $rel['group_id'] );
                    }
                }

                /** 用户组信息 **/
                $acctUserGroupCore = AcctUserGroupCore::getInstance();
                $user_groups = $acctUserGroupCore->getGroups( $group_ids  );


                /** 组权限链接关系**/
                $acctRightGroupRelCore = AcctRightGroupRelCore::getInstance();
                $group_right = $acctRightGroupRelCore->getGroupRight( $group_ids );

                $right_ids = array();
                if( $user_right != false ){
                    foreach( $user_right as $ur ){
                        if( !in_array( $ur['right_id'] , $right_ids ) ){
                            array_push ( $right_ids , $ur['right_id'] );
                        }
                    }
                }
                if( $group_right != false){
                    foreach( $group_right as $gr ){
                        if( !in_array( $gr['right_id'] , $right_ids ) ){
                            array_push ( $right_ids , $gr['right_id'] );
                        }
                    }
                }

                $acctRightsCore = AcctRightsCore::getInstance();
                $right_info = $acctRightsCore->getRights( $right_ids );
                $user_info['rights'] = $right_info;
                $user_info['groups'] = $user_groups;
                $user_info['group_ids'] = $group_ids ;
                $user_info['right_ids'] = $right_ids;
                $user_info['chains'] = $userChainRel;
            }
        }

        return $user_info;

    }




    /**
     * 给用户的手机发送短信动态密码
     *
     * @param string $uname 手机号
     * @param string $clientip    客户端IP，根据反作弊策略和接口发起位置可选
     * @return array|false        登录失败返回false（除了除非验证码策略），否则返回如下数组:
     * array(
     *  'errno' => 错误号
     *  )
     */
    public function getdpass($uname, $clientip = null)
    {
        if (empty($clientip)){
            $clientip = CLog::getClientIP();
        }
        $params = array(
            'username' => $uname,
            //反作弊策略
            'clientid' => PPSApiConfig::PP_CLIENTID,
            'clientip' => $clientip,
        );
        return $this->call_method('/v2/sapi/getdpass', self::HTTP_POST, $params);
    }

    /**
     * 无密码登录接口
     * @author weixiaozhan(weixiaozhan@baidu.com) 2013/03/26
     * @param int $uid passport的用户id
     * @param string $clientip 客户端IP，根据反作弊策略和接口发起位置可选
     * @param bool $memlogin
     * @param int $usersource 第三方账号登陆向pass传递的第三方类型参数
     * @param string $srctpl 源产品线tpl
     * @param string $subpro 产品线的渠道号
     * @return array|bool        登录失败返回false（除了除非验证码策略），否则返回如下数组:
     * array(
     *  'errno' => 错误号
     *  'errmsg' => '成功',
     *  'uid' => 用户id, 仅当登录成功时返回
     *  'uname' => 用户名, 仅当登录成功时返回
     *  'bduss' => bduss, 仅当登录成功且非验证时返回
     *  'ptoken' => ptoken, 仅当登录成功且非验证时返回
     *  'stoken' => stoken, 仅当登录成功且非验证时返回
     *  )
     */
    public function nopasslogin($uid, $clientip = null,$memlogin=false,$usersource=0,$srctpl=null,$subpro=null)
    {
        $params = array(
            'userid' => $uid,
            //反作弊策略
            'clientid' => PPSApiConfig::PP_CLIENTID,
            'clientip' => $clientip,
            'memlogin' => $memlogin?1:0,
        );
        !empty($usersource)&&$params['usersource'] = $usersource;
        !empty($srctpl) && $params['srctpl'] = $srctpl;
        !empty($subpro) && $params['subpro'] = $subpro;
        return $this->call_method('/sapi/nopasslogin', self::HTTP_POST, $params);
    }

    /**
     * 设置session公有位接口
     * 前面有怪兽，迁移到v2接口之后未验证
     *
     * @param int $uid        passport的用户id
     * @param string $clientip    客户端IP，根据反作弊策略和接口发起位置可选
     * @return array|false        登录失败返回false（除了除非验证码策略），否则返回如下数组:
     * array(
     *  'errno' => 错误号
     *  'uid' => 用户id, 仅当登录成功时返回
     *  'uname' => 用户名, 仅当登录成功时返回
     *  'bduss' => bduss, 仅当登录成功且非验证时返回
     *  'ptoken' => ptoken, 仅当登录成功且非验证时返回
     *  'stoken' => stoken, 仅当登录成功且非验证时返回
     *  )
     */
    public function setgdata($uid, $clientip = null)
    {
        $params = array(
            'userid' => $uid,
            //反作弊策略
            'clientid' => PPSApiConfig::PP_CLIENTID,
            'clientip' => $clientip
        );

        return $this->call_method('/sapi/nopasslogin', self::HTTP_POST, $params);
    }


    /**
     * 使用bduss设置session共有位
     * @author weixiaozhan(weixiaozhan@baidu.com) 2013/03/26
     * @param string $bduss 百度账号BDUSS
     * @param int $gdata_byte 设置的字节数
     * @param int $gdata_val 设置的值
     * @return array | false 请求接口正确返回如下数组，否则返回false
     * array (
       *   'errno' => '0',
       *   'errmsg' => '成功',
     * )
     */
    public function setgdataByBduss($bduss,$gdata_byte,$gdata_val)
    {
        $params = array(
            'pwd' => PPSApiConfig::PP_PWD,
            'gdatapos' => $gdata_byte,
            'gdataval' => $gdata_val,
            'bduss' => $bduss,
            'logid' => CLog::logId(),
        );
        return $this->call_method('/sapi/setgdata',self::HTTP_GET,$params);
    }

    /**
     * 退出登录接口
     *
     * @param string $bduss        BDUSS cookie值
     * @param string $stoken    STOKEN cookie值，支持统一认证的产品应该传递该参数
     * @param string $clientip    客户端IP，根据反作弊策略和接口发起位置可选
     * @param string $verifycode    用户输入的验证码内容，当要求验证码时必选
     * @param string $vcodestr    验证码凭据key，当要求验证码时必选
     * @return array|false    退出失败返回false，否则返回数组:
     *     array(
     *         'uid' => 用户uid,仅退出成功时返回
     *         'uname' => 用户名,仅退出成功时返回
     *     )
     */
    public function logout($bduss, $stoken = null, $clientip = null,
                           $verifycode = null, $vcodestr = null)
    {
        if (empty($bduss)) {
            return false;
        }

        if (empty($clientip)){
            $clientip = CLog::getClientIP();
        }

        $params = array(
            'bduss' => $bduss,
            'bdstoken' => md5($bduss . PPSApiConfig::PP_KEY),
            'stoken' => empty($stoken) ? 'reserved' : $stoken,
            'ptoken' => 'reserved',
            //反作弊策略
            'clientid' => PPSApiConfig::PP_CLIENTID,
            'clientip' => $clientip,
            //验证码策略
            'verifycode' => $verifycode,
            'vcodestr' => $vcodestr,
        );

        return $this->call_method('/sapi/logout', self::HTTP_POST, $params);
    }

    /**
     * 弱密码检查接口
     * 前面有怪兽，迁移到v2接口之后未验证
     *
     * @param string $uname        用户名
     * @param string $password    密码
     * @return int|false    请求失败返回false，否则返回整数，0-正常，1-弱密码，2-极弱密码
     */
    public function weakpasscheck($uname, $password)
    {
        $params = array(
            'username' => $uname,
            'password' => base64_encode($password),
            'crypttype' => 1, //表示明文传递
        );
        $result = $this->call_method('/sapi/weakpasscheck', self::HTTP_POST, $params);
        if ($result && isset($result['result'])) {
            return intval($result['result']);
        }
        return false;
    }

    /**
     * 用户名检查接口
     * 前面有怪兽，迁移到v2接口之后未验证
     *
     * @param string $uname        用户名
     * @param int     $type        检查类型，1-uname，2-phone，3-email
     * @param string $clientip    客户端IP，根据反作弊策略和接口发起位置可选
     * @param string $verifycode    用户输入的验证码内容，当要求验证码时必选
     * @param string $vcodestr    验证码凭据key，当要求验证码时必选
     * @return int|false    请求失败返回false，否则返回整数，0-正常，1-已存在，2-违禁用户名，3-格式不正确
     */
    public function check_username($uname, $type = 1, $clientip = null)
    {
        $params = array(
            'username' => $uname,
            'type' => $type,
            'clientid' => PPSApiConfig::PP_CLIENTID,
            'clientip' => $clientip,
        );
        $result = $this->call_method('/sapi/ucheck', self::HTTP_POST, $params);
        if ($result && isset($result['result'])) {
            return intval($result['result']);
        }
        return false;
    }

    /**
     * 注册接口
     * 前面有怪兽，迁移到v2接口之后未验证
     *
     * @param string $uname        用户名
     * @param string $password    密码
     * @param int $sex    性别    可选，1-男，2-女，若不传，默认男
     * @param string $verifycode    用户输入的验证码内容，当要求验证码时必选
     * @param string $vcodestr    验证码凭据key，当要求验证码时必选
     * @return array|false    登录失败返回false（除了除非验证码策略），否则返回如下数组:
     * array(
     *     'needvcode' => 是否需要验证码, 值枚举：1|0
     *     'vcodestr' => 验证码凭据key
     *  'uid' => 用户id, 仅当登录成功时返回
     *  'uname' => 用户名, 仅当登录成功时返回
     *  'bduss' => bduss, 仅当登录成功且非验证时返回
     *  'ptoken' => ptoken, 仅当登录成功且非验证时返回
     *  'stoken' => stoken, 仅当登录成功且非验证时返回
     *  'weakpass' => 是否弱密码, 0-正常，1-弱密码，2-极弱密码
     *  )
     */
    public function reg_user($uname, $password, $sex, $clientip = null,
                        $verifycode = null, $vcodestr = null)
    {
        $params = array(
            'username' => $uname,
            'password' => $password,
            'sex' => $sex,
            'clientid' => PPSApiConfig::PP_CLIENTID,
            'clientip' => $clientip,
            'verifycode' => $verifycode,
            'vcodestr' => $vcodestr,
        );
        return $this->call_method('/sapi/reg', self::HTTP_POST, $params);
    }

    /**
     * 注册passport半账号
     * @param array $params
     * @param string $regip 用户ip
     * @param string $srctpl 源产品线tpl
     * @param string $subpro 产品线的渠道号
     * @param string $regtime 注册时间
     * @return array | false 注册成功返回如下数组，否则返回false:
     * array(
     *         'errno' => 110000,
     *         'userid' => xxxx,
     *         'errmsg' => '成功',
     * )
     */
    public function reg_incomplete(Array $params=array(),$regip='',$srctpl=null,$subpro=null,$regtime=null)
    {
        $params['regip'] = empty($regip)?CLog::getClientIP():$regip;
        $params['regtime'] = $regtime ? $regtime : time();
        !empty($srctpl) && $params['srctpl'] = $srctpl;
        !empty($subpro) && $params['subpro'] = $subpro;
        $result = $this->call_method('/v2/?regpcs', self::HTTP_GET, $params);
        if (isset($result['userid']) && $result['userid'] > 0){
            return $result;
        }else {
            return false;
        }
    }

    /**
     * 半账号登录
     * @param int $uid 半账号uid
     * @param bool $memlogin 是否记住密码
     * @param string $thirdname 第三方用户名
     * @param string $clientip
     * @param int  $usersource
     * @param string  $srctpl  产品线名称
     * @param string $subpro
     * @return array | false 登录成功返回如下数组，否则返回false:
     * array(
     *        'errno' => 110000,登录正确的错误码是110000
     *        'bduss' => 'xxxx',
     *        'errmsg' => '成功',
     * )
     */
    public function login_incomplete($uid,$memlogin=false,$thirdname = null,$clientip = null,$usersource=0,
                                     $srctpl=null,$subpro=null)
    {
        if (empty($clientip)){
            $clientip =CLog::getClientIP();
        }
        $params = array(
                    'clientip' => $clientip,
                    'userid' => intval($uid),
                    'isremember_pwd' => $memlogin ? 1 : 0
                  );
        $thirdname && $params['thirdname'] = $thirdname;
        $srctpl && $params['srctpl'] = $srctpl;
        $subpro && $params['subpro'] = $subpro;
        $params['usersource'] = $usersource;
        $result = $this->call_method('/v2/?loginpcs', self::HTTP_GET, $params);
        if ($result['bduss']){
            return $result;
        }else {
            return false;
        }
    }

    /**
     * 个人资料获取接口
     * @param string $bduss        passport的用户bduss
     * @return array|false        登录失败返回false，否则返回如下数组:
     * array(
     *  'errno' => 错误号
     *  'data' => 请求的资料数据
     *  )
     */
    public function get_uinfo_all($bduss)
    {
        $fields = json_encode(array('birthday', 'sex', 'blood', 'homeplace', 'residence',
                     'introduction', 'somatotype', 'marital_status', 'smoke', 'drink',
                     'sleep', 'character', 'edu_status', 'career', 'contact', 'interest',
                     'edu_background', 'job', 'real_name'));
        $params = array(
            'bduss' => $bduss,
            'fields' => $fields,
        );
        return $this->call_method('/sapi/getuinfo', self::HTTP_POST, $params);
    }

    /**
     * 个人资料设置接口
     * @param $bduss 登录用户的bduss
     * @param $user_info 用户信息
     * @return array|false        登录失败返回false，否则返回如下数组:
     * array(
     *  'errno' => 错误号
     *  'fields' => 设置成功的字段名
     *  )
     */
    public function set_uinfo($bduss, $user_info)
    {
        $params = array(
            'bduss' => $bduss,
            'fields' => json_encode($user_info),
            'ie' => 'utf-8',
            'partial' => 1,
        );
        return $this->call_method('/sapi/setuinfo', self::HTTP_POST, $params);
    }

    /**
     * 获取绑定email控件的token
     * @param int $uid 用户uid
     * @param string $baiduid cookie中的baiduid
     * @param string $ip 客户端ip
     * @return string
     */
    public function getBindMobileWidgetToken($uid,$baiduid,$ip){
        return $this->getWidgetToken('bindmobile',$uid,$baiduid,$ip);
    }

    /**
     * 获取绑定手机号控件的token
     * @param int $uid 用户uid
     * @param string $baiduid cookie中的baiduid
     * @param string $ip 客户端ip
     * @return string
     */
    public function getBindEmailWidgetToken($uid,$baiduid,$ip){
        return $this->getWidgetToken('bindemail',$uid,$baiduid,$ip);
    }

    /**
     * 获取绑定控件的token
     * @param string $action bindemail(绑定email控件) && bindmobile(绑定手机号控件)
     * @param int $userid   用户uid
     * @param string $baiduid cookie中的baiduid
     * @param string $ip    客户端ip
     * @return string
     */
    private function getWidgetToken($action, $userid, $baiduid, $ip=''){
        $ip = empty($ip) ? CLog::getClientIP() : $ip;
        $info = json_encode(array(
            $action,
            $userid,
            $baiduid,
            $ip,
        ));
        $pack = json_encode(
            array(
                PPSApiConfig::PP_TPL,
                PPSApiConfig::PP_APPID,
                Utils::rc4_encode($info,PPSApiConfig::PP_KEY),
            )
        );
        return Utils::rc4_encode($pack);
    }

    /**
     * 具体实现接口调用
     *
     * @param string $path        SApi接口的url路径
     * @param string $method    Http方法，'GET' or 'POST'
     * @param array $params        Http请求参数
     * @return array
     */
    protected function call_method($path, $method, array $params)
    {
        //产品线标识
        $params['tpl'] = PPSApiConfig::PP_TPL;
        $params['appid'] = PPSApiConfig::PP_APPID;
        $params['sig'] = $this->generate_sig($params, PPSApiConfig::PP_KEY);

        return parent::call_method($path, $method, $params,
                                   PPSApiConfig::$arrServers,
                                   PPSApiConfig::RETRY_TIMES,
                                   PPSApiConfig::CONNECT_TIMEOUT,
                                   PPSApiConfig::TIMEOUT);
    }

    /**
     * 生成参数签名
     *
     * @param array $params        待签名参数
     * @param string $secret    密钥
     * @return string
     */
    protected function generate_sig($params, $secret)
    {
        $str = '';
        ksort($params);
        foreach ($params as $k => $v) {
            if ($v === null) {
                continue;
            }
            $str .= "$k=$v&";
        }
        $str .= 'sign_key=' . $secret;
        return md5($str);
    }

    /**
     * 解析http响应内容
     * @param $response    http响应内容
     * @return array|false
     */
    protected function parse_response($response)
    {
        if (!empty($response)) {
            $result = json_decode($response, true);
            $this->set_error($result['errno']);
            if(isset($result['errno']) && ($result['errno'] < 0
                 || $result['errno'] == self::PP_EC_DEPRECATED_INTERFACE)){
                return false;
            }else{
                $result['errmsg']=$this->errmsg();
                return $result;
            }
        }
        $this->set_error(self::PP_EC_DEFAULT);
        return false;
    }

    protected function set_error($errno, $errmsg = '')
    {
        if (empty($errmsg)) {
            if (isset(self::$error_descs[$this->lang][$errno])) {
                $errmsg = self::$error_descs[$this->lang][$errno];
            } else {
                $errmsg = self::$error_descs[$this->lang][self::PP_EC_DEFAULT];
            }
        }
        parent::set_error($errno, $errmsg);
    }
}

