<?php

/**
 * Encoder/Decoder of the user portrait & uid
 *
 * @category    Encrypt
 * @package        bdUcrypt
 * @version        $Revision: 1.2 $
 */
class bdUcrypt
{
    // 用于加密解密用户key
    public $pubKey;

    public $privKey;

    public function __construct(){
        //$this -> privKey = file_get_contents( CommonConfig::KEYS_PATH .'/private.key');
    }

    /**
     * Encode an integer and a string into an encrypt string
     *
     * @param int $intUserID
     * @param string $strUserName
     * @return string
     */
    public static function encode_portrait($intUserID, $strUserName = '')
    {
        $strChars = '0123456789abcdef';
        $arrValue = self::reinterpret_cast($intUserID);

        $strCode = $strChars[$arrValue[0] >> 4] . $strChars[$arrValue[0] & 15];
        $strCode .= $strChars[$arrValue[1] >> 4] . $strChars[$arrValue[1] & 15];

        $intLen = strlen($strUserName);
        for ($i = 0; $i < $intLen; ++$i) {
            $intValue = ord($strUserName[$i]);
            $strCode .= $strChars[($intValue >> 4)] . $strChars[($intValue & 15)];
        }

        $strCode .= $strChars[$arrValue[2] >> 4] . $strChars[$arrValue[2] & 15];
        $strCode .= $strChars[$arrValue[3] >> 4] . $strChars[$arrValue[3] & 15];

        return $strCode;
    }

    /**
     * Decode a encrypt string into an integer or an array
     *
     * @param string $strCode    encrypt string
     * @param bool $bolNeedUserName whether to retrive the user name party
     * @return int|array|false
     */
    public static function decode_portrait($strCode, $bolNeedUserName = false)
    {
        $intLen = strlen($strCode);
        if ($intLen < 10) {
            return false;
        }

        $intUserID = hexdec($strCode[$intLen - 2] . $strCode[$intLen - 1]);
        $intUserID = ($intUserID << 8) + hexdec($strCode[$intLen - 4] . $strCode[$intLen - 3]);
        $intUserID = ($intUserID << 8) + hexdec($strCode[2] . $strCode[3]);
        $intUserID = ($intUserID << 8) + hexdec($strCode[0] . $strCode[1]);

        if ($bolNeedUserName) {
            $intLast = $intLen - 4;
            $strUserName = '';
            for ($i = 4; $i < $intLast; $i += 2) {
                $strUserName .= chr(hexdec($strCode[$i] . $strCode[$i + 1]));
            }
            if (strlen($strUserName) > 32 || !preg_match('/^[^<>"\'\/]+$/', $strUserName)) {
                return false;
            }
            return array('uid'  =>  $intUserID,
                         'uname'=>  $strUserName,
                        );
        } else {
            return $intUserID;
        }
    }

    /**
     * Encode uid to protect it from the third party
     *
     * @param int $uid
     * @return  int
    **/
    public static function api_encode_uid($uid)
    {
        $sid = ($uid & 0x0000ff00)<< 16;
        $sid += (($uid & 0xff000000)>> 8)& 0x00ff0000;
        $sid += ($uid & 0x000000ff)<< 8;
        $sid += ($uid & 0x00ff0000)>> 16;
        $sid ^= 282335;    //该值定了就不能再改了，否则就出问题了
        return $sid;
    }

    /**
     * Decode uid from sid
     *
     * @param int $sid
     * @return int
    **/
    public static function api_decode_uid($sid)
    {
        if (!is_int($sid)&& !is_numeric($sid))
        {
            return false;
        }

        $sid ^= 282335;    //该值定了就不能再改了，否则就出问题了
        $uid = ($sid & 0x00ff0000)<< 8;
        $uid += ($sid & 0x000000ff)<< 16;
        $uid += (($sid & 0xff000000)>> 16)& 0x0000ff00;
        $uid += ($sid & 0x0000ff00)>> 8;
        return $uid;
    }

    /**
     * Convert an integer into an array of bytes
     *
     * @param int $intUserID
     * @return array
     */
    private static function reinterpret_cast($intUserID)
    {
        $arrValue = array();
        $intUserID = intval($intUserID);
        $arrValue[] = $intUserID & 0x000000ff;
        $arrValue[] = ($intUserID & 0x0000ff00) >> 8;
        $arrValue[] = ($intUserID & 0x00ff0000) >> 16;
        $arrValue[] = ($intUserID >> 24) & 0x000000ff;

        return $arrValue;
    }

    /**
     * device id对称加密算法
     *
     * @param string $device_id 设备唯一标识码
     * @param string $secret    密钥
     * @return string
     */
   public static function encryptDeviceId($device_id, $secret)
    {
        $md5_v = md5($secret);
        //Open the cipher
        $td = mcrypt_module_open('rijndael-128', '', 'cbc', '');
        //Create key and IV
        $key = substr($md5_v, 0, 16);
        $iv = strrev(substr($md5_v, 0, 16));
        //Intialize encryption，不满16字符后面补\0
        mcrypt_generic_init($td, $key, $iv);
        //Encrypt data
        $device_id = mcrypt_generic($td, trim($device_id));
        //Terminate encryption handler and close module
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return base64_encode($device_id);
    }

    /**
     * device id解密算法
     *
     * @param string  $device_id 设备唯一标识码
     * @param string  $secret    密钥
     * @return string
     */
    public static function decodeDeviceId($device_id, $secret)
    {
        $device_id = base64_decode($device_id);
        if (empty($device_id)) {
            return false;
        }
        $md5_v = md5($secret);
        //Open the cipher
        $td = mcrypt_module_open('rijndael-128', '', 'cbc', '');
        //Create key and IV，不满16字符后面补\0
        $key = substr($md5_v, 0, 16);
        $iv = strrev(substr($md5_v, 0, 16));
        //Intialize encryption
        mcrypt_generic_init($td, $key, $iv);
        //Encrypt data
        $device_id = mdecrypt_generic($td, $device_id);
        //Terminate encryption handler and close module
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return trim($device_id);
    }
    
    /**
    * form 信息加密算法
    * @param string $pdata 数据
    * @param string $secret 密钥
    * @return array
    * @author xiaochen
    */

    public static function encodeRSA( $data ){

        $pubKey = file_get_contents( CommonConfig::KEYS_PATH . '/public.key' );

        if (openssl_public_encrypt( $data, $encrypted, $pubKey )){
            $ret = base64_encode($encrypted);
        }else{
            throw new Exception('Unable to encrypt data. Perhaps it is bigger than the key size?');
        }
        return $ret;

    }

    /**
    * form 信息解密算法
    * @param string $pdata 数据
    * @param string $secret 密钥
    * @return array
    * @author xiaochen
    */

    public static function decodeRSA( $data ){

        $privKey = file_get_contents( CommonConfig::KEYS_PATH .'/private.key');

        if (openssl_private_decrypt(base64_decode($data), $decrypted, $privKey )){
            $ret = $decrypted;  
        }else{
            $ret = '';
        }
        return $ret;

    }


}
