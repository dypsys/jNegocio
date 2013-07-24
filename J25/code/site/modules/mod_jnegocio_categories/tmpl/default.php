<?php
/**
* @version		$Id$
* @package		Joomla
* @subpackage	jInmo
* @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
* @license		Comercial License
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$document = &JFactory::getDocument();

$document->addScript(JURI::base(true) . '/modules/mod_jnegocio_categories/assets/js/jquery.dcjqaccordion.js');
$document->addStyleSheet(JURI::base(true) . '/modules/mod_jnegocio_categories/assets/css/style.css', 'text/css' ); 	// original Flexslider CSS

$script = array();
$script[] = "jQuery(document).ready(function(jQuery){";
$script[] = "jQuery('#mod_jnegocio_categories_".$uid."').dcAccordion({";
// $script[] = "classExpand : 'cid59',";
$script[] = "menuClose: false,";
$script[] = "autoClose: true,";
$script[] = "saveState: false,";
$script[] = "disableLink: false,";
$script[] = "autoExpand: true ";
$script[] = "});";
$script[] = "});";

$document->addScriptDeclaration(implode("\n", $script));

$lfirt=true;
$nlevel=2;
?>
<ul class="mod_jnegocio_categories" id="mod_jnegocio_categories_<?php echo $uid ?>">
    <?php 
    foreach($list as $category) {
        if ($category->level==$nlevel) {
            if ($lfirt) { $lfirt=false; } else { echo "</li>\n"; }
        } elseif ($category->level>$nlevel) {
            echo "<span class='expand'></span>";
            echo "<ul>\n";
        } elseif($category->level<$nlevel) {
            for($n=$nlevel; $n>$category->level; $n--) {
                echo "</li>\n</ul>\n"; 
            }
        }
        
	$nlevel = $category->level;
	$stropen = '';
	if (@$active_category_id ) {        
//            if ((@$active_category_id == $category->id) || (strpos(@$active_category->path_sort, $category->path_sort) === 0)) {
//		$stropen = "open: true;";
//            }
        }
        $link = JRoute::_('index.php?option=com_jnegocio&view=products&layout=category&filter_categoryid='.$category->category_id);
        echo "<li class='cid".$category->category_id."'><a href='".$link."'>".$category->name."</a>\n";
    }
    echo "</li>\n";
    for($n=$nlevel; $n>1; $n--) {
	echo "</ul>\n";
    }
    ?>
</ul>