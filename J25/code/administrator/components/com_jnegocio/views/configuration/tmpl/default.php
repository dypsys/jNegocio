<?php
/**
 * @version	$Id$
 * @package	Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 * 
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jFWBase::load('HelperSelect', 'helpers.select');

jimport('joomla.html.pane');
?>
<form action="<?= @JRoute::_($this->action); ?>" method="post" name="adminForm" enctype="multipart/form-data">
    <?php
    $pane = & JPane::getInstance('Tabs');
    echo $pane->startPane('jNegocioConfig');
    
    echo $pane->startPanel(JText::_('COM_JNEGOCIO_GENERAL'), 'jnegocio-general-page');
    echo $this->loadTemplate('general');
    echo $pane->endPanel();

    echo $pane->startPanel(JText::_('COM_JNEGOCIO_CONFIG_COMPANY'), 'jnegocio-company-page');
    echo $this->loadTemplate('company');
    echo $pane->endPanel();
    
    echo $pane->endPane();
    
    echo JHTML::_('form.token'); 
    ?>
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="option" value="<?= jFWBase::getComponentName(); ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->_name; ?>" />
    <input type="hidden" name="view" value="<?php echo $this->_name; ?>" />
    <input type="hidden" name="task" id="task" value="" />
    <input type="hidden" name="tmpl" value="<?php echo $this->tmpl; ?>" />
</form>
<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>