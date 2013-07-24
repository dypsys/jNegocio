<?php
/**
* @version		$Id$
* @package		Joomla
* @subpackage	jNegocio
* @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
* @license		Comercial License
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if ( !class_exists('lessc') ) {
    require_once( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_jnegocio' .DS. 'library' .DS. 'lessphp' .DS. 'lessc.inc.php' );
}

Class jFWLess
{
	function autoCompile($inputFile, $outputFile, $configless = array() ) {
		// load config file
		$configFile = JPATH_BASE . DIRECTORY_SEPARATOR . 'configuration.php';
		$config = JFactory::getConfig($configFile);
		//path to temp folder
		$tmpPath = $config->get('tmp_path');
		//get Application
		$app = JFactory::getApplication();
		
		//force recompilation regardless of change
		$cfg_ForceCreate		= isset($options['ForceCreate']) 		? $options['ForceCreate'] 		: true;
		$cfg_PreserveComments 	= isset($options['PreserveComments']) 	? $options['PreserveComments'] 	: false;
		$cfg_Compress 			= isset($options['Compress']) 			? $options['Compress'] 			: false;
		
		//load chached file
		$cacheFile = $tmpPath . DIRECTORY_SEPARATOR . $app->getTemplate() . "_" . basename($inputFile) . ".cache";
		
		if (file_exists($cacheFile)) {
			$cache = unserialize(file_get_contents($cacheFile));
		} else {
			$cache = $inputFile;
		}
		
		$less = new lessc;
		//set less options
				
		//preserve comments
		if($cfg_PreserveComments) { $less->setPreserveComments(true); }
		
		//compression
		if($cfg_Compress) {
			$less->setFormatter("compressed");
		} else {
			$formatter = new lessc_formatter_classic;
			$formatter->disableSingle = true;
			$formatter->breakSelectors = true;
			$formatter->assignSeparator = ": ";
			$formatter->selectorSeparator = ",";
			$formatter->indentChar = "\t";
		}
		
		$newCache = $less->cachedCompile($cache, $cfg_ForceCreate);
		
		if (!is_array($cache) || $newCache["updated"] > $cache["updated"]) {
			file_put_contents($cacheFile, serialize($newCache));
			file_put_contents($outputFile, $newCache['compiled']);
		}
	}
}