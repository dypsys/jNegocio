<?php
/**
* @version		$Id$
* @package		Joomla
* @subpackage	jNegocio
* @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
* @license		Comercial License
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div class="filter-search btn-group nec_flt_lft">
	<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_JNEGOCIO_FILTER_SEARCH_DESC'); ?></label>
	<input type="text" name="filter_search" placeholder="<?php echo JText::_('COM_JNEGOCIO_FILTER_SEARCH_DESC'); ?>" id="filter_search" value="<?php echo $this->escape(@$this->state->search); ?>" title="<?php echo JText::_('COM_JNEGOCIO_FILTER_SEARCH_DESC'); ?>" />
</div>
<div class="btn-group pull-left hidden-phone">
	<button class="nec_btn nec_action_applyfilters tip hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
	<button class="nec_btn nec_action_applyfilters tip hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
</div>

<div class="btn-group hidden-phone nec_flt_rgt">
	<?php echo  jFWSelect::state( @$this->state->filter_state ); ?>
</div>
<div class="necclearfix"></div>