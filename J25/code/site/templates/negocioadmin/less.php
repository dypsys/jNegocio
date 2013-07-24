<?php
/**
 * @package     Joomla.Templates
 * @subpackage  Templates.cesiadm
 * @copyright   Copyright (C) 2005 - 2013 CESI Informàtica i comunicions. All rights reserved.
 * @license     Comercial License
 */
defined('_JEXEC') or die;

require_once('lessc.inc.php');

function autoCompileLess($inputFile, $outputFile) {
	// load config file
	$configFile = JPATH_BASE . DIRECTORY_SEPARATOR . 'configuration.php';
	$config = JFactory::getConfig($configFile);
	//path to temp folder
	$tmpPath = $config->get('tmp_path');
	//get Application
	$app = JFactory::getApplication();
	$templateparams = $app->getTemplate(true)->params;
	
	//load chached file
	$cacheFile = $tmpPath . DIRECTORY_SEPARATOR . $app->getTemplate() . "_" . basename($inputFile) . ".cache";

	if (file_exists($cacheFile)) {
		$cache = unserialize(file_get_contents($cacheFile));
	} else {
		$cache = $inputFile;
	}
	
	$less = new lessc;
	//set less options
	
	//force recompilation regardless of change 
	$force = (boolean) $templateparams->get('less_force', 0);
	
	//preserve comments
	if($templateparams->get('less_comments', 0))
	{
		$less->setPreserveComments(true);
	}
	
	//compression 
	if($templateparams->get('less_compress', 0)) {
		$less->setFormatter("compressed");
	} else {
		$formatter = new lessc_formatter_classic;
		$formatter->disableSingle = true;
		$formatter->breakSelectors = true;
		$formatter->assignSeparator = ": ";
		$formatter->selectorSeparator = ",";
		$formatter->indentChar = "\t";
	}
	
	$newCache = $less->cachedCompile($cache, $force);

	if (!is_array($cache) || $newCache["updated"] > $cache["updated"]) {
		file_put_contents($cacheFile, serialize($newCache));
		file_put_contents($outputFile, $newCache['compiled']);
	}
}