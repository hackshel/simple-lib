<?php
/***************************************************************************
 * 
 * Copyright (c) 2009 Baidu.com, Inc. All Rights Reserved
 * 
 **************************************************************************/

require_once('SignatureMethod.class.php');

/**
 * 使用MD5摘要算法进行签名
 * @package	signature
 * @version	$Revision: 1.1 $
 */
class SignatureMethodFactory
{
	/**
	 * 根据给定的签名算法, 创建对应的SignMethod子类对象.
	 * 目前仅支持MD5和SHA1两种摘要算法
	 * 
	 * @param int $sign_method	签名算法
	 * @return SignatureMethod 如果给定的签名算法是被支持的,则返回对应签名对象; 否则, 返回false.
	 */
	public static function getInstance($sign_method)
	{
		if (SignatureMethod::SIGN_METHOD_MD5 == $sign_method)
		{
			require_once 'Md5SignatureMethod.class.php';
			return new Md5SignatureMethod();
		}
		elseif (SignatureMethod::SIGN_METHOD_SHA1 == $sign_method)
		{
			require_once 'SHA1SignatureMethod.class.php';
			return new SHA1SignatureMethod();
		}
		//不支持的sign_method
		return false;
	}
}
?>
