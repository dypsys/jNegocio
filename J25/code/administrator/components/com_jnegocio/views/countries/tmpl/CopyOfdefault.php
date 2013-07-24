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

$db			= JFactory::getDBO();
$user		= JFactory::getUser();
$userId     = $user->get('id');
$nullDate	= $db->getNullDate();
$document	= JFactory::getDocument();

jFWBase::load( 'jFWHelperBase', 'helpers._base' );

JHtml::_('behavior.mootools');
JHtml::_('behavior.tooltip');

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
<form action="<?php echo JRoute::_( $this->action );?>" method="post" name="adminForm" id="nec_list_form">
	<div class="box box-color box-bordered">
		<div class="box-title">
			<h3><i class="icon-table"></i>Dynamic table</h3>
		</div>
		<div class="box-content nopadding">
			<div class="box-filters">
				<?php echo $this->loadTemplate('filters'); ?>
			</div>
		</div>
	</div>
	
	<table class="nectable nectableadminlist">
	<thead>
		<tr>
			<th width="1%" class="hidden-phone">
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onClick="checkAll(<?php echo count( $this->rows ); ?>);" />
			</th>
			<th>
				<?php echo jFWGrid::jFWsort( 'COM_NEGOCIO_TITLE', 'tbl.name', @$state->filter_order_Dir, @$state->filter_order ); ?>
			</th>
			<th width="1%" class="nowrap center">
				<?php echo jFWGrid::jFWsort( 'COM_NEGOCIO_STATUS', 'tbl.published', @$state->filter_order_Dir, @$state->filter_order ); ?>
			</th>
			<th style="100px;" nowrap="nowrap">
            	<?php echo @jFWGrid::jFWsort( 'Order', "tbl.ordering", @$state->filter_order_Dir, @$state->filter_order ); ?>
				<?php echo $ordering ? jFWGrid::jFWorder( @$this->rows , 'filesave.png', 'saveorder' ) : ''; ?>            	
            </th>
			<th width="1%" class="nowrap center hidden-phone">
				<?php echo jFWGrid::jFWsort( 'COM_NEGOCIO_ID', 'tbl.' . $tbl_key, @$state->filter_order_Dir, @$state->filter_order ); ?>
			</th>
		</tr>
	</thead>
	
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
			<td width="7"><?php echo @$checked; ?></td>
			
			<td align="left">
				<?php
				if ( $row->checked_out && ( $row->checked_out != $this->user->get('id') ) ) {
					echo htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8');
				} else {
				?>
					<a href="<?php echo $link; ?>">
						<?php echo @htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'); ?>
					</a>
				<?php
				}
				?>
				<p class="smallsub">
					<?php echo @$row->isocode_2 . ' ' . @$row->isocode_3; ?>
				</p>
			</td>
			
			<td align="center"><?php echo @$published; ?></td>
			<td class="order">
				<span><?php echo $this->pageNav->orderUpIcon( $i, true, 'orderup', 'Move Up', $ordering ); ?></span>
				<span><?php echo $this->pageNav->orderDownIcon( $i, $n, true, 'orderdown', 'Move Down', $ordering );?></span>
				<?php $disabled = $ordering ?  '' : '"disabled=disabled"'; ?>				
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="text-area-order" />
			</td>
			<td align="center"><?php echo @$row->$tbl_key; ?></td>
		</tr>
		<?php 
			$k = 1 - $k; 
		} ?>
		
		<?php if (!count($this->rows)) : ?>
			<tr>
				<td colspan="20" align="center">
					<?php echo @JText::_( 'COM_NEGOCIO_NO_ITEMS_FOUND'); ?>
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
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo @$state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo @$state->filter_order_Dir; ?>" />
	<?php echo @JHTML::_( 'form.token' ); ?>
</form>