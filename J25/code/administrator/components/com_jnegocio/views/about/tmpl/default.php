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

jimport('joomla.html.pane');

$tabs	= & JPane::getInstance('tabs');
echo $tabs->startPane("tab-jec-about");
echo $tabs->startPanel( JText::_( 'COM_JNEGOCIO_ABOUT_INFORMATION' ), 'tab-item-about' );
?>
<ul class="jec_about">
	<li>
		<label><?php echo JText::_('COM_JNEGOCIO_ABOUT_NAME'); ?></label> 
		<?php echo JText::_($this->manifest->getManifest()->name);?>
	</li>
	<li>
		<label><?php echo JText::_('COM_JNEGOCIO_ABOUT_AUTHOR'); ?></label> 
		<?php echo $this->manifest->getManifest()->author;?>
	</li>
	<li>
		<label><?php echo JText::_('COM_JNEGOCIO_ABOUT_COPYRIGHT'); ?></label> 
		<?php echo $this->manifest->getManifest()->copyright;?>
	</li>
	<li>
		<label><?php echo JText::_('COM_JNEGOCIO_ABOUT_LICENCE'); ?></label>
		<?php echo $this->manifest->getManifest()->license;?>
	</li>
</ul>
<?php 
echo $tabs->endPanel();
echo $tabs->endPanel();
echo $tabs->startPanel( JText::_( 'COM_JNEGOCIO_ABOUT_CHANGELOG' ), 'tab-jec-changelog' );
echo $this->manifest->changelogs();
echo $tabs->endPane();
?>