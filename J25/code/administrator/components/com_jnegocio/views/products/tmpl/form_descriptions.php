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

foreach ($this->languages as $lang) {
    $field = "name_" . $lang->language;
    $alias = "alias_" . $lang->language;
    $shortdesc = "shortdesc_" . $lang->language;
    $description = "description_".$lang->language;
    $meta_title = "meta_title_" . $lang->language;
    $meta_keyword = "meta_keyword_" . $lang->language;
    $meta_description = "meta_description_" . $lang->language;

    $name_pane = JText::_('COM_JNEGOCIO_DETAILS');
    if ($this->multilang) {
        $name_pane .= " (" . $lang->lang . ")";
    }
    echo $this->pane->startPanel($name_pane, $lang->lang . '-page');
    ?>
    <fieldset class="adminform">
        <ul class="adminformlist">
        <li>
            <label id="name_<?php print $lang->language; ?>-lbl" class="hasTip required" title="<?= @JText::_('COM_JNEGOCIO_PRODUCT_NAME_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_PRODUCT_NAME_DESC'); ?>" for="name_<?php print $lang->language; ?>">
                <?= @JText::_('COM_JNEGOCIO_PRODUCT_NAME_LABEL'); ?> <?php if ($this->multilang) print "(" . $lang->lang . ")"; ?>
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
            <label id="<?php print $alias; ?>-lbl" class="hasTip" title="<?= @JText::_('COM_JNEGOCIO_PRODUCT_SHORTDESC_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_PRODUCT_SHORTDESC_DESC'); ?>" for="<?php print $alias; ?>">
                <?= @JText::_('COM_JNEGOCIO_PRODUCT_SHORTDESC_LABEL'); ?>
            </label>
            <textarea name="<?php print $shortdesc; ?>" id="<?php print $shortdesc; ?>" cols="35" rows="5"><?= @$this->row->$shortdesc ?></textarea>
        </li>
        
        <li>
            <label id="<?php print $description;?>-lbl" class="hasTip" title="<?= @JText::_( 'COM_JNEGOCIO_PRODUCT_DESC_LABEL' ); ?>::<?= @JText::_( 'COM_JNEGOCIO_PRODUCT_DESC_DESC' ); ?>" for="<?php print $description;?>">
                <?= @JText::_( 'COM_JNEGOCIO_PRODUCT_DESC_LABEL' ); ?>
            </label>
        </li>
        <li>
            <?php print $this->getEditor()->display($description, htmlspecialchars_decode(@$this->row->$description), '100%', '350', '75', '20', false, 'id_'.$description ) ;?>
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
    echo $this->pane->endPanel();
}
