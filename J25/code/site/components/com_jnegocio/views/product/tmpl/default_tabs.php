<?php
/**
 * @version	$Id$
 * @package	Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.pane');

$tabs = & JPane::getInstance('Tabs');
echo $tabs->startPane('productPane');

echo $tabs->startPanel( JText::_('COM_JNEGOCIO_DETAILS') , 'product-details');
echo htmlspecialchars_decode($this->row->description);
echo $tabs->endPanel();

echo $tabs->endPane();
?>
