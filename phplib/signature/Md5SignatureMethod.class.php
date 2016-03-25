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
class Md5SignatureMethod extends SignatureMethod 
{
	protected function do_sign($sign_data)
	{
        return md5($sign_data);
    }
}

?>
