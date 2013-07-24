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
<div class="necShop">
<?php
// Mostrar Header h2
?>
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