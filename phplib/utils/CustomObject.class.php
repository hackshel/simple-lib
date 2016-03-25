<?php
/***************************************************************************
 * 
 * Copyright (c) 2009 Baidu.com, Inc. All Rights Reserved
 * $Id: CustomObject.class.php,v 1.1 2010/01/01 10:20:41 zhujt Exp $ 
 * 
 **************************************************************************/
 
/**
 * @file /home/zhujt/app/search/space/phplib/class/CustomObject.class.php
 * @author zhujt(zhujianting@baidu.com)
 * @date 2009/03/03 12:12:55
 * @version $Revision: 1.1 $ 
 * @brief 
 *  
 **/

class CustomObject
{
	public function __construct($vals, $cascade = true)
	{
		if( is_array($vals) && key($vals) !== 0 )
		{
			foreach( $vals as $key => $val )
			{
				$this->{$key} = self::convert2CustomObject($val, $cascade);
			}
		}
	}

	private static function convert2CustomObject($vals, $cascade = true)
	{
		//if $vals is not an array, just return it
		if( !is_array($vals) || $cascade === false )
		{
			return $vals;
		}

		if( key($vals) !== 0 )	//associat array
		{
			return new CustomObject($vals, true);
		}
		else	//nonassociat array
		{
			$arrRet = array();
			foreach( $vals as $item )
			{
				$arrRet[] = self::convert2CustomObject($item);
			}
			return $arrRet;
		}
	}
}




/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
?>
