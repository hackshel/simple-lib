<?php
/***************************************************************************
 * 
 * Copyright (c) 2009 Baidu.com, Inc. All Rights Reserved
 * 
 **************************************************************************/
/**
 * 各种签名方法 
 * 
 * @package	signature
 * @version	$Revision: 1.1 $
 */
abstract class SignatureMethod
{
		
	//定义签名算法
	/**
	 * MD5算法
	 */
	const SIGN_METHOD_MD5	= 1;
	/**
	 * SHA1算法
	 */
	const SIGN_METHOD_SHA1	= 2;
	/**
	 * 由子类重写, 实现具体的签名算法.
	 * 
	 * @param string $sign_data	待签名的字符串
	 * @return string|bool	如果签名成功 返回签名结果 否则返回false
	 */
	protected function do_sign($sign_data)
    {
        //避免zend编译器下的黄线警告
    	is_null($sign_data);
    	return false;
    }
    
    /**
     * 对传入的参数进行签名
     * 
     * @param array $sign_params	待签名的参数数组
     * @param string $key			签名时使用的密钥
     * @return string|bool	如果签名成功 返回签名结果 否则返回false
     */
    public function sign($sign_params, $key)
    {
        $sign_data = $this->generate_sign_data($sign_params, $key);
        //do_sign应该由子类实现
        return $this->do_sign($sign_data);
    }
    
    /**
     * 对传入的数组进行验签
     * 签名结果不区分大小写
     * @param array $sign_params	待验签的参数数组
     * @param string $expect_sign	期望的验签结果
     * @param string $key			验签时使用的密钥
     * @return bool	通过 true 不通过 false
     */
    public function validate_sign($sign_params, $expect_sign, $key)
    {
        $sign_data = $this->generate_sign_data($sign_params, $key);

        //do_sign应该由子类实现
        $sign = $this->do_sign($sign_data);

        return 0 == strcasecmp($sign, $expect_sign);
    }
    
    /**
     * 根据待签名的参数数组, 生成待签名的字符串
     * @param array $sign_params	待签名的参数数组
     * @param string $key			签名时使用的密钥
     * @return string 待签名的字符串
     */
    private function generate_sign_data($sign_params, $key)
    {
        $sign_data = '';
        //ksort($sign_params); 此处用ksort
        foreach ($sign_params as $name => $value)
        {
            $sign_data .= $name . '=' . $value . '&';
        }
        $sign_data .= "key=$key";
        
        return $sign_data;
    }
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
?>
