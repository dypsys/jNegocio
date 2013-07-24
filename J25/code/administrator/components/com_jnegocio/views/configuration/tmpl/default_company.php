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
    <ul class="config-option-list">
        <li>
            <label for="company_name"><?php echo JText::_('COM_JNEGOCIO_CONFIG_COMPANY_NAME'); ?></label>
            <input type="text" value="<?php echo $this->row->company_name; ?>" id="company_name" name="company_name" />
        </li>

        <li>
            <label id="country_id-lbl" class="hasTip" title="<?= @JText::_( 'COM_JNEGOCIO_COUNTRY_NAME_LABEL' ); ?>::<?= @JText::_( 'COM_JNEGOCIO_COUNTRY_NAME_DESC' ); ?>" for="country_id">
		<?= @JText::_( 'COM_JNEGOCIO_COUNTRY_NAME_LABEL' ); ?>
            </label>
            <?= HelperSelect::countries( @$this->row->company_country, 'company_country', '', 'company_country', false, true );?>
        </li>        

        <li>
            <label for="company_zone"><?php echo JText::_('COM_JNEGOCIO_CONFIG_COMPANY_ZONE'); ?></label>
            <input type="text" value="<?php echo $this->row->company_zone; ?>" id="company_zone" name="company_zone" />
        </li>
        
        <li>
            <label for="company_codpostal"><?php echo JText::_('COM_JNEGOCIO_CONFIG_COMPANY_CITY'); ?></label>
            <input type="text" value="<?php echo $this->row->company_codpostal; ?>" id="company_codpostal" name="company_codpostal" size="6" maxlength="10" />
            <input type="text" value="<?php echo $this->row->company_city; ?>" id="company_city" name="company_city" />
        </li>

        <li>
            <label for="company_phone"><?php echo JText::_('COM_JNEGOCIO_CONFIG_COMPANY_PHONE'); ?></label>
            <input type="text" value="<?php echo $this->row->company_phone; ?>" id="company_phone" name="company_phone" />
        </li>
        
    </ul>
</div>