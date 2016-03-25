<?php
/***************************************************************************
 * 
 * Copyright (c) 2009 Baidu.com, Inc. All Rights Reserved
 * 
 **************************************************************************/

/**
 * Resouce Factory to get instances of some resouce.
 * 
 * @package utils
 * @author zhujt(zhujianting@baidu.com)
 * @version v1.0.0
 **/
class ResourceFactory
{
	/**
	 * Smarty instance
	 * @var Smarty
	 */
	private static $smarty = null;

	public static function getSmartyInstance()
	{
		if (self::$smarty === null) {
			$smarty = new Smarty();
			$smarty->setTemplateDir(SMARTY_TEMPLATE_DIR);
			$smarty->setCompileDir(SMARTY_COMPILE_DIR);
			$smarty->setConfigDir(SMARTY_CONFIG_DIR);
			$smarty->setCacheDir(SMARTY_CACHE_DIR);
			$smarty->addPluginsDir(SMARTY_PLUGIN_DIR);
			$smarty->left_delimiter = '{{';
			$smarty->right_delimiter = '}}';
			
			self::$smarty = $smarty;
		}
		
		return self::$smarty;
	}
}



/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */