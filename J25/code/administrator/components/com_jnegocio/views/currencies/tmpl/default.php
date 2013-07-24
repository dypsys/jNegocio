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

JHtml::_('behavior.mootools');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$db				= & JFactory::getDBO();
$user			= & JFactory::getUser();
$nullDate		= $db->getNullDate();
$document		= & JFactory::getDocument();

jFWBase::load( 'jFWHelperBase', 'helpers._base' );
jFWBase::load( 'HelperCurrency', 'helpers.currency' );

$script = array();
$script[] = 'window.addEvent(\'domready\', function() {';
$script[] = 'var necformlist = new Negocio.formlist.App({';
$script[] = 'locale:\''.$this->config->default_lang.'\'';
$script[] = '});';
$script[] = '});';
$document->addScriptDeclaration(implode("\n", $script));

$tbl_key 	= $this->idkey;
$state 		= @$this->state;
$ordering 	= (@$state->filter_order == 'tbl.ordering');
?>
<form action="<?= @JRoute::_('index.php');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('COM_JNEGOCIO_FILTER'); ?>:</label>
			<input type="text" name="search" id="search" value="<?php echo $this->escape(@$state->search); ?>" title="<?php echo JText::_('COM_JNEGOCIO_FILTER_SEARCH_DESC'); ?>" />

			<a href="javascript:;" class="nec_btn nec_action_applyfilters"><span class="icon applyfilters"></span><?= @JText::_( 'COM_JNEGOCIO_FILTER_APPLY');?></a>
			<a href="javascript:;" class="nec_btn nec_action_clearfilters"><span class="icon clearbutton"></span><?= @JText::_( 'COM_JNEGOCIO_FILTER_CLEAR');?></a>			
		</div>
		<div class="filter-select fltrt">
		</div>
	</fieldset>
	<div class="clr"> </div>
	
	<table class="adminlist">
	<thead>
		<tr>
			<th width="1%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onClick="Joomla.checkAll(this)" /></th>
			<th class="title"><?= jFWGrid::jFWsort( 'COM_JNEGOCIO_CURRENCY_NAME_LABEL', 'tbl.name', @$state->filter_order_Dir, @$state->filter_order ); ?></th>
			<th class="title"><?= jFWGrid::jFWsort( 'COM_JNEGOCIO_CURRENCY_CODE_LABEL', 'tbl.currency_code', @$state->filter_order_Dir, @$state->filter_order ); ?></th>
			<th class="title"><?= @JText::_( 'COM_JNEGOCIO_CURRENCY_FORMAT' ); ?></th>
			<th class="title"><?= @JText::_( 'COM_JNEGOCIO_CURRENCY_EXCHANGE' ); ?></th>
			<th class="title"><?= @JText::_( 'COM_JNEGOCIO_CURRENCY_EXCHANGE_EUR' ); ?></th>
			<th class="title"><?= jFWGrid::jFWsort( 'COM_JNEGOCIO_CURRENCY_UPDATED_DATE', 'tbl.currency_updated_date', @$state->filter_order_Dir, @$state->filter_order ); ?></th>
			<th width="1%" nowrap="nowrap"><?= @JText::_( 'COM_JNEGOCIO_PUBLISHED' ); ?></th>
			<th width="1%" nowrap="nowrap"><?= jFWGrid::jFWsort( 'COM_JNEGOCIO_FIELD_ID_LABEL', 'tbl.' . $tbl_key, @$state->filter_order_Dir, @$state->filter_order ); ?></th>
		</tr>
	</thead>
	
	<tfoot>
		<tr>
			<td colspan="20"><?= @$this->pageNav->getListFooter(); ?></td>
		</tr>
	</tfoot>
	
	<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count($this->rows); $i < $n; $i++) {
			$row = $this->rows[$i];

			$link 		= 'index.php?option='.jFWBase::getComponentName().'&controller='.$this->_name.'&view='.$this->_name.'&task=edit&amp;cid[]='. $row->$tbl_key;
			$published 	= jFWGrid::jFWpublished( $row->published, $i );
			$checked 	= jFWGrid::checkedout( $row, $i, 'id' );
   		?>
		<tr class="row<?= @$k; ?>">
			<td width="7"><?= @$checked; ?></td>
			
			<td align="left">
				<?php
				if ( $row->checked_out && ( $row->checked_out != $this->user->get('id') ) ) {
					echo htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8');
				} else {
				?>
					<span class="editlinktip hasTip" title="<?= @JText::_( 'COM_JNEGOCIO_EDIT_ITEM' );?>::<?php echo $row->name; ?>">
					<a href="<?php echo $link; ?>">
					<?= @htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'); ?>
					</a></span>
				<?php
				}
				?>
			</td>
			
			<td align="center"><?= @$row->currency_code; ?></td>
			<td align="center"><?= HelperCurrency::format( '9876.54321', $row ); ?></td>
			<td align="center"><?= HelperCurrency::convert( $row->currency_code ); ?></td>
			<td align="center"><?= HelperCurrency::format( HelperCurrency::convert( $row->currency_code , 'EUR'), 'EUR'); ?></td>
			<td align="center">
				<?php
				if ( $row->currency_updated_date == $nullDate ) {
					echo JText::_( 'COM_JNEGOCIO_SIN_ACTUALIZAR' );
				} else {
					echo JHTML::_('date',  $row->currency_updated_date,  JText::_('DATE_FORMAT_LC2') );
				}
				?>
			</td>
									
			<td align="center"><?= @$published; ?></td>
			<td align="center"><?= @$row->$tbl_key; ?></td>
		</tr>
		<?php $k = 1 - $k; } ?>
		
		<?php if (!count($this->rows)) : ?>
			<tr>
				<td colspan="20" align="center">
					<?= @JText::_( 'COM_JNEGOCIO_NO_ITEMS_FOUND'); ?>
				</td>
			</tr>
		<? endif; ?>		
	</tbody>

	</table>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="<?= jFWBase::getComponentName();?>" />
	<input type="hidden" name="controller" value="<?= @$this->_name;?>" />
	<input type="hidden" name="view" value="<?= @$this->_name;?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?= @$state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?= @$state->filter_order_Dir; ?>" />
	<?= @JHTML::_( 'form.token' ); ?>
</form>