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
JHtml::_('behavior.formvalidation');

jFWBase::load( 'HelperSelect', 'helpers.select' );

$tbl_key	= $this->idkey;
$db 		= &JFactory::getDBO();	
$nullDate 	= $db->getNullDate();
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id('currency-form'))) {
			Joomla.submitform(task, document.getElementById('currency-form'));
		} else {
			var msg = '<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>';
			if($('currency_name').hasClass('invalid')){msg += '\n\n\t* <?= @JText::_( 'COM_JNEGOCIO_CURRENCY_NAME_LABEL' ); ?> <?= @JText::_( 'COM_JNEGOCIO_IS_INVALID' );?>';}
			alert(msg);
			return false;		
		}
	}
</script>

<form action="<?= @JRoute::_($this->action); ?>" method="post" id="currency-form" name="adminForm" class="form-validate" >
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->row->$tbl_key) ? JText::_('COM_JNEGOCIO_NEW_ITEM') : JText::_('COM_JNEGOCIO_EDIT_ITEM'); ?></legend>
			<ul class="adminformlist">
				<li>
					<label id="currency_name-lbl" class="hasTip required" title="<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_NAME_LABEL' ); ?>::<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_NAME_DESC' ); ?>" for="currency_name">
						<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_NAME_LABEL' ); ?><span class="star">&nbsp;*</span>
					</label>
					<input class="inputbox required" type="text" name="currency_name" id="currency_name" value="<?= @$this->row->currency_name; ?>" size="30" maxlength="250" />					
				</li>
			   	<li>
			   		<label id="currency_code-lbl" class="hasTip" title="<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_CODE_LABEL' ); ?>::<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_CODE_DESC' ); ?>" for="currency_code">
						<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_CODE_LABEL' ); ?>
					</label>
					<input class="inputbox required" type="text" name="currency_code" id="currency_code" value="<?= @$this->row->currency_code; ?>" size="5" maxlength="3" />
			   	</li>
			   	<li>
			   		<label id="currency_symbol-lbl" class="hasTip" title="<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_SYMBOL_LABEL' ); ?>::<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_SYMBOL_DESC' ); ?>" for="currency_symbol">
						<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_SYMBOL_LABEL' ); ?>
					</label>
					<input class="text_area" type="text" name="currency_symbol" id="currency_symbol" value="<?= @$this->row->currency_symbol; ?>" size="10" maxlength="12" />
			   	</li>
			   	<li>
			   		<label id="currency_symbol_position-lbl" class="hasTip" title="<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_SYMBOL_POSITION_LABEL' ); ?>::<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_SYMBOL_POSITION_DESC' ); ?>" for="currency_symbol_position">
						<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_SYMBOL_POSITION_LABEL' ); ?>
					</label>
					<?= @HelperSelect::symbolalign( @$this->row->currency_symbol_position, 'currency_symbol_position', '', 'currency_symbol_position' ); ?>
			   	</li>
			   	<li>
			   		<label id="currency_decimals-lbl" class="hasTip" title="<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_DECIMALS_LABEL' ); ?>::<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_DECIMALS_DESC' ); ?>" for="currency_decimals">
						<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_DECIMALS_LABEL' ); ?>
					</label>
					<input class="text_area" type="text" name="currency_decimals" id="currency_decimals" value="<?= @$this->row->currency_decimals; ?>" size="10" maxlength="12" />
			   	</li>
			   	<li>
			   		<label id="currency_decimals_separator-lbl" class="hasTip" title="<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_DECIMALS_SEPARATOR_LABEL' ); ?>::<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_DECIMALS_SEPARATOR_DESC' ); ?>" for="currency_decimals_separator">
						<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_DECIMALS_SEPARATOR_LABEL' ); ?>
					</label>
					<input class="text_area" type="text" name="currency_decimals_separator" id="currency_decimals_separator" value="<?= @$this->row->currency_decimals_separator; ?>" size="10" maxlength="12" />
			   	</li>	
			   	<li>
			   		<label id="currency_thousands_separator-lbl" class="hasTip" title="<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_THOUSANDS_SEPARATOR_LABEL' ); ?>::<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_THOUSANDS_SEPARATOR_DESC' ); ?>" for="currency_thousands_separator">
						<?= @JText::_( 'COM_JNEGOCIO_CURRENCY_THOUSANDS_SEPARATOR_LABEL' ); ?>
					</label>
					<input class="text_area" type="text" name="currency_thousands_separator" id="currency_thousands_separator" value="<?= @$this->row->currency_thousands_separator; ?>" size="10" maxlength="12" />
			   	</li>
			</ul>
		</fieldset>
	</div>
	<div class="width-40 fltlft">
		<?php echo JHtml::_('sliders.start', 'currency-sliders-'.$this->row->$tbl_key, array('useCookie'=>1)); ?>

		<?php echo JHtml::_('sliders.panel', JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'publishing-details'); ?>
	
		<fieldset class="panelform">
			<ul class="adminformlist">
				<li>
					<label title="<?= @JText::_('COM_JNEGOCIO_FIELD_ID_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_FIELD_ID_DESC'); ?>" class="hasTip" for="<?= @$tbl_key;?>" id="<?= @$tbl_key;?>-lbl"><?= @JText::_('COM_JNEGOCIO_FIELD_ID_LABEL'); ?></label>
					<input type="text" readonly="readonly" class="readonly" value="<?= @$this->row->$tbl_key; ?>" id="<?= @$tbl_key;?>" name="<?= @$tbl_key;?>">
				</li>
				<li>
					<label id="published-lbl" class="hasTip" title="<?= @JText::_( 'COM_JNEGOCIO_FIELD_STATUS_LABEL' ); ?>::<?= @JText::_( 'COM_JNEGOCIO_FIELD_STATUS_DESC' ); ?>" for="published"><?= @JText::_( 'COM_JNEGOCIO_FIELD_STATUS_LABEL' ); ?></label>
					<?= HelperSelect::booleans( @$this->row->published, 'published', array('class' => 'inputbox', 'size' => '1'), null, false, 'COM_JNEGOCIO_SELECT_STATUS', 'COM_JNEGOCIO_OPTION_PUBLISHED', 'COM_JNEGOCIO_OPTION_UNPUBLISHED' );?>
			   	</li>
				<li>
					<label title="<?= @JText::_('COM_JNEGOCIO_FIELD_CREATED_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_FIELD_CREATED_DESC'); ?>" class="hasTip" for="created" id="created-lbl"><?= @JText::_('COM_JNEGOCIO_FIELD_CREATED_LABEL'); ?></label>
					<?php 
					if ($this->row->created == $nullDate) {
						echo '<div class="fielsetdisplay">'.JText::_( 'COM_JNEGOCIO_NEW_DOCUMENT' ).'</div>';
					} else {
						echo '<div class="fielsetdisplay">'.JHTML::_('date',  $this->row->created,  JText::_('DATE_FORMAT_LC2') ).'</div>';
					}
					?>
				</li>
				<li>
					<label title="<?= @JText::_('COM_JNEGOCIO_FIELD_MODIFIED_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_FIELD_MODIFIED_DESC'); ?>" class="hasTip" for="modified" id="created-lbl"><?= @JText::_('COM_JNEGOCIO_FIELD_MODIFIED_LABEL'); ?></label>
					<?php 
					if ($this->row->modified == $nullDate) {
						echo '<div class="fielsetdisplay">'.JText::_( 'COM_JNEGOCIO_DOCUMENT_NO_MODIFIED' ).'</div>';
					} else {
						echo '<div class="fielsetdisplay">'.JHTML::_('date',  $this->row->modified,  JText::_('DATE_FORMAT_LC2') ).'</div>';
					}
					?>
				</li>				
			</ul>
		</fieldset>
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
	<div class="clr"></div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="<?= jFWBase::getComponentName();?>" />
	<input type="hidden" name="controller" value="<?php echo $this->_name;?>" />
	<input type="hidden" name="view" value="<?php echo $this->_name;?>" />
	<input type="hidden" name="task" value="" />
</form>
<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>