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
$filter_order = @$this->state->filter_order;
$document = &JFactory::getDocument();

$count_product_to_row = $this->Config->get('productlist_products_x_row',3);
$itemspan = 12 / $count_product_to_row;
    
$js = array();
$js[] = "jQuery.noConflict();";
$js[] = "jQuery(document).ready(function() {";
$js[] = "   jQuery('#necbtndisplaygrid').click(function() {";
$js[] = "       jQuery('.necProductItems').animate({opacity:0},function(){";
// $js[] = "           jQuery('#necbtndisplaylist').removeClass('btn-success');";
// $js[] = "           jQuery('#necbtndisplaygrid').addClass('btn-success');";
// $js[] = "           jQuery('.necProductItems').removeClass('necListProductItems').addClass('necGridProductItems');";
// $js[] = "           jQuery('#necProductItems').find('ul.necProductItems li').each(function(i, e){";
// $js[] = "		jQuery(e).removeClass('span12').addClass('span".$itemspan."');";
// $js[] = "           });";
$js[] = "           jQuery('#filter_displaystyle').val('grid');";
$js[] = "           document.formfiltersnegocio.submit();";
// $js[] = "           jQuery('.necProductItems').stop().animate({opacity:1});";
$js[] = "       });";
$js[] = "   });";
$js[] = "   jQuery('#necbtndisplaylist').click(function() {";
$js[] = "       jQuery('.necProductItems').animate({opacity:0},function(){";
// $js[] = "           jQuery('#necbtndisplaygrid').removeClass('btn-success');";
// $js[] = "           jQuery('#necbtndisplaylist').addClass('btn-success');";
// $js[] = "           jQuery('.necProductItems').removeClass('necGridProductItems').addClass('necListProductItems');";
// $js[] = "           jQuery('#necProductItems').find('ul.necProductItems li').each(function(i, e){";
// $js[] = "		jQuery(e).removeClass('span".$itemspan."').addClass('span12');";
// $js[] = "           });";
$js[] = "           jQuery('#filter_displaystyle').val('list');";
$js[] = "           document.formfiltersnegocio.submit();";
// $js[] = "           jQuery('.necProductItems').stop().animate({opacity:1});";
$js[] = "       });";
$js[] = "   });";
$js[] = "});";

$document->addScriptDeclaration(implode("\n", $js));

$attribs = array('class' => 'inputbox', 'size' => '1', 'onchange' => 'document.formfiltersnegocio.submit();');
?>
<form action="<?= JRoute::_( @$this->form['action'] );?>" method="post" name="formfiltersnegocio" id="formfiltersnegocio">
    <div class="necProductFilter clearfix">
        <div class="necSortBy inline pull-left">
            <label id="lbl-order" class="inline"><?= JText::_( 'COM_JNEGOCIO_ORDER_BY');?></label>
            <?php echo HelperSelect::getProductListOrder( @$this->state->filter_order, 'list', 'filter_order', $attribs);?>
        </div>
        
        <div class="necShowLimit inline pull-left">
            <label id="lbl-limit" class="inline"><?= JText::_( 'COM_JNEGOCIO_LIMIT_NUMBER');?></label>
            <?php echo HelperSelect::getProductLimitBox( @$this->state->limit, 'list', 'limit', $attribs);?>
        </div>
        
        <div class="necDisplayBtn inline pull-right">
            <label id="lbl-display" class="inline"><?= JText::_( 'COM_JNEGOCIO_DISPLAY');?></label>
            <div class="btn-group">
                <a id="necbtndisplaygrid" class="btn <?php if( @$this->state->filter_displaystyle == 'grid') { echo 'btn-success';}?>"><i class="icon-th"></i></a>
                <a id="necbtndisplaylist" class="btn <?php if( @$this->state->filter_displaystyle == 'list') { echo 'btn-success';}?>"><i class="icon-list"></i></a>
            </div>
        </div>
    </div>
    
    <input type='hidden' name='filter_categoryid' value='<?= @$this->state->filter_categoryid;?>' />     
    <input type='hidden' name='filter_manufacturerid' value='<?= @$this->state->filter_manufacturerid;?>' />
    <input type='hidden' name='filter_groupid' value='<?= @$this->state->filter_groupid;?>' />
    <input type='hidden' id="filter_displaystyle" name='filter_displaystyle' value='<?= @$this->state->filter_displaystyle;?>' />
    <input type="hidden" name="limitstart" value="0" />
    <?=  @$this->form['validate'];?>
</form>