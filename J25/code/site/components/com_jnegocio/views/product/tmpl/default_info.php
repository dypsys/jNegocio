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
<h4 class="nectitle necdoubledotted"><?= @$this->row->name ?></h4>

<div class="product-shortdesc necdoubledotted clearfix">
    <?= @$this->row->shortdesc ?>
</div>

<div class="product-price necdoubledotted clearfix">
    <div class="necPriceCnt necPriceCnt">
        <label class="necPrice"><?= JText::_('COM_JNEGOCIO_PRODUCT_BRUTO_LABEL'); ?></label>
        <span class="necPrice"><?= @$this->row->f_pp_bruto; ?></span>
    </div>

    <?php if ($this->row->pp_totaldto > 0) : ?>
        <div class="necPriceCnt necOldPriceCnt">
            <label class="necPrice"><?= JText::_('COM_JNEGOCIO_PRODUCT_DISCOUNT_LABEL'); ?></label>
            <span class="necPrice"><?= @$this->row->f_pp_discount; ?></span>    
        </div>
        <div class="necPriceCnt necOldPriceCnt">
            <label class="necPrice"><?= JText::_('COM_JNEGOCIO_PRODUCT_TOTALDISCOUNT_LABEL'); ?></label>
            <span class="necPrice"><?= @$this->row->f_pp_totaldto; ?></span>    
        </div>
    <?php endif; ?>

    <div class="necPriceCnt necPriceCnt">
        <label class="necPrice"><?= JText::_('COM_JNEGOCIO_PRODUCT_NETO_LABEL'); ?></label>
        <span class="necPrice"><?= @$this->row->f_pp_neto; ?></span>
    </div>

    <div class="necPriceCnt necPriceCnt">
        <label class="necPrice"><?= JText::_('COM_JNEGOCIO_PRODUCT_TAXRATE_LABEL'); ?></label>
        <span class="necPrice"><?= @$this->row->f_taxrate; ?></span>
    </div>

    <div class="necPriceCnt necPriceCnt">
        <label class="necPrice"><?= JText::_('COM_JNEGOCIO_PRODUCT_TOTALTAXRATE_LABEL'); ?></label>
        <span class="necPrice"><?= @$this->row->f_pp_ratetotal; ?></span>
    </div>

    <div class="necPriceCnt necPriceCnt">
        <label class="necPrice"><?= JText::_('COM_JNEGOCIO_PRODUCT_TOTAL_LABEL'); ?></label>
        <span class="necPrice"><?= @$this->row->f_pp_total; ?></span>
    </div>
</div>

<div class="product-rate necdoubledotted clearfix">
    <ul class="rating">
        <li><i class="star-on"></i></li>
        <li><i class="star-on"></i></li>
        <li><i class="star-on"></i></li>
        <li><i class="star-off"></i></li>
        <li><i class="star-off"></i></li>
    </ul>
    <span>18 Review(s) <a href="#">Make a Review</a></span>
</div>

<div class="product-info">
    <dl class="dl-horizontal">
        <dt><?= JText::_('COM_JNEGOCIO_AVARLABILTY_LABEL'); ?>:</dt>
        <dd>Available In Stock</dd>

        <dt><?= JText::_('COM_JNEGOCIO_PRODUCT_CODE_LABEL'); ?>:</dt>
        <dd>No. CtAw9458</dd>

        <dt><?= JText::_('COM_JNEGOCIO_MANUFACTURER_LABEL'); ?>:</dt>
        <dd>Nicka Corparation</dd>
    </dl>
</div>

<div class="product-inputs">
    <form action="page" method="post">
        <div class="input-append">
            <input type="text" placeholder="QTY" value="" name="" class="span2">
            <button class="btn btn-primary"><i class="icon-shopping-cart"></i> Add To Cart</button>
        </div>
    </form>
</div>