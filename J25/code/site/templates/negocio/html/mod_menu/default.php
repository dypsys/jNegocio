<?php
/**
 * @version		$Id: default.php 22355 2011-11-07 05:11:58Z github_bot $
 * @package		Joomla.Site
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

$nav_dropdown = 1;

echo '<ul class="nav' . $class_sfx.'"';
	$tag = '';
	if ($params->get('tag_id')!=NULL) {
		$tag = $params->get('tag_id').'';
		echo ' id="'.$tag.'"';
	}
echo '>';

foreach ($list as $i => &$item) :
	$class = 'item-'.$item->id;
	if ($item->id == $active_id) {
		$class .= ' current';
	}

	if ( $item->type == 'alias' &&
			in_array($item->params->get('aliasoptions'),$path)
		||	in_array($item->id, $path)) {
		$class .= ' active';
	}

	if (($item->deeper) && ($nav_dropdown)) {
		$class .= ' dropdown';
	} elseif ($item->deeper) {
		$class .= ' deeper';
	}
	
	if ($item->parent) {
		$class .= ' parent';
	}

	if (!empty($class)) {
		$class = ' class="'.trim($class) .'"';
	}

	echo '<li'.$class.'>';

	// Render the menu item.
	switch ($item->type) :
		case 'separator':
		case 'url':
		case 'component':
			require JModuleHelper::getLayoutPath('mod_menu', 'default_'.$item->type);
			break;

		default:
			require JModuleHelper::getLayoutPath('mod_menu', 'default_url');
			break;
	endswitch;

	// The next item is deeper.
	if (($item->deeper) && ($nav_dropdown)) {
		if ($item->level < 2) {
			echo '<ul class="dropdown-menu">';
		} else {
			echo '<ul class="flyout-menu">';
		}
	} elseif ($item->deeper) {
		echo '<ul>';
	} elseif ($item->shallower) {
		// 	The next item is shallower.
		echo '</li>';
		echo str_repeat('</ul></li>', $item->level_diff);
	} else {
		// The next item is on the same level.
		echo '</li>';
	}
endforeach;
echo '</ul>';