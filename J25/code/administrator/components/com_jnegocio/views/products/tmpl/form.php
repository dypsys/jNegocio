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

jFWBase::load('HelperSelect', 'helpers.select');
jFWBase::load('HelperProduct', 'helpers.product');
jFWBase::load('HelperCurrency', 'helpers.currency');
jFWBase::load('jFWUrl', 'library.url');

$tbl_key    = $this->idkey;
$db         = &JFactory::getDBO();
$nullDate   = $db->getNullDate();
$document   = & JFactory::getDocument();

jimport('joomla.html.pane');
JHtml::_('behavior.mootools');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.modal');

$js = 'jQuery.noConflict();';
$document->addScriptDeclaration($js);

$document->addScript(jFWBase::getUrl('js', false) . 'jquery/autonumeric/autoNumeric.js');
$document->addScript(jFWBase::getUrl('js', false) . 'jquery/plupload/browserplus-min.js');
$document->addScript(jFWBase::getUrl('js', false) . 'jquery/plupload/plupload.js');
$document->addScript(jFWBase::getUrl('js', false) . 'jquery/plupload/plupload.gears.js');
$document->addScript(jFWBase::getUrl('js', false) . 'jquery/plupload/plupload.silverlight.js');
$document->addScript(jFWBase::getUrl('js', false) . 'jquery/plupload/plupload.flash.js');
$document->addScript(jFWBase::getUrl('js', false) . 'jquery/plupload/plupload.browserplus.js');
$document->addScript(jFWBase::getUrl('js', false) . 'jquery/plupload/plupload.html4.js');
$document->addScript(jFWBase::getUrl('js', false) . 'jquery/plupload/plupload.html5.js');

JHtml::_('stylesheet', 'nec.plupload.css', jFWBase::getUrl('css', false) );

?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task === 'cancel' || document.formvalidator.isValid(document.id('product-form'))) {
            Joomla.submitform(task, document.getElementById('product-form'));
        } else {
            var msg = '<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>';
            <?php
            foreach ($this->languages as $lang) {
                $field = "name_" . $lang->language;?>
                if ($('name_<?php print $lang->language; ?>').hasClass('invalid')) {
                    msg += '\n\n\t* <?= @JText::_('COM_JNEGOCIO_PRODUCT_NAME_LABEL'); ?> <?= @JText::_('COM_JNEGOCIO_ITEM_IS_INVALID'); ?>';
                }
            <?php } ?>
            alert(msg);
            return false;
        }
    }
</script>

<form action="<?= @JRoute::_($this->action); ?>" method="post" id="product-form" name="adminForm" class="form-validate" enctype="multipart/form-data" >
    <div class="width-69 fltlft">
        <table width='100%'><tr><td>
            <?php
            $this->pane = & JPane::getInstance('Tabs');
            echo $this->pane->startPane('productPane');
            echo $this->loadTemplate('descriptions');
            
            echo $this->pane->startPanel( JText::_('COM_JNEGOCIO_GENERAL') , 'product-general-page');
            echo $this->loadTemplate('general');
            echo $this->pane->endPanel();
            
            echo $this->pane->startPanel( JText::_('COM_JNEGOCIO_PRICES') , 'product-prices-page');
            echo $this->loadTemplate('prices');
            echo $this->pane->endPanel();
            
            echo $this->pane->startPanel( JText::_('COM_JNEGOCIO_ATTRIBUTES') , 'product-attributes-page');
            echo $this->loadTemplate('attributes');
            echo $this->pane->endPanel();
            
            echo $this->pane->startPanel( JText::_('COM_JNEGOCIO_IMAGES') , 'product-images-page');
            echo $this->loadTemplate('images');
            echo $this->pane->endPanel();
            
            echo $this->pane->endPane();
            ?>
        </td></tr></table>
    </div>
    <div class="width-30 fltrt">
        <?php echo JHtml::_('sliders.start', 'manufacturers-sliders-' . $this->row->$tbl_key, array('useCookie' => 1)); ?>

        <?php echo JHtml::_('sliders.panel', JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'publishing-details'); ?>

        <fieldset class="panelform">
            <ul class="adminformlist">
                <li>
                    <label title="<?= @JText::_('COM_JNEGOCIO_FIELD_ID_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_FIELD_ID_DESC'); ?>" class="hasTip" for="<?= @$tbl_key; ?>" id="<?= @$tbl_key; ?>-lbl"><?= @JText::_('COM_JNEGOCIO_FIELD_ID_LABEL'); ?></label>
                    <input type="text" readonly="readonly" class="readonly" value="<?= @$this->row->$tbl_key; ?>" id="<?= @$tbl_key; ?>" name="<?= @$tbl_key; ?>">
                </li>
                <li>
                    <label id="published-lbl" class="hasTip" title="<?= @JText::_('COM_JNEGOCIO_FIELD_STATUS_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_FIELD_STATUS_DESC'); ?>" for="published"><?= @JText::_('COM_JNEGOCIO_FIELD_STATUS_LABEL'); ?></label>
                    <?= HelperSelect::booleans(@$this->row->published, 'published', array('class' => 'inputbox', 'size' => '1'), null, false, 'COM_JNEGOCIO_SELECT_STATUS', 'COM_JNEGOCIO_OPTION_PUBLISHED', 'COM_JNEGOCIO_OPTION_UNPUBLISHED'); ?>
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
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="option" value="<?= jFWBase::getComponentName(); ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->_name; ?>" />
    <input type="hidden" name="view" value="<?php echo $this->_name; ?>" />
    <input type="hidden" name="task" value="" />
</form>
<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>