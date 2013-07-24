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

?>
<fieldset class="adminform">
    <ul class="adminformlist">
        <li>
            <label id="sku-lbl" class="hasTip" title="<?= @JText::_('COM_JNEGOCIO_PRODUCT_SKU_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_PRODUCT_SKU_DESC'); ?>" for="sku">
                <?= @JText::_('COM_JNEGOCIO_PRODUCT_SKU_LABEL'); ?>
            </label>
            <input class="inputbox" type="text" name="sku" id="sku" value="<?= @$this->row->sku; ?>" size="30" maxlength="64" />
        </li>
        <li>
            <label id="ean-lbl" class="hasTip" title="<?= @JText::_('COM_JNEGOCIO_PRODUCT_EAN_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_PRODUCT_EAN_DESC'); ?>" for="ean">
                <?= @JText::_('COM_JNEGOCIO_PRODUCT_EAN_LABEL'); ?>
            </label>
            <input class="inputbox" type="text" name="ean" id="sku" value="<?= @$this->row->ean; ?>" size="30" maxlength="64" />
        </li>
        
        <li>
            <label id="category-lbl" class="hasTip" title="<?= @JText::_('COM_JNEGOCIO_PRODUCT_CATEGORY_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_PRODUCT_CATEGORY_DESC'); ?>" for="category">
                <?= @JText::_('COM_JNEGOCIO_PRODUCT_CATEGORY_LABEL'); ?>
            </label>
            <?= HelperSelect::category( @$this->categories_select, 'categories[]', array('class' => 'inputbox', 'size' => '10', 'multiple' => 'multiple' ) , 'category', false );?>
        </li>
        
        <li>
            <label id="manufacturer-lbl" class="hasTip" title="<?= @JText::_('COM_JNEGOCIO_MANUFACTURER_NAME_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_MANUFACTURER_NAME_DESC'); ?>" for="manufacturer">
                <?= @JText::_('COM_JNEGOCIO_MANUFACTURER_NAME_LABEL'); ?>
            </label>
            <?= HelperSelect::manufacturers( @$this->row->manufacturer_id , 'manufacturer_id ', '', 'manufacturer_id ', true, true );?>
        </li>        
    </ul>
</fieldset>