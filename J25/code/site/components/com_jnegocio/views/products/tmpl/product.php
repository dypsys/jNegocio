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

$linkProduct = JRoute::_( 'index.php?option=' . jFWBase::getComponentName() . '&view=product&id=' . $product->product_id );
?>
<div class="thumbnail">
    <a class="neclink inverseColor" href="<?= @$linkProduct;?>">
        <img class="necprdimg" src="<?= JURI::root() . $product->gl_location_url . 'list/' . $product->gl_attachment; ?>" alt="" title="" onerror="this.src='<?= jFWBase::getURL('images', false) . 'default/212x192.jpg';?>'">
    </a>

<div class="thumbSetting">
    <div class="thumbTitle">
        <h3>
            <a class="neclink inverseColor" href="<?= @$linkProduct;?>"><?= @$product->name;?></a>
        </h3>
    </div>
    <div class="thumbDesc"><?= @$product->shortdesc;?></div>
    <div class="thumbPrice">
        <?php if (@$product->pp_totaldto>0) { ?>
            <div class="necPriceCnt necOldPriceCnt">
                <?php if ($this->Config->get('work_pricewithtax',1) == 1) { ?>
                    <label class="necPrice"><?= JText::_( 'COM_JNEGOCIO_PRODUCT_OLDPRICEWITHTAX_LABEL');?></label>
                    <span class="necPrice"><?= @HelperCurrency::format( @$product->pp_total );?></span>
                <?php } else { ?>
                    <label class="necPrice"><?= JText::_( 'COM_JNEGOCIO_PRODUCT_OLDPRICE_LABEL');?></label>
                    <span class="necPrice"><?= @HelperCurrency::format( @$product->pp_total );?></span>            
                <?php } ?>            
            </div>
        <?php } ?>
        <div class="necPriceCnt">
            <?php if ($this->Config->get('work_pricewithtax',1) == 1) { ?>
                <label class="necPrice"><?= JText::_( 'COM_JNEGOCIO_PRODUCT_PRICEWITHTAX_LABEL');?></label>
                <span class="necPrice"><?= @HelperCurrency::format( @$product->p_priceincltax );?></span>
            <?php } else { ?>
                <label class="necPrice"><?= JText::_( 'COM_JNEGOCIO_PRODUCT_PRICE_LABEL');?></label>
                <span class="necPrice"><?= @HelperCurrency::format( @$product->p_price );?></span>            
            <?php } ?>
        </div>
    </div>
    <div class="thumbButtons">
        <a href="#" class="necbtnaddcar btn btn-primary btn-small"><i class="icon-shopping-cart"></i> <?= JText::_( 'COM_JNEGOCIO_ADD_TO_CART');?></a>
        <a href="<?= @$linkProduct;?>" class="necbtndetail btn btn-success btn-small"><i class="icon-file"></i> <?= JText::_( 'COM_JNEGOCIO_DETAILS');?></a>
    </div>
</div>
</div>