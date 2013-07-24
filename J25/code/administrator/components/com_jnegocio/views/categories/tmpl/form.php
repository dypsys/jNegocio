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
jFWBase::load('jFWUrl', 'library.url');

$tbl_key = $this->idkey;
$db = &JFactory::getDBO();
$nullDate = $db->getNullDate();

jimport('joomla.html.pane');
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'cancel' || document.formvalidator.isValid(document.id('category-form'))) {
            Joomla.submitform(task, document.getElementById('category-form'));
        } else {
            var msg = '<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>';
            <?php
            foreach ($this->languages as $lang) {
                $field = "name_" . $lang->language;
                ?>
                if ($('name_<?php print $lang->language; ?>').hasClass('invalid')) {
                    msg += '\n\n\t* <?= @JText::_('COM_JNEGOCIO_CATEGORY_NAME_LABEL'); ?> <?= @JText::_('COM_JNEGOCIO_ITEM_IS_INVALID'); ?>';
                }
            <?php } ?>
            alert(msg);
            return false;
        }
    }
</script>

<form action="<?= @JRoute::_($this->action); ?>" method="post" id="category-form" name="adminForm" class="form-validate" enctype="multipart/form-data" >
    <div class="width-60 fltlft">
        <table width='100%'><tr><td>
            <?php
            $pane = & JPane::getInstance('Tabs');
            echo $pane->startPane('typePane');
            foreach ($this->languages as $lang) {
                $field = "name_" . $lang->language;
                $alias = "alias_" . $lang->language;
                $meta_title = "meta_title_" . $lang->language;
                $meta_keyword = "meta_keyword_" . $lang->language;
                $meta_description = "meta_description_" . $lang->language;

                $name_pane = JText::_('COM_JNEGOCIO_DETAILS');
                if ($this->multilang) {
                    $name_pane .= " (" . $lang->lang . ")";
                }
                echo $pane->startPanel($name_pane, $lang->lang . '-page');
                ?>
                <fieldset class="adminform">
                    <ul class="adminformlist">
                    <li>
                        <label id="name_<?php print $lang->language; ?>-lbl" class="hasTip required" title="<?= @JText::_('COM_JNEGOCIO_CATEGORY_NAME_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_CATEGORY_NAME_DESC'); ?>" for="name_<?php print $lang->language; ?>">
                            <?= @JText::_('COM_JNEGOCIO_CATEGORY_NAME_LABEL'); ?> <?php if ($this->multilang) print "(" . $lang->lang . ")"; ?>
                            <span class="star">&nbsp;*</span>
                        </label>
                        <input class="inputbox required" type="text" name="name_<?php print $lang->language; ?>" id="name_<?php print $lang->language; ?>" value="<?= @$this->row->$field; ?>" size="30" maxlength="250" />					
                    </li>
                    <li>
                        <label id="<?php print $alias; ?>-lbl" class="hasTip" title="<?= @JText::_('JFIELD_ALIAS_LABEL'); ?>::<?= @JText::_('JFIELD_ALIAS_DESC'); ?>" for="<?php print $alias; ?>">
                            <?= @JText::_('JFIELD_ALIAS_LABEL'); ?>
                        </label>
                        <input class="inputbox" type="text" name="<?php print $alias; ?>" id="<?php print $alias; ?>" value="<?= @$this->row->$alias; ?>" size="30" maxlength="250" />
                    </li>
                    <li>
                        <label id="<?php print $meta_title; ?>-lbl" class="hasTip" title="<?= @JText::_('COM_JNEGOCIO_FIELD_META_TITEL_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_FIELD_META_TITEL_DESC'); ?>" for="<?php print $meta_title; ?>">
                            <?= @JText::_('COM_JNEGOCIO_FIELD_META_TITEL_LABEL'); ?>
                        </label>
                        <input class="inputbox" type="text" name="<?php print $meta_title; ?>" id="<?php print $meta_title; ?>" value="<?= @$this->row->$meta_title; ?>" size="30" maxlength="250" />
                    </li>
                    <li>
                        <label id="<?php print $meta_description; ?>-lbl" class="hasTip" title="<?= @JText::_('JFIELD_META_DESCRIPTION_LABEL'); ?>::<?= @JText::_('JFIELD_META_DESCRIPTION_DESC'); ?>" for="<?php print $meta_description; ?>">
                            <?= @JText::_('JFIELD_META_DESCRIPTION_LABEL'); ?>
                        </label>
                        <textarea name="<?php print $meta_description; ?>" id="<?php print $meta_description; ?>" cols="35" rows="5"><?= @$this->row->$meta_description ?></textarea>
                    </li>
                    <li>
                        <label id="<?php print $meta_keyword; ?>-lbl" class="hasTip" title="<?= @JText::_('JFIELD_META_KEYWORDS_LABEL'); ?>::<?= @JText::_('JFIELD_META_KEYWORDS_DESC'); ?>" for="<?php print $meta_keyword; ?>">
                            <?= @JText::_('JFIELD_META_KEYWORDS_LABEL'); ?>
                        </label>
                        <textarea name="<?php print $meta_keyword; ?>" id="<?php print $meta_keyword; ?>" cols="35" rows="5"><?= @$this->row->$meta_keyword ?></textarea>
                    </li>
                    </ul>
                </fieldset>
                <?php
                echo $pane->endPanel();
            }
            
            if ($this->row->$tbl_key) {
                echo $pane->startPanel( JText::_('COM_JNEGOCIO_IMAGEN') , 'image-page');
                ?>
                <fieldset class="adminform">
                    <ul class="adminformlist">
                    <li>
                        <label id="current_image-lbl" class="hasTip" title="<?= @JText::_('COM_JNEGOCIO_CURRENT_IMAGE_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_CURRENT_IMAGE_DESC'); ?>" for="attachment">
                            <?= @JText::_('COM_JNEGOCIO_CURRENT_IMAGE_LABEL'); ?>
                        </label>
                        <?php
                        jimport('joomla.filesystem.file');
                        if (!empty($this->row->attachment) && JFile::exists( JPATH_SITE .DS. $this->row->locationuri .DS. $this->row->attachment)) {
                            $src = JURI::root() . $this->row->locationurl . '/' . $this->row->attachment;
                            $src_thumbs = JURI::root() . $this->row->locationurl . '/thumbs/' . $this->row->attachment;
                            $img_thumb = "<img src='" . $src_thumbs ."' align='center' border='0' >";

                            echo jFWUrl::popup( $src, $img_thumb, array('update' => false, 'img' => true));
                        }
                        ?>
                        <br/>
                        <input class="inputbox" type="hidden" name="attachment" id="attachment" value="<?= @$this->row->attachment; ?>" size="30" maxlength="250" readonly />
                        <input class="inputbox" type="hidden" name="locationurl" id="locationurl" value="<?= @$this->row->locationurl; ?>" size="30" maxlength="250" readonly />
                        <input class="inputbox" type="hidden" name="locationuri" id="locationuri" value="<?= @$this->row->locationuri; ?>" size="30" maxlength="250" readonly />
                    </li>
                    <li>
                        <label for="delete_image_field" class="hasTip" title="<?= @JText::_('COM_JNEGOCIO_DELETE_FILE_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_DELETE_FILE_DESC'); ?>" for="delete_image">
                            <?= @JText::_('COM_JNEGOCIO_DELETE_FILE_LABEL')?>
                        </label>
			<input type="checkbox" name="delete_image" />
                    </li>
                    <li>
                        <label id="new_image-lbl" class="hasTip" title="<?= @JText::_('COM_JNEGOCIO_NEW_IMAGE_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_NEW_IMAGE_DESC'); ?>" for="new_image">
                            <?= @JText::_('COM_JNEGOCIO_NEW_IMAGE_LABEL'); ?>
                        </label>
                        <input name="category_image_new" type="file" size="40" />
                    </li>
                    </ul>
                </fieldset>
                <?php
                echo $pane->endPanel();
            }
            echo $pane->endPane();?>
        </td></tr></table>
    </div>
    <div class="width-40 fltlft">
        <?php echo JHtml::_('sliders.start', 'categories-sliders-' . $this->row->$tbl_key, array('useCookie' => 1)); ?>

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
                    <label id="parent_id-lbl" class="hasTip" title="<?= @JText::_('COM_JNEGOCIO_CATEGORY_PARENT_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_CATEGORY_PARENT_DESC'); ?>" for="parent_id">
                    <?= @JText::_('COM_JNEGOCIO_CATEGORY_PARENT_LABEL'); ?>
                    </label>
<?= @HelperSelect::category(@$this->row->parent_id, 'parent_id', '', 'parent_id', false, true); ?>
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
    <input type="hidden" name="option" value="<?= jFWBase::getComponentName(); ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->_name; ?>" />
    <input type="hidden" name="view" value="<?php echo $this->_name; ?>" />
    <input type="hidden" name="task" value="" />
</form>
<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>