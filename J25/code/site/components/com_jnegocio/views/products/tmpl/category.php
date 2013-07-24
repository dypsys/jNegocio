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

$state = @$this->state;
?>
<div class="necShop">
<?php
// Mostrar Header h2
?>
    <div class="necShop_list_category">
        <?php
        if (count(@$this->categories)>=1) {
            foreach(@$this->categories as $kcat => $category) {
                
            }
        }
        ?>
    </div>
    <div class="necShop_list_products">
        <?php 
        include(dirname(__FILE__)."/filters.php");
        if (count(@$this->rows)>=1) {
            include(dirname(__FILE__)."/list_products.php");
        } else { 
            ?><br/><br/><center><?php echo JText::_( 'COM_JNEGOCIO_NO_ITEMS_FOUND' ); ?></center><br/><br/><?php 
        }
        include(dirname(__FILE__)."/pagination.php");
        ?>
    </div>
</div>