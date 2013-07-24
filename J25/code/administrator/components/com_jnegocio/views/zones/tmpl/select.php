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

$db				= & JFactory::getDBO();
$user			= & JFactory::getUser();
$nullDate		= $db->getNullDate();
$document		= & JFactory::getDocument();

jFWBase::load( 'jFWHelperBase', 'helpers._base' );
jFWBase::load( 'HelperSelect', 'helpers.select' );
jimport('joomla.html.toolbar');

$state 		= @$this->state;
// echo print_r($state);
?>
<div id="toolbar-box">
	<?php echo JToolBar::getInstance('toolbar')->render('toolbar'); ?>
	<div class="pagetitle icon-48-COM_JNEGOCIO_ZONES"><h2><?= @JText::_( 'COM_JNEGOCIO_SELECT_ZONE_FOR' ); ?> : <?php echo @$this->geozone->name;?></h2></div>
</div>
<div class="clr"></div>

<form action="<?php echo JRoute::_( $this->action );?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('COM_JNEGOCIO_FILTER'); ?>:</label>
			<input type="text" name="search" id="search" value="<?php echo $this->escape(@$state->search); ?>" title="<?php echo JText::_('COM_JNEGOCIO_FILTER_SEARCH_DESC'); ?>" />

			<a href="javascript:;" class="nec_btn nec_action_applyfilters"><span class="icon applyfilters"></span><?= @JText::_( 'COM_JNEGOCIO_FILTER_APPLY');?></a>
			<a href="javascript:;" class="nec_btn nec_action_clearfilters"><span class="icon clearbutton"></span><?= @JText::_( 'COM_JNEGOCIO_FILTER_CLEAR');?></a>			
		</div>
		<div class="filter-select fltrt">
			<?php echo HelperSelect::countries( @$state->filter_countryid, 'filter_countryid', array('class' => 'inputbox necFilter', 'size' => '1', 'onchange' =>'submitform( );' ), 'filter_countryid', true); ?>
			<?php // echo jFWSelect::state( @$state->filter_state ); ?>
		</div>
	</fieldset>
	<div class="clr"> </div>
	<table class="adminlist">
	<thead>
		<tr>
			<th width="1%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onClick="Joomla.checkAll(this)" /></th>
			<th class="title"><?php echo jFWGrid::jFWsort( 'COM_JNEGOCIO_ZONE_NAME_LABEL', 'tbl.name', @$state->filter_order_Dir, @$state->filter_order ); ?></th>
			<th class="title"><?php echo jFWGrid::jFWsort( 'COM_JNEGOCIO_COUNTRY_NAME_LABEL', 'tbl.code', @$state->filter_order_Dir, @$state->filter_order ); ?></th>
			<th width="1%" class="nowrap center"><?php echo jFWGrid::jFWsort( 'COM_JNEGOCIO_PUBLISHED', 'tbl.published', @$state->filter_order_Dir, @$state->filter_order ); ?></th>
		</tr>
	</thead>
	
	<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count($this->rows); $i < $n; $i++) {
			$row = $this->rows[$i];

//			$link 		= 'index.php?option='.jFWBase::getComponentName().'&controller='.$this->_name.'&view='.$this->_name.'&task=edit&amp;cid[]='. $row->$tbl_key;
			$published 	= jFWGrid::jFWpublished( $row->published, $i );
			$enabled 	= jFWGrid::jFWenable( $row->geozone_selected, $i, 'selected_' );
			$checked 	= jFWGrid::checkedout( $row, $i, 'id' );
   		?>
		<tr class="row<?= @$k; ?>">
			<td width="7"><?php echo @$checked; ?></td>
			
			<td align="left">
				<?php echo @htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'); ?>
				<span class="smallsub">(<?php echo @$row->code; ?>)</span>
			</td>
			<td align="left"><?php echo @$row->country_name;?></td>
			
			<td align="center"><?php echo $enabled; ?></td>
		</tr>
		<?php 
			$k = 1 - $k; 
		} ?>
		
		<?php if (!count($this->rows)) : ?>
			<tr>
				<td colspan="20" align="center">
					<?php echo @JText::_( 'COM_JNEGOCIO_NO_ITEMS_FOUND'); ?>
				</td>
			</tr>
		<?php endif; ?>		
	</tbody>
	
	<tfoot>
		<tr>
			<td colspan="20"><?php echo @$this->pageNav->getListFooter(); ?></td>
		</tr>
	</tfoot>
	
	</table>
	
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="<?= jFWBase::getComponentName();?>" />
	<input type="hidden" name="controller" value="<?= @$this->_name;?>" />
	<input type="hidden" name="view" value="<?= @$this->_name;?>" />	
	<input type="hidden" name="layout" value="select" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo @$state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo @$state->filter_order_Dir; ?>" />
	<input type="hidden" name="filter_geozoneid" value="<?php echo @$state->filter_geozoneid; ?>" />
	<?php echo @JHTML::_( 'form.token' ); ?>
</form>