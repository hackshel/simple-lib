<?php
/**
 * Some Util functions, mainly for string operations.
 * 
 * @category    Utils
 * @package        Utils
 * @version        $Revision: 1.2 $ 
 */
class Utils
{
    /**
     * check if the first arg starts with the second arg
     *
     * @param string $str        the string to search in
     * @param string $needle    the string to be searched
     * @return bool    true or false
     * @author zhujt
    **/
    public static function starts_with($str, $needle)
    {
        $pos = stripos($str, $needle);
        return $pos === 0;
    }

    /**
     * check if the first arg ends with the second arg
     *
     * @param string $str        the string to search in
     * @param string $needle    the string to be searched
     * @return bool    true or false
     * @author zhujt
    **/
    public static function ends_with($str, $needle)
    {
        $pos = stripos($str, $needle);
        if( $pos === false ) {
            return false;
        }
        return ($pos + strlen($needle) == strlen($str));
    }

    /**
     * undoes any magic quote slashing from an array, like the $_GET, $_POST, $_COOKIE
     *
     * @param array    $val    Array to be noslashing
     * @return array The array with all of the values in it noslashed
     * @author zhujt
    **/
    public static function noslashes_recursive($val)
    {
        if (get_magic_quotes_gpc()) {
            $val = self::stripslashes_recursive($val);
        }
        return $val;
    }

    public static function stripslashes_recursive($var)
    {
        if (is_array($var)) {
            return array_map(array('Utils', 'stripslashes_recursive'), $var);
        } elseif (is_object($var)) {
            $rvar = null;
            foreach ($var as $key => $val) {
                $rvar->{$key} = self::stripslashes_recursive($val);
            }
            return $rvar;
        } elseif (is_string($var)) {
            return stripslashes($var);
        } else {
            return $var;
        }
    }

    /**
     * Convert string or array to requested character encoding
     *
     * @param mix $var    variable to be converted
     * @param string $in_charset    The input charset.
     * @param string $out_charset    The output charset
     * @return mix    The array with all of the values in it noslashed
     * @see http://cn2.php.net/manual/en/function.iconv.php
     * @author zhujt
    **/
    public static function iconv_recursive($var, $in_charset = 'UTF-8', $out_charset = 'GBK')
    {
        if (is_array($var)) {
            $rvar = array();
            foreach ($var as $key => $val) {
                $rvar[$key] = self::iconv_recursive($val, $in_charset, $out_charset);
            }
            return $rvar;
        } elseif (is_object($var)) {
            $rvar = null;
            foreach ($var as $key => $val) {
                $rvar->{$key} = self::iconv_recursive($val, $in_charset, $out_charset);
            }
            return $rvar;
        } elseif (is_string($var)) {
            return iconv($in_charset, $out_charset, $var);
        } else {
            return $var;
        }
    }

    /**
     * Check if the text is gbk encoding
     *
     * @param string $str    text to be check
     * @return bool
     * @author zhujt
    **/
    public static function is_gbk($str)
    {
        return preg_match('%^(?:[\x81-\xFE]([\x40-\x7E]|[\x80-\xFE]))*$%xs', $str);
    }

    /**
     * Check if the text is utf8 encoding
     *
     * @param string $str    text to be check
     * @return bool Returns true if input string is utf8, or false otherwise
     * @author zhujt
    **/
    public static function is_utf8($str)
    {
        return preg_match('%^(?:[\x09\x0A\x0D\x20-\x7E]'.    // ASCII
                    '| [\xC2-\xDF][\x80-\xBF]'.                //non-overlong 2-byte
                    '| \xE0[\xA0-\xBF][\x80-\xBF]'.            //excluding overlongs
                    '| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}'.    //straight 3-byte
                    '| \xED[\x80-\x9F][\x80-\xBF]'.            //excluding surrogates
                    '| \xF0[\x90-\xBF][\x80-\xBF]{2}'.        //planes 1-3
                    '| [\xF1-\xF3][\x80-\xBF]{3}'.            //planes 4-15
                    '| \xF4[\x80-\x8F][\x80-\xBF]{2}'.        //plane 16
                    ')*$%xs', $str);
    }

    public static function txt2html($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'GB2312');
    }

    /**
     * Escapes text to make it safe to display in html.
     * FE may use it in Javascript, we also escape the QUOTES
     *
     * @param string $str    text to be escaped
     * @return string    escaped string in gbk
     * @author zhujt
    **/
    public static function escape_html_entities($str)
    {
        return htmlspecialchars($str, ENT_QUOTES, 'GB2312');
    }

    /**
     * Escapes text to make it safe to use with Javascript
     *
     * It is usable as, e.g.:
     *  echo '<script>alert(\'begin'.escape_js_quotes($mid_part).'end\');</script>';
     * OR
     *  echo '<tag onclick="alert(\'begin'.escape_js_quotes($mid_part).'end\');">';
     * Notice that this function happily works in both cases; i.e. you don't need:
     *  echo '<tag onclick="alert(\'begin'.txt2html_old(escape_js_quotes($mid_part)).'end\');">';
     * That would also work but is not necessary.
     *
     * @param string $str    text to be escaped
     * @param bool $quotes    whether should wrap in quotes
     * @return string
     * @author zhujt
    **/
    public static function escape_js_quotes($str, $quotes = false)
    {
        $str = strtr($str, array('\\'    => '\\\\',
                                 "\n"    => '\\n',
                                 "\r"    => '\\r',
                                 '"'    => '\\x22',
                                 '\''    => '\\\'',
                                 '<'    => '\\x3c',
                                 '>'    => '\\x3e',
                                 '&'    => '\\x26'));

        return $quotes ? '"'. $str . '"' : $str;
    }

    public static function escape_js_in_quotes($str, $quotes = false)
    {
        $str = strtr($str, array('\\"'    => '\\&quot;',
                                 '"'    => '\'',
                                 '\''    => '\\\'',
                                ));

        return $quotes ? '"'. $str . '"' : $str;
    }

    /**
     * Redirect to the specified page
     *
     * @param string $url    the specified page's url
     * @param bool $top_redirect    Whether need to redirect the top page frame
     * @author zhujt
    **/
    public static function redirect($url, $top_redirect = true)
    {
        if ($top_redirect && preg_match('/^https?:\/\/([^\/]*\.)?baidu\.com(:\d+)?/i', $url)) {
            // make sure baidu.com url's load in the full frame so that we don't
            // get a frame within a frame.
            echo '<script type="text/javascript"> top.location.href = "' . $url . '";</script>';
        } else {
            header('Location: ' . $url);
        }
        exit();
    }

    /**
     * Get current page's real url
     * 
     * @return string
     * @author zhujt
    **/
    public static function current_url()
    {
        $scheme = 'http';
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            $scheme = strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']);
        } elseif (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $scheme = 'https';
        }

        return $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Get the login url for specified product of baidu
     * 
     * @param string $next    The next url to jump when finish login
     * @param string $tpl    Tpl code for baidu product
     * @return string    Login url string
     * @author zhujt
     */
    public static function get_login_url($next = null, $tpl = 'sp')
    {
        $passport = defined('PASSPORT_DOMAIN') ? PASSPORT_DOMAIN : 'https://passport.baidu.com';
        return $passport . '/?login&tpl=' . $tpl . '&tpl_reg=' . $tpl . 'sp&' . ($next ? 'u='.urlencode($next) : '');
    }

    /**
     * Redirect current page to baidu default login page
     * 
     * @param $tpl Tpl code for current product
     * @author zhujt
     */
    public static function require_login($tpl = 'sp')
    {
        self::redirect(self::get_login_url(self::current_url(), $tpl));
    }

    /**
     * Converts charactors in the string to upper case
     *
     * @param string $str string to be convert
     * @return string
     * @author zhujt
    **/
    public static function strtoupper($str)
    {
        $uppers =
            array('A','B','C','D','E','F','G','H','I','J','K','L','M','N',
                  'O', 'P','Q','R','S','T','U','V','W','X','Y','Z');
        $lowers =
            array('a','b','c','d','e','f','g','h','i','j','k','l','m','n',
                  'o','p','q','r','s','t','u','v','w','x','y','z');
        return str_replace($lowers, $uppers, $str);
    }

    /**
     * Converts charactors in the string to lower case
     *
     * @param string $str    string to be convert
     * @return string
     * @author zhujt
    **/
    public static function strtolower($str)
    {
        $uppers =
            array('A','B','C','D','E','F','G','H','I','J','K','L','M','N',
                  'O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $lowers =
            array('a','b','c','d','e','f','g','h','i','j','k','l','m','n',
                  'o','p','q','r','s','t','u','v','w','x','y','z');
        return str_replace($uppers, $lowers, $str);
    }

    /**
     * Urlencode a variable recursively, array keys and object property names
     * will not be encoded, so you would better use ASCII to define the array
     * key name or object property name.
     *
     * @param mixed $var
     * @return  mixed, with the same variable type
     * @author zhujt
    **/
    public static function urlencode_recursive($var)
    {
        if (is_array($var)) {
            return array_map(array('Utils', 'urlencode_recursive'), $var);
        } elseif (is_object($var)) {
            $rvar = null;
            foreach ($var as $key => $val) {
                $rvar->{$key} = self::urlencode_recursive($val);
            }
            return $rvar;
        } elseif (is_string($var)) {
            return urlencode($var);
        } else {
            return $var;
        }
    }

    /**
     * Urldecode a variable recursively, array keys and object property
     * names will not be decoded, so you would better use ASCII to define
     * the array key name or object property name.
     *
     * @param mixed $var
     * @return  mixed, with the same variable type
     * @author zhujt
    **/
    public static function urldecode_recursive($var)
    {
        if (is_array($var)) {
            return array_map(array('Utils', 'urldecode_recursive'), $var);
        } elseif (is_object($var)) {
            $rvar = null;
            foreach ($var as $key => $val) {
                $rvar->{$key} = self::urldecode_recursive($val);
            }
            return $rvar;
        } elseif (is_string($var)) {
            return urldecode($var);
        } else {
            return $var;
        }
    }

    /**
     * Encode a string according to the RFC3986
     * @param string $s
     * @return string
     */
    public static function urlencode3986($var)
    {
        return str_replace('%7E', '~', rawurlencode($var));
    }
    
    /**
     * Decode a string according to RFC3986.
     * Also correctly decodes RFC1738 urls.
     * @param string $s
     */
    public static function urldecode3986($var)
    {
        return rawurldecode($var);
    }
    
    /**
     * Urlencode a variable recursively according to the RFC3986, array keys
     * and object property names will not be encoded, so you would better use
     * ASCII to define the array key name or object property name.
     *
     * @param mixed $var
     * @return  mixed, with the same variable type
     * @author zhujt
    **/
    public static function urlencode3986_recursive($var)
    {
        if (is_array($var)) {
            return array_map(array('Utils', 'urlencode3986_recursive'), $var);
        } elseif (is_object($var)) {
            $rvar = null;
            foreach ($var as $key => $val) {
                $rvar->{$key} = self::urlencode3986($val);
            }
            return $rvar;
        } elseif (is_string($var)) {
            return str_replace('%7E', '~', rawurlencode($var));
        } else {
            return $var;
        }
    }
    
    /**
     * Urldecode a variable recursively according to the RFC3986, array keys
     * and object property names will not be decoded, so you would better use
     * ASCII to define the array key name or object property name.
     *
     * @param mixed $var
     * @return  mixed, with the same variable type
     * @author zhujt
    **/
    public static function urldecode3986_recursive($var)
    {
        if (is_array($var)) {
            return array_map(array('Utils', 'urldecode3986_recursive'), $var);
        } elseif (is_object($var)) {
            $rvar = null;
            foreach ($var as $key => $val) {
                $rvar->{$key} = self::urldecode3986_recursive($val);
            }
            return $rvar;
        } elseif (is_string($var)) {
            return rawurldecode($var);
        } else {
            return $var;
        }
    }
    
    /**
     * Base64_encode a variable recursively, array keys and object property
     * names will not be encoded, so you would better use ASCII to define the
     * array key name or object property name.
     *
     * @param mixed $var
     * @return mixed, with the same variable type
     * @author zhujt
    **/
    public static function base64_encode_recursive($var)
    {
        if (is_array($var)) {
            return array_map(array('Utils', 'base64_encode_recursive'), $var);
        } elseif (is_object($var)) {
            $rvar = null;
            foreach ($var as $key => $val) {
                $rvar->{$key} = self::base64_encode_recursive($val);
            }
            return $rvar;
        } elseif (is_string($var)) {
            return base64_encode($var);
        } else {
            return $var;
        }
    }

    /**
     * Base64_decode a variable recursively, array keys and object property
     * names will not be decoded, so you would better use ASCII to define the
     * array key name or object property name.
     *
     * @param mixed $var
     * @return mixed, with the same variable type
     * @author zhujt
    **/
    public static function base64_decode_recursive($var)
    {
        if (is_array($var)) {
            return array_map(array('Utils', 'base64_decode_recursive'), $var);
        } elseif (is_object($var)) {
            $rvar = null;
            foreach ($var as $key => $val) {
                $rvar->{$key} = self::base64_decode_recursive($val);
            }
            return $rvar;
        } elseif (is_string($var)) {
            return base64_decode($var);
        } else {
            return $var;
        }
    }

    /**
     * Encode the GBK format var into json format.
     * 
     * The standard json_encode & json_decode needs all strings be in ASCII
     * or UTF-8 format, but most of the time, we use GBK format strings and
     * the standard ones will not work properly, by base64_encoded the strings
     * we can change them to ASCII format and let the json_encode & json_decode
     * functions work.
     * 
     * @param mixed $var The value being encoded. Can be any type except a resource.
     * @return string json format string
     * @author zhujt
    **/
    public static function json_encode($var)
    {
        return json_encode(self::base64_encode_recursive($var));
    }

    /**
     * Decode the GBK format var from json format.
     * 
     * The standard json_encode & json_decode needs all strings be in ASCII
     * or UTF-8 format, but most of the time, we use GBK format strings and
     * the standard ones will not work properly, by base64_encoded the strings
     * we can change them to ASCII format and let the json_encode & json_decode
     * functions work.
     * 
     * @param string $json    json formated string
     * @param bool $assoc    When TRUE, returned objects will be converted into associative arrays.
     * @return mixed, associated array with values be urldecoded
     * @author zhujt
    **/
    public static function json_decode($json, $assoc = false)
    {
        return self::base64_decode_recursive(json_decode($json, $assoc));
    }
    
    /**
     * Remove BOM string (0xEFBBBF in hex) for input string which is added
     * by windows when create a UTF-8 file.
     * 
     * @param string $str
     * @return string
     * @author zhujt
     */
    public static function remove_bom($str)
    {
        if (substr($str, 0, 3) === pack('CCC', 0xEF, 0xBB, 0xBF)) {
            $str = substr($str, 3);
        }
        return $str;
    }
    
    /**
     * Generate a unique random key using the methodology
     * recommend in php.net/uniqid
     *
     * @return string a unique random hex key
    **/
    public static function generate_rand_key()
    {
        return md5(uniqid(mt_rand(), true));
    }
    
    /**
     * Generate a random string of specifified length
     * 目前应用创建产生的api key和secret key使用该算法，切记不能改动。
     * 
     * @author wulin02(wulin02@baidu.com)
     * @param  int    $len    default 32
     * @param  string $seed
     * @return string
     */
    public static function generate_rand_str($len = 32, $seed = '')
    {
        if (empty($seed)) {
            $seed = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIGKLMNOPQRSTUVWXYZ';
        }
        $seed_len = strlen($seed);
        $word = '';
        //随机种子更唯一
        mt_srand((double)microtime() * 1000000 * getmypid());
        for ($i = 0; $i < $len; ++$i) 
        {
            $word .= $seed{mt_rand() % $seed_len};
        }
        return $word;
    }

    /**
     * Send email by sendmail command
     *
     * @param string $from    mail sender
     * @param string $to    mail receivers
     * @param string $subject    subject of the mail
     * @param string $content    content of the mail
     * @param string $cc
     * @return int result of sendmail command
     * @author zhujt
    **/
    public static function sendmail($from, $to, $subject, $content, $cc = null)
    {
        if (empty($from) || empty($to) || empty($subject) || empty($content)) {
            return false;
        }
        
        $mailContent = "To:$to\nFrom:$from\n";
        if (!empty($cc)) {
            $mailContent .= "Cc:$cc";
        }
        $mailContent .= "Subject:$subject\nContent-Type:text/html;charset=gb2312\n\n$content";
        
        $output = array();
        exec("echo -e '" . $mailContent . "' | /usr/sbin/sendmail -t", $output, $ret);
        
        return $ret;
    }

    /**
     * Trim the right '/'s of an uri path, e.g. '/xxx//' will be sanitized to '/xxx'
     *
     * @param string $uri URI to be trim
     * @return string sanitized uri
     * @author zhujt
    **/
    public static function sanitize_uri_path($uri)
    {
        $arrUri = explode('?', $uri);
        $arrUri = parse_url($arrUri[0]);
        $path = $arrUri['path'];
        
        $path = rtrim(trim($path), '/');
        if (!$path) {
            return '/';
        }
        return preg_replace('#/+#', '/', $path);
    }
    
    /**
     * Check whether input url has http:// or https:// as its scheme,
     * if hasn't, it will add http:// as its prefix
     * @param string $url
     * @return string
     */
    public static function http_scheme_auto_complete($url)
    {
        $url = trim($url);
        if (stripos($url, 'http://') !== 0 && stripos($url, 'https://') !== 0) {
            $url = 'http://' . $url;
        }
        return $url;
    }
    
    /**
     * Check if the two bytes are a chinese charactor
     *
     * @param char $lower_chr    lower bytes of the charactor
     * @param char $higher_chr    higher bytes of the charactor
     * @return bool Returns true if it's a chinese charactor, or false otherwise
     * @author liaohuiqin
    **/
    public static function is_cjk($lower_chr, $higher_chr)
    {
        if (($lower_chr >= 0xb0 && $lower_chr <= 0xf7 && $higher_chr >= 0xa1 && $higher_chr <= 0xfe) ||
            ($lower_chr >= 0x81 && $lower_chr <= 0xa0 && $higher_chr >= 0x40 && $higher_chr<=0xfe) ||
            ($lower_chr >= 0xaa && $lower_chr <= 0xfe && $higher_chr >= 0x40 && $higher_chr <=0xa0)) {
            return true;
        }
        return false;
    }

    /**
     * 检查一个字符是否是gbk图形字符
     *
     * @param char $lower_chr    lower bytes of the charactor
     * @param char $higher_chr    higher bytes of the charactor
     * @return bool Returns true if it's a chinese graph charactor, or false otherwise
     * @author liaohq
    **/
    public static function is_gbk_graph($lower_chr, $higher_chr)
    {
        if (($lower_chr >= 0xa1 && $lower_chr <= 0xa9 && $higher_chr >= 0xa1 && $higher_chr <= 0xfe) ||
            ($lower_chr >= 0xa8 && $lower_chr <= 0xa9 && $higher_chr >= 0x40 && $higher_chr <= 0xa0)) {
            return true;
        }
        return false;
    }

    /**
     * 检查字符串中每个字符是否是gbk范围内可见字符，包括图形字符和汉字, 半个汉字将导致检查失败,
     * ascii范围内不可见字符允许，默认$str是gbk字符串,如果是其他编码可能会失败
     * 
     * @param string $str string to be checked
     * @return  bool 都是gbk可见字符则返回true，否则返回false
     * @author liaohq
    **/
    public static function  check_gbk_seen($str)
    {
        $len = strlen($str);
        $chr_value = 0;
        
        for ($i = 0; $i < $len; $i++) {
            $chr_value = ord($str[$i]);
            if ($chr_value < 0x80) {
                continue;
            } elseif ($chr_value === 0x80) {
                //欧元字符;
                return false;
            } else {
                if ($i + 1 >= $len) {
                    //半个汉字;
                    return false;
                }
                if (!self::is_cjk(ord($str[$i]), ord($str[$i + 1])) &&
                    !self::is_gbk_graph(ord($str[$i]), ord($str[$i + 1]))) {
                    return false;
                }
            }
            $i++;
        }
        return true;
    }

    /**
     * 检查$str是否由汉字/字母/数字/下划线/.组成，默认$str是gbk编码
     *
     * @param string $str string to be checked
     * @return  bool
     * @author liaohq
    **/
    public static function check_cjkalnum($str)
    {
        $len = strlen($str);
        $chr_value = 0;
        
        for ($i = 0; $i < $len; $i++) {
            $chr_value = ord($str[$i]);
            if ($chr_value < 0x80) {
                if (!ctype_alnum($str[$i]) && $str[$i] != '_' && $str[$i] != '.') {
                    return false;
                }
            } elseif ($chr_value === 0x80) {
                //欧元字符;
                return false;
            } else {
                if ($i + 1 >= $len) {
                    //半个汉字;
                    return false;
                }
                if (!self::is_cjk(ord($str[$i]), ord($str[$i + 1]))) {
                    return false;
                }
                $i++;
            }
        }
        return true;
    }

    /**
     * 检查字符串是否是gbk汉字，默认字符串的编码格式是gbk
     *
     * @param string $str string to be checked
     * @return  bool
     * @author liaohq
    **/
    public static function check_cjk($str)
    {
        $len = strlen($str);
        $chr_value = 0;
        
        for ($i = 0; $i < $len; $i++) {
            $chr_value = ord($str[$i]);
            if ($chr_value <= 0x80) {
                return false;
            } else {
                if ($i + 1 >= $len) {
                    //半个汉字;
                    return false;
                }
                if (!self::is_cjk(ord($str[$i]), ord($str[$i + 1]))) {
                    return false;
                }
                $i++;
            }
        }
        return true;
    }

    /**
     * check whether the url is safe
     * 
     * @param string $url    URL to be checked
     * @return bool
     * @author zhujt
    **/
    public static function is_valid_url($url)
    {
        if (strlen($url) > 0) {
            if (!preg_match('/^https?:\/\/[^\s&<>#;"\'\?]+(|#[^\s<>"\']*|\?[^\s<>"\']*)$/i',
                            $url, $match)) {
                return false;
            }
        }
        return true;
    }

    /**
     * check whether the email address is valid
     * 
     * @param string $email Email to be checked
     * @return bool
     * @author zhujt
    **/
    public static function is_valid_email($email)
    {
        if (strlen($email) > 0) {
            if (!preg_match('/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})$/i',
                            $email, $match)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check whether it is a valid phone number
     * 
     * @param string $phone    Phone number to be checked
     * @return bool
     * @author zhujt
    **/
    public static function is_valid_phone($phone)
    {
        if (strlen($phone) > 0) {
            if (!preg_match('/^([0-9]{11}|[0-9]{3,4}-[0-9]{7,8}(-[0-9]{2,5})?)$/i',
                            $phone, $match)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check whether it is a valid ip list, each ip is delemited by ','
     * 
     * @param string $iplist Ip list string to be checked
     * @return bool
     * @author zhujt
    **/
    public static function is_valid_iplist($iplist)
    {
        $iplist = trim($iplist);
        if (strlen($iplist) > 0) {
            if (!preg_match('/^(([0-9]{1,3}\.){3}[0-9]{1,3})(,(\s)*([0-9]{1,3}\.){3}[0-9]{1,3})*$/i',
                            $iplist, $match)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Generate a signature.  Should be copied into the client
     * library and also used on the server to validate signatures.
     *
     * @param array    $params    params to be signatured
     * @param string $secret    secret key used in signature
     * @param string $namespace    prefix of the param name, all params whose name are equal
     * with $namespace will not be put in the signature.
     * @return string md5 signature
     **/
    public static function generate_sig($params, $secret, $namespace = 'bd_sig')
    {
        $str = '';
        ksort($params);
        foreach ($params as $k => $v) {
            if ($k != $namespace) {
                $str .= "$k=$v";
            }
        }
        $str .= $secret;
        return md5($str);
    }

    /**
     *
     * 对应扩展chpr_b64的decode方法，用于对框的cuid进行解密
     * @param string $input
     * @param int $secret
     * @return bool|string
     */
    public static function cipher_base64_decode($input,$secret=0){
       if(empty($input)){
           return false;
       }
       $len = chpr_B64_Decode_php($input,$secret);
       if($len>0){
           return substr($input,0,$len);
       }
       return false;
    }

    /**
     *
     * cipher_base64_encode方法,用于对框的cuid进行加密操作
     * 对应扩展的chpr_b64的encode方法
     * @param string $input
     * @param int $secret
     * @return bool
     */
    public static function cipher_base64_encode($input,$secret=0){
       if(empty($input)){
           return false;
       }
       $input_size = strlen($input);
       $pad_size = chpr_B64_Buffer_Need_php($input_size);
       $input = str_pad($input, $pad_size, '0');
       $len = chpr_B64_Encode_php($input,$secret);
       return $len>=0 ? $input : false;
    }
    
    /**
     * 安全的获得一个url的host部分
     *
     * @param string $url
     * @return string | false
     */
    public static function getUrlHost($url)
    {
        /**
         * 模拟浏览器(除firefox外)的行为，将'\'转换成'/',规避如下风险：
         * http://xss1.com\@www.baidu.com 经parse_url解析出的host为www.baidu.com
         */
        $url = str_replace('\\', '/', $url);
        /**
         * 模拟大部分浏览器的行为，将';'转换成'/;',规避如下风险：
         * http://xss1.com;.www.baidu.com 经parse_url解析出的host为xss1.com;.www.baidu.com
         */
        $url = str_replace(';', '/;', $url);
        
        $host = parse_url($url, PHP_URL_HOST);
        if (empty($host)) {
            return false;
        }
        /**
         * 规避parse_url的1个坑：
         * $callback_url = 'http://passport.iqiyi.com@www.wooyun.org?passport.iqiyi.com/';
         * parse_url($callback_url, PHP_URL_HOST);得到的是：
         * "www.wooyun.org?passport.iqiyi.com"
         *
         */
        // 去除/后面的部分
        $slash_index = strpos($host, '/');
        if ($slash_index !== FALSE ) {
            $host = substr($host, 0, $slash_index);
        }
        //去除query部分
        $q_index = strpos($host, '?');
        if ($q_index !== FALSE ) {
            $host = substr($host, 0, $q_index);
        }
        //去除fragment部分
        $q_index = strpos($host, '#');
        if ($q_index !== FALSE ) {
            $host = substr($host, 0, $q_index);
        }
        
        return $host;
    }
    
    public static function getRootDomain($host)
    {
        if (!preg_match('/[^.]+\.[^.]+$/', $host, $matches)) {
            return false;
        }
        return $matches[0];
    }
    
    /**
     * 判断当前请求是否是https的
     *
     * @return bool 是https请求时，返回true
     */
    public static function isHttps()
    {
        /**
         * nginx会暴露一个内网端口给系统部，https请求经过系统部加解密之后会转发到这个端口
         * 这个端口的访问均认为是https的
         * add: zhengweide 2014-07-02
         */
        if (intval($_SERVER['SERVER_PORT']) === SSL_BYPASS_PORT ||
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')) {
            return true;
        }
        return false;
    }

    /**
     * 使用rc4算法加密
     * @param string $plain 明文
     * @param string $key   秘钥
     * @param int $expiry   失效时间
     * @return string
     */
    public static function rc4_encode($plain,$key='',$expiry=0){
        return self::rc4($plain,'ENCODE',$key,$expiry);
    }

    /**
     * 使用rc4算法解密
     * @param $cipher $cipher
     * @param string  $key
     * @param int     $expiry
     * @return string
     */
    public static function rc4_decode($cipher,$key='',$expiry=0){
        return self::rc4($cipher,'DECODE',$key,$expiry);
    }

    /**
     * 支持odp环境的bd_crypt_rc4 加密、解密
     * @see https://svn.baidu.com/inf/odp/trunk/lib/crypt/lib/bd/crypt/Rc4.php
     * @param string $string  输入字符串
     * @param string $operation 操作ENCODE(加密) or DECODE(解密)
     * @param string $key 秘钥
     * @param int $expiry 有效期，时间单位为秒
     * @return string 加密（解密）后的字符串
     *
     * @example:  rc4('a'); 用默认的key对字符 a 进行加密
     * @example:  rc4('a', 'DECODE'); 用默认的key对a进行解密
     * @example:  rc4('a', 'ENCODE', 'abc'); 用指定的 key 'abc'对字符a进行加密
     * @example:  rc4('a', 'ENCODE', 'abc', 15); 用指定的 key 'abc'对字符a进行加密, 设定有效期 15 秒
     *
     */
    private static function rc4($string, $operation = 'ENCODE', $key = '', $expiry = 0)
    {
        $ckey_length = 4;

        $key = md5($key != '' ? $key : 'BaiduRc4Key');
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $box[$i] = $box[$i] ^ $box[$j];
            $box[$j] = $box[$i] ^ $box[$j];
            $box[$i] = $box[$i] ^ $box[$j];
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $box[$a] = $box[$a] ^ $box[$j];
            $box[$j] = $box[$a] ^ $box[$j];
            $box[$a] = $box[$a] ^ $box[$j];
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0)
                && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)
            ) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }
}

