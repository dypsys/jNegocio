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

JHtml::_('behavior.mootools');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

jFWBase::load('HelperSelect', 'helpers.select');

$tbl_key = $this->idkey;
$state = @$this->state;
$db = &JFactory::getDBO();
$nullDate = $db->getNullDate();
jimport('joomla.html.pane');
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'cancel' || document.formvalidator.isValid(document.id('attributesvalues-form'))) {
            Joomla.submitform(task, document.getElementById('attributesvalues-form'));
        } else {
            var msg = '<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>';
            <?php
            foreach ($this->languages as $lang) {
                $field = "name_" . $lang->language;
                ?>
                if ($('name_<?php print $lang->language; ?>').hasClass('invalid')) {
                    msg += '\n\n\t* <?= @JText::_('COM_JNEGOCIO_ATTRIBUTESVALUES_NAME_LABEL'); ?> <?= @JText::_('COM_JNEGOCIO_ITEM_IS_INVALID'); ?>';
                }
                <?php } ?>
                alert(msg);
            return false;
        }
    }
</script>

<form action="<?= @JRoute::_($this->action); ?>" method="post" id="attributesvalues-form" name="adminForm" class="form-validate" enctype="multipart/form-data" >
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <ul class="adminformlist">
                <?php foreach ($this->languages as $lang) {
                    $field = "name_" . $lang->language;
                    ?>
                    <li>
                        <label id="name_<?php print $lang->language; ?>-lbl" class="hasTip required" title="<?= @JText::_('COM_JNEGOCIO_ATTRIBUTESVALUES_NAME_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_ATTRIBUTESVALUES_NAME_DESC'); ?>" for="name_<?php print $lang->language; ?>">
                            <?= @JText::_('COM_JNEGOCIO_ATTRIBUTESVALUES_NAME_LABEL'); ?> <?php if ($this->multilang) print "(" . $lang->lang . ")"; ?>
                            <span class="star">&nbsp;*</span>
                        </label>
                        <input class="inputbox required" type="text" name="name_<?php print $lang->language; ?>" id="name_<?php print $lang->language; ?>" value="<?= @$this->row->$field; ?>" size="30" maxlength="250" />					
                    </li>
                <?php } ?>
            </ul>
        </fieldset>
    </div>
    <div class="width-40 fltlft">
        <?php echo JHtml::_('sliders.start', 'attributes-sliders-' . $this->row->$tbl_key, array('useCookie' => 1)); ?>

        <?php echo JHtml::_('sliders.panel', JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'publishing-details'); ?>

        <fieldset class="panelform">
            <ul class="adminformlist">
                <li>
                    <label title="<?= @JText::_('COM_JNEGOCIO_FIELD_ID_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_FIELD_ID_DESC'); ?>" class="hasTip" for="<?= @$tbl_key; ?>" id="<?= @$tbl_key; ?>-lbl"><?= @JText::_('COM_JNEGOCIO_FIELD_ID_LABEL'); ?></label>
                    <input type="text" readonly="readonly" class="readonly" value="<?= @$this->row->$tbl_key; ?>" id="<?= @$tbl_key; ?>" name="<?= @$tbl_key; ?>">
                </li>
                
                <li>
                    <label title="<?= @JText::_('COM_JNEGOCIO_FIELD_CREATED_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_FIELD_CREATED_DESC'); ?>" class="hasTip" for="created" id="created-lbl"><?= @JText::_('COM_JNEGOCIO_FIELD_CREATED_LABEL'); ?></label>
                    <?php
                    if ($this->row->created == $nullDate) {
                        echo '<div class="fielsetdisplay">' . JText::_('COM_JNEGOCIO_NEW_DOCUMENT') . '</div>';
                    } else {
                        echo '<div class="fielsetdisplay">' . JHTML::_('date', $this->row->created, JText::_('DATE_FORMAT_LC2')) . '</div>';
                    }
                    ?>
                </li>
                <li>
                    <label title="<?= @JText::_('COM_JNEGOCIO_FIELD_MODIFIED_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_FIELD_MODIFIED_DESC'); ?>" class="hasTip" for="modified" id="created-lbl"><?= @JText::_('COM_JNEGOCIO_FIELD_MODIFIED_LABEL'); ?></label>
                    <?php
                    if ($this->row->modified == $nullDate) {
                        echo '<div class="fielsetdisplay">' . JText::_('COM_JNEGOCIO_DOCUMENT_NO_MODIFIED') . '</div>';
                    } else {
                        echo '<div class="fielsetdisplay">' . JHTML::_('date', $this->row->modified, JText::_('DATE_FORMAT_LC2')) . '</div>';
                    }
                    ?>
                </li>				
            </ul>
        </fieldset>
        <?php echo JHtml::_('sliders.end'); ?>
    </div>
    <div class="clr"></div>

    <?php echo JHTML::_('form.token'); ?>
    <input type="hidden" name="attribute_id" value="<?= @$state->filter_attrid; ?>" />
    <input type="hidden" name="option" value="<?= jFWBase::getComponentName(); ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->_name; ?>" />
    <input type="hidden" name="view" value="<?php echo $this->_name; ?>" />
    <input type="hidden" name="task" value="" />
</form>
<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>        