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
?>
<div class="width-100 margin1" >
    <div class="width-100 fltlft" >
        <ul class="config-option-list">
            <li>
                <label for="debug_mode"><?php echo JText::_('COM_JNEGOCIO_CONFIG_DEBUG_MODE'); ?></label>
                <?php echo jFWSelect::booleans($this->row->debug_mode, 'debug_mode', array('class' => 'inputbox', 'size' => '1'), null, false, '', 'COM_JNEGOCIO_OPTION_SI', 'COM_JNEGOCIO_OPTION_NO'); ?>
            </li>
        </ul>
    </div>
    <div class="width-49 fltlft" >
        <ul class="config-option-list">
            <li>
                <label for="debug_mode"><?php echo JText::_('COM_JNEGOCIO_CONFIG_LESS_BACKEND'); ?></label>
                <?php echo jFWSelect::booleans($this->row->less_admin, 'less_admin', array('class' => 'inputbox', 'size' => '1'), null, false, '', 'COM_JNEGOCIO_OPTION_SI', 'COM_JNEGOCIO_OPTION_NO'); ?>
            </li>

            <li>
                <label for="debug_mode"><?php echo JText::_('COM_JNEGOCIO_CONFIG_LOAD_JQUERY_BACKEND'); ?></label>
                <?php echo jFWSelect::booleans($this->row->loadjquey_admin, 'loadjquey_admin', array('class' => 'inputbox', 'size' => '1'), null, false, '', 'COM_JNEGOCIO_OPTION_SI', 'COM_JNEGOCIO_OPTION_NO'); ?>
            </li>
        </ul>
    </div>
    <div class="width-49 fltrt" >
        <ul class="config-option-list">
            <li>
                <label for="debug_mode"><?php echo JText::_('COM_JNEGOCIO_CONFIG_LESS_FRONTEND'); ?></label>
                <?php echo jFWSelect::booleans($this->row->less_frontend, 'less_frontend', array('class' => 'inputbox', 'size' => '1'), null, false, '', 'COM_JNEGOCIO_OPTION_SI', 'COM_JNEGOCIO_OPTION_NO'); ?>
            </li>
            <li>
                <label for="debug_mode"><?php echo JText::_('COM_JNEGOCIO_CONFIG_LOAD_JQUERY_FRONTEND'); ?></label>
                <?php echo jFWSelect::booleans($this->row->loadjquey_frontend, 'loadjquey_frontend', array('class' => 'inputbox', 'size' => '1'), null, false, '', 'COM_JNEGOCIO_OPTION_SI', 'COM_JNEGOCIO_OPTION_NO'); ?>
            </li>
        </ul>
    </div>
    
    <div class="width-49 fltlft" >
        <ul class="config-option-list">
            <li>
                <label for="debug_mode"><?php echo JText::_('COM_JNEGOCIO_CONFIG_CURRENCY'); ?></label>
                <?= HelperSelect::currencies( @$this->row->default_currencyid, 'default_currencyid', '', 'default_currencyid', false, true );?>
            </li>
        </ul>
    </div>
</div>