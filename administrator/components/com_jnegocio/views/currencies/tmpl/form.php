<?php
/**
 * @version	    3.0.1
 * @package	    Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2014 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.formvalidation');

jFWBase::load( 'HelperSelect', 'helpers.select' );

$tbl_key    = $this->idkey;
$db 		= JFactory::getDBO();
$nullDate 	= $db->getNullDate();
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'cancel' || document.formvalidator.isValid(document.id('country-form')))
        {
            Joomla.submitform(task, document.id('country-form'));
        } else {
            var msg = '<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>';
            if($('currency_name').hasClass('invalid')){msg += '\n\n\t* <?= @JText::_( 'COM_JNEGOCIO_CURRENCY_NAME_LABEL' ); ?> <?= @JText::_( 'COM_JNEGOCIO_IS_INVALID' );?>';}
            if($('currency_code').hasClass('invalid')){msg += '\n\n\t* <?= @JText::_( 'COM_JNEGOCIO_CURRENCY_CODE_LABEL' ); ?> <?= @JText::_( 'COM_JNEGOCIO_IS_INVALID' );?>';}
            alert(msg);
            return false;
        }
    }
</script>
<form action="<?= @JRoute::_($this->action); ?>" method="post" id="country-form" name="adminForm" class="form-validate">
    <div class="form-horizontal">
        <div class="row-fluid">
            <div class="span9">

                <div class="control-group">
                    <div class="control-label">
                        <label id="currency_name-lbl" for="currency_name" class="hasTooltip required" title="<?= @JText::_('COM_JNEGOCIO_CURRENCY_NAME_DESC'); ?>" >
                            <?= @JText::_( 'COM_JNEGOCIO_CURRENCY_NAME_LABEL' ); ?>
                            <span class="star">&nbsp;*</span>
                        </label>
                    </div>
                    <div class="controls">
                        <input type="text" name="jform[currency_name]" id="currency_name" value="<?= @$this->row->currency_name; ?>" class="inputbox required" size="40" maxlength="250" />
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label">
                        <label id="currency_code-lbl" for="currency_code" class="hasTooltip required" title="<?= @JText::_('COM_JNEGOCIO_CURRENCY_CODE_DESC'); ?>" >
                            <?= @JText::_( 'COM_JNEGOCIO_CURRENCY_CODE_LABEL' ); ?>
                            <span class="star">&nbsp;*</span>
                        </label>
                    </div>
                    <div class="controls">
                        <input type="text" name="jform[currency_code]" id="currency_code" value="<?= @$this->row->currency_code; ?>" class="inputbox required" size="5" maxlength="3" />
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label">
                        <label id="currency_symbol-lbl" for="currency_symbol" class="hasTooltip" title="<?= @JText::_('COM_JNEGOCIO_CURRENCY_SYMBOL_DESC'); ?>" >
                            <?= @JText::_( 'COM_JNEGOCIO_CURRENCY_SYMBOL_LABEL' ); ?>
                        </label>
                    </div>
                    <div class="controls">
                        <input type="text" name="jform[currency_symbol]" id="currency_symbol" value="<?= @$this->row->currency_symbol; ?>" class="inputbox required" size="10" maxlength="12" />
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label">
                        <label id="currency_symbol_position-lbl" for="currency_symbol_position" class="hasTooltip" title="<?= @JText::_('COM_JNEGOCIO_CURRENCY_SYMBOL_POSITION_DESC'); ?>" >
                            <?= @JText::_( 'COM_JNEGOCIO_CURRENCY_SYMBOL_POSITION_LABEL' ); ?>
                        </label>
                    </div>
                    <div class="controls">
                        <?= @HelperSelect::symbolalign( @$this->row->currency_symbol_position, 'jform[currency_symbol_position]', '', 'currency_symbol_position' ); ?>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label">
                        <label id="currency_decimals-lbl" for="currency_decimals" class="hasTooltip" title="<?= @JText::_('COM_JNEGOCIO_CURRENCY_DECIMALS_DESC'); ?>" >
                            <?= @JText::_( 'COM_JNEGOCIO_CURRENCY_DECIMALS_LABEL' ); ?>
                        </label>
                    </div>
                    <div class="controls">
                        <input type="text" name="jform[currency_decimals]" id="currency_decimals" value="<?= @$this->row->currency_decimals; ?>" class="inputbox required" size="10" maxlength="12" />
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label">
                        <label id="currency_decimals_separator-lbl" for="currency_decimals_separator" class="hasTooltip" title="<?= @JText::_('COM_JNEGOCIO_CURRENCY_DECIMALS_SEPARATOR_DESC'); ?>" >
                            <?= @JText::_( 'COM_JNEGOCIO_CURRENCY_DECIMALS_SEPARATOR_LABEL' ); ?>
                        </label>
                    </div>
                    <div class="controls">
                        <input type="text" name="jform[currency_decimals_separator]" id="currency_decimals_separator" value="<?= @$this->row->currency_decimals_separator; ?>" class="inputbox required" size="10" maxlength="12" />
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label">
                        <label id="currency_thousands_separator-lbl" for="currency_thousands_separator" class="hasTooltip" title="<?= @JText::_('COM_JNEGOCIO_CURRENCY_THOUSANDS_SEPARATOR_DESC'); ?>" >
                            <?= @JText::_( 'COM_JNEGOCIO_CURRENCY_THOUSANDS_SEPARATOR_LABEL' ); ?>
                        </label>
                    </div>
                    <div class="controls">
                        <input type="text" name="jform[currency_thousands_separator]" id="currency_thousands_separator" value="<?= @$this->row->currency_thousands_separator; ?>" class="inputbox required" size="10" maxlength="12" />
                    </div>
                </div>
            </div>

            <div class="span3">
                <fieldset class="form-vertical">
                    <div class="control-group">
                        <div class="control-label">
                            <label id="<?php print $tbl_key;?>-lbl" for="<?php print $tbl_key;?>" class="hasTooltip" title="<?= @JText::_('COM_JNEGOCIO_FIELD_ID_DESC'); ?>"><?= @JText::_('COM_JNEGOCIO_FIELD_ID_LABEL'); ?></label>
                        </div>
                        <div class="controls">
                            <input type="text" readonly="readonly" class="readonly" value="<?= @$this->row->$tbl_key; ?>" id="<?= @$tbl_key;?>" name="jform[<?= @$tbl_key;?>]">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">
                            <label id="published-lbl" for="published" class="hasTooltip" title="<?= @JText::_( 'COM_JNEGOCIO_FIELD_STATUS_DESC' ); ?>"><?= @JText::_( 'COM_JNEGOCIO_FIELD_STATUS_LABEL' ); ?></label>
                        </div>
                        <div class="controls">
                            <?= HelperSelect::booleans( @$this->row->published, 'jform[published]', array('class' => 'inputbox', 'size' => '1'), null, false, 'COM_JNEGOCIO_SELECT_STATUS', 'COM_JNEGOCIO_OPTION_PUBLISHED', 'COM_JNEGOCIO_OPTION_UNPUBLISHED' );?>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">
                            <label id="created-lbl" for="created" class="hasTooltip" title="<?= @JText::_( 'COM_JNEGOCIO_FIELD_CREATED_DESC' ); ?>"><?= @JText::_( 'COM_JNEGOCIO_FIELD_CREATED_LABEL' ); ?></label>
                        </div>
                        <div class="controls">
                            <?php
                            if ($this->row->created == $nullDate) {
                                echo JText::_( 'COM_JNEGOCIO_NEW_DOCUMENT' );
                            } else {
                                echo JHTML::_('date',  $this->row->created,  JText::_('DATE_FORMAT_LC2') );
                            }
                            ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">
                            <label id="created-lbl" for="created" class="hasTooltip" title="<?= @JText::_( 'COM_JNEGOCIO_FIELD_MODIFIED_DESC' ); ?>"><?= @JText::_( 'COM_JNEGOCIO_FIELD_MODIFIED_LABEL' ); ?></label>
                        </div>
                        <div class="controls">
                            <?php
                            if ($this->row->modified == $nullDate) {
                                echo JText::_( 'COM_JNEGOCIO_DOCUMENT_NO_MODIFIED' );
                            } else {
                                echo JHTML::_('date',  $this->row->modified,  JText::_('DATE_FORMAT_LC2') );
                            }
                            ?>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>

    <?php echo JHTML::_( 'form.token' ); ?>
    <input type="hidden" name="option" value="<?= jFWBase::getComponentName();?>" />
    <input type="hidden" name="controller" value="<?php echo $this->_name;?>" />
    <input type="hidden" name="view" value="<?php echo $this->_name;?>" />
    <input type="hidden" name="task" value="" />
</form>