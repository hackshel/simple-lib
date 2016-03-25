<?php

/***************************************************************************
 *
 * Copyright (c) 2011 Baidu.com, Inc. All Rights Reserved
 *
 **************************************************************************/

/**
 * bdSMS.class.php
 * @brief 短信平台服务
 * @version	$Revision: 1.0 Mon Sep 24 06:36:59 GMT 2012
 **/

class bdSMS 
{
	
	const HTTP_GET = 'GET';
	const HTTP_POST = 'POST';
	
	protected $errno = 0;
	protected $errmsg = '';
	
	private static $instance = null;
	
	 /**
     * @return bdSMS
     */
	public static function getInstance()
	{
		if (self::$instance === null) {
			self::$instance = new bdSms();
		}
		return self::$instance;
	}
	
	public function __construct()
	{
		
	}
	
	protected function set_error($errno, $errmsg = '')
	{
		$this->errno = $errno;
		$this->errmsg = $errmsg;
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
	 * @brief 发送短信
	 * @modify yangqiteng@baidu.com add $scheduledDate
	 *
	 * @param string $msgDest 手机号
	 * @param string $msgContent 短信内容
	 * @param string|null $scheduledDate 发送时间 format 'yyyy-MM-dd hh:mm:ss'
	 * @return false|string
	 */
	public function sendSms($msgDest, $msgContent, $scheduledDate = null) {
		if (empty($msgDest) || empty($msgContent)) {
			$this->set_error('1', 'params is mull');
			return false;
		}
		$params['businessCode'] = bdSMSConfig::SMS_BUSINESS_CODE ;
		$params['msgDest'] = $msgDest;
		$params['msgContent'] = $msgContent;
		$params['username'] = bdSMSConfig::SMS_USER_NAME;
		$params['scheduledDate'] = $scheduledDate;
		$sign_str = bdSMSConfig::SMS_USER_NAME.bdSMSConfig::SMS_PASSWORD.$msgDest.$msgContent.bdSMSConfig::SMS_BUSINESS_CODE.$scheduledDate;
		$params['signature'] = md5($sign_str);
		$ret = $this->http_request(bdSMSConfig::SMS_SEND_SERVER , 'POST', $params, 1000, 1000);
		return $ret;
	}
	
	
	/**
	 * Send http request and get the response back
	 * 
	 * @param string $url		Target url
	 * @param string $method	Http method, 'GET' or 'POST'
	 * @param array $params		Query params or POST params
	 * @param int $connect_timeout	Connect timeout
	 * @param int $timeout			Http request timeout
	 * @return string|false		Returns the response content if success,
	 * 							or false if http request failed
	 */
	protected function http_request($url, $method, $params, $connect_timeout, $timeout)
	{
		$user_agent = sprintf('Baidu Phplib Client (PHP %s)', phpversion());
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
}

