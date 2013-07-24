<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_languages
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
// JHtml::_('stylesheet', 'mod_languages/template.css', array(), true);

$htmllist = '';
$htmlactive = '';
foreach($list as $language) {
	if ($language->active) {
		if ($params->get('image', 1)) {
			$htmlactive .= JHtml::_('image', 'mod_languages/'.$language->image.'.gif', $language->title_native, array('title'=>$language->title_native), true);		
		} else {
			$htmlactive .= $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef);
		} 
	}
	$htmllist .= "<li><a href='".$language->link."'>";
//	if ($params->get('image', 1)) {
		$htmllist .= JHtml::_('image', 'mod_languages/'.$language->image.'.gif', $language->title_native, array('title'=>$language->title_native), true);		
//	} else {
		$htmllist .= "&nbsp;";
                $htmllist .= $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef);
//	}
	$htmllist .= "</a></li>";
}
?>
<div class="mod-languages<?php echo $moduleclass_sfx ?>">
	<div class="btn-group">
		<button class="btn btn-small dropdown-toggle" data-toggle="dropdown"><?php print $htmlactive;?> <span class="caret"></span></button>
		<ul class="dropdown-menu language">
			<?php print $htmllist;?>
		</ul>
	</div>
</div>
