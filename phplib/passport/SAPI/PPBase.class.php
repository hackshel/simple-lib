<?php

/**
 * Base class for Passport interfaces
 *
 * @package        Passport
 * @author        zhujt <zhujianting@baidu.com>
 * @version        $Revision: 1.0 $
 */
abstract class PPBase
{
    const HTTP_GET = 'GET';
    const HTTP_POST = 'POST';

    protected $errno = 0;
    protected $errmsg = '';

    public function __construct()
    {

    }

    protected function set_error($errno, $errmsg = '')
    {
        $this->errno = $errno;
        $this->errmsg = $errmsg;

        /**
         * comments by liliang
         * cache the error by errno() and errmsg
         *
        if ($this->errno) {
            CLog::warning("errno[$this->errno] errmsg[$this->errmsg]");
        }
        **/
    }

    /**
     * Returns last errno
     * @return int
     */
    public function errno()
    {
        return $this->errno;
    }

    /**
     * Returns last error message
     * @return string
     */
    public function errmsg()
    {
        return $this->errmsg;
    }

    /**
     * Do http method call
     *
     * @param string $path        Url path for http request
     * @param string $method    Method for http request, 'GET' or 'POST'
     * @param array $params        Params for http request
     * @param array $servers    Server config for http request
     * @param int $retry_times    Retry times for failed http request
     * @param int $connect_timeout    Connect timeout for http request
     * @param int $timeout        Timeout for http request
     * @return array|false        Returns an array if http request success,
     *                             or false if otherwise
     */
    protected function call_method($path, $method, array $params,
                                   array $servers, $retry_times = 3,
                                   $connect_timeout = 100, $timeout = 1000)
    {
        $current_idc = defined('CURRENT_CONF') ? CURRENT_CONF : 'jx';
        $servers = $servers[$current_idc];

        for ($i = 0; $i < $retry_times; ++$i) {
            if (count($servers) <= 0) {
                break;
            }
            //randomly pick a server
            $index = array_rand($servers);
            $server =  $servers[$index];
            $url = $server . $path;

            $response = $this->http_request($url, $method, $params,
                                            $connect_timeout, $timeout);
            if ($response === false) {
                unset($servers[$index]);
                continue;
            }

            return $this->parse_response($response);
        }

        return false;
    }

    /**
     * Send http request and get the response back
     *
     * @param string $url        Target url
     * @param string $method    Http method, 'GET' or 'POST'
     * @param array $params        Query params or POST params
     * @param int $connect_timeout    Connect timeout
     * @param int $timeout            Http request timeout
     * @return string|false        Returns the response content if success,
     *                             or false if http request failed
     */
    protected function http_request($url, $method, $params, $connect_timeout, $timeout)
    {
        $user_agent = sprintf('Hxfilm Phplib Client (PHP %s)', phpversion());
        $param_string = str_replace('%7E', '~', http_build_query($params));

        $ch = curl_init();
        if ($ch === false) {
            $this->set_error('default', 'init curl failed');
            return false;
        }
        $curl_opts = array(
            CURLOPT_USERAGENT => $user_agent,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => false,
        );
        if (defined('CURLOPT_CONNECTTIMEOUT_MS')) {
            $curl_opts[CURLOPT_CONNECTTIMEOUT_MS] = $connect_timeout;
            $curl_opts[CURLOPT_TIMEOUT_MS] = $timeout;
        } else {
            $curl_opts[CURLOPT_CONNECTTIMEOUT] = ceil($connect_timeout / 1000);
            $curl_opts[CURLOPT_TIMEOUT] = ceil($timeout / 1000);
        }
        if ($method == self::HTTP_POST) {
            $curl_opts[CURLOPT_URL] = $url;
            $curl_opts[CURLOPT_POSTFIELDS] = $param_string;
        } else {
            $delimiter = strpos($url, '?') === false ? '?' : '&';
            $curl_opts[CURLOPT_URL] = $url . $delimiter . $param_string;
            $curl_opts[CURLOPT_POST] = false;
        }

        curl_setopt_array($ch, $curl_opts);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->set_error('default', 'curl error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }
        curl_close($ch);

        return $response;
    }

    /**
     * Extender classes should implement this interface
     *
     * @param string $response
     */
    abstract protected function parse_response($response);
}

/* vim: set ts=4 sw=4 sts=4 tw=90 noet: */
