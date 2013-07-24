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
<div id="productsattributes_container">
    <table class="adminlist" id="productattributes_table">
        <header>
            <tr>
                <th><?= @JText::_('COM_JNEGOCIO_PRODUCT_ATTRIBUTE_NAME'); ?></th>
                <th><?= @JText::_('COM_JNEGOCIO_PRODUCT_ATTRIBUTE_VALUE'); ?></th>
                <th><?= @JText::_('COM_JNEGOCIO_PRODUCT_SKU'); ?></th>
                <th colspan="2"><?= @JText::_('COM_JNEGOCIO_PRODUCT_ATTRIBUTE_PRICE'); ?></th>
                <th><?= @JText::_('COM_JNEGOCIO_PRODUCT_STOCK'); ?></th>
                <th><?= @JText::_('COM_JNEGOCIO_PRODUCT_WEIGHT'); ?></th>
                <th><?= @JText::_('COM_JNEGOCIO_ORDER'); ?></th>
                <th></th>
            </tr>
        </header>
        <body>
            <?php
            $k = 1;
            $attrcont = 0;
            for ($attrcont = 0, $attrtotal = count($this->attributes_rows); $attrcont < $attrtotal; $attrcont++) {
                $RowAttr = $this->attributes_rows[$attrtotal];
                $RelAttr = $attrcont+1;
                ?>
                <tr class="row<?= @$k; ?>" rel="<?= @$RelAttr;?>">
                    <td>
                        <input type="hidden" id="pa_id_<?= @$RelAttr;?>" name="prdattr[<?= @$RelAttr;?>][productattribute_id]" value="<?= @$RowAttr->productattribute_id;?>" />
                        <input type="hidden" id="pa_deleted_<?= @$RelAttr;?>" name="prdattr[<?= @$RelAttr;?>][deleted]" value="0" />
                    </td>
                    
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    
                    <td>
                        <a class="pa_delete_node" id="pa_delete_action_<?= @$RelAttr;?>" href="#" rel="<?= @$RelAttr;?>">
                            <img src="<?= @jFWBase::getURL('icons');?>16/remove.png" border="0" alt="<?= @JText::_('COM_JNEGOCIO_REMOVE'); ?>'" />
                        </a>
                    </td>
                </tr>
                <?php
                $k = 1 - $k;
            }
            ?>
        </body>
        <tfoot>
            <tr>
                <td>
                    <?php 
                    $html = '';
                    $attrCont = 0;
                    foreach(@$this->attributes as $att) {
                        $html .= '<span class="attribute_name">';
                        $html .= '<input type="hidden" id="pa_tmp_attr_id_' . $att->attribute_id .'" name="pa_tmp_attr_id[]" value="'.$att->attribute_id.'" />';
                        $html .= $att->name;
                        $html .= "</span>";
                        $html .='<div class="clr"></div>';
                        $attrCont++;
                    }
                    $html .= '<input type="hidden" id="pa_tmp_total_attr" name="pa_tmp_total_attr" value="'.$attrCont.'" />';
                    echo $html;
                    ?>
                </td>
                
                <td>
                    <?php
                    $attrCont = 0;
                    foreach(@$this->attributes as $att) {
                        echo '<span class="attribute_value">';
                        echo HelperSelect::attributesvalues($att->attribute_id, null, 'pa_tmp_attrvalue_'.$attrCont, array('class' => 'inputbox', 'size' => '1'), 'pa_tmp_attrvalue_'.$attrCont, true);
                        echo '</span>';
                        echo '<div class="clr"></div>';
                        $attrCont++;
                    }
                    ?>
                </td>
                
                <td>
                    <input class="inputbox" type="text" name="pa_tmp_sku" id="pa_tmp_sku" value="" size="10" maxlength="250" title="<?= @JText::_('COM_JNEGOCIO_PRODUCT_SKU' ); ?>" placeholder="<?= @JText::_('COM_JNEGOCIO_PRODUCT_SKU' ); ?>" />
                </td>
                
                <td>
                    <?php echo HelperSelect::priceprefix( '0', 'pa_tmp_priceprefix', array('class' => 'inputbox', 'size' => '1'), 'pa_tmp_priceprefix', false); ?>
                </td>
                
                <td>
                    <input type="text" id="pa_tmp_price" name="pa_tmp_price" class="inputbox classPrice" value="0" />
                </td>
                
                <td>
                    <input type="text" id="pa_tmp_stock" name="pa_tmp_stock" class="inputbox classQuantity" value="0" />
                </td>

                <td>
                    <input type="text" id="pa_tmp_peso" name="pa_tmp_stock" class="inputbox classQuantity" value="0" />
                </td>
                
                <td></td>
                <td>
                    <a class="nec_btn" onclick="Joomla.submitbutton('apply')"><?= @JText::_('COM_JNEGOCIO_ADD'); ?></a>
                </td>
            </tr>
        </tfoot>
    </table>
</div>