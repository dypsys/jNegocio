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

$style = @$this->state->filter_displaystyle;
$ncont = 0;
$count_product_to_row = $this->Config->get('productlist_products_x_row',3);
if( @$this->state->filter_displaystyle == 'grid' ) {
    $itemspan = 12 / $count_product_to_row;
} else {
    $itemspan = 12;
}

if ($style == 'list') {
    $classStyle = 'necListProductItems';
} else {
    $classStyle = 'necGridProductItems';
}

echo '<div id="necProductItems">';
foreach(@$this->rows as $kprod => $product) {
    if ($ncont%$count_product_to_row==0) {
        if ($ncont!=0) {echo '</ul>';} 
        echo '<ul class="necProductItems '.$classStyle.' clearfix">';
    }
    echo '<li class="span'. $itemspan . ' clearfix">';
    include(dirname(__FILE__)."/product.php");
    echo '</li>';
    $ncont++; 
}
if ($ncont%$count_product_to_row!=$count_product_to_row-1) print "</ul>";
echo '</div>';
?>
