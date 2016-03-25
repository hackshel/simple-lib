<?php
/***************************************************************************
 * 
 * Copyright (c) 2009 Baidu.com, Inc. All Rights Reserved
 * 
 **************************************************************************/

require_once('SignatureMethod.class.php');
/**
 * 使用SHA1摘要算法进行签名
 */
class SHA1SignatureMethod extends SignatureMethod 
{
	protected function do_sign($sign_data)
	{
        return sha1($sign_data);
    }
}

?>