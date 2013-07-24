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
JHtml::_('behavior.multiselect');

$db         = & JFactory::getDBO();
$user       = & JFactory::getUser();
$nullDate   = $db->getNullDate();
$document   = & JFactory::getDocument();

jFWBase::load('jFWHelperBase', 'helpers._base');

$script = array();
$script[] = 'window.addEvent(\'domready\', function() {';
$script[] = 'var necformlist = new Negocio.formlist.App({';
$script[] = 'locale:\''.$this->config->default_lang.'\'';
$script[] = '});';
$script[] = '});';
$document->addScriptDeclaration(implode("\n", $script));

$tbl_key    = $this->idkey;
$state      = @$this->state;
$ordering   = (@$state->filter_order == 'tbl.ordering');
?>
<form action="<?php echo JRoute::_( $this->action );?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('COM_JNEGOCIO_FILTER'); ?>:</label>
            <input type="text" name="search" id="search" value="<?php echo $this->escape(@$state->search); ?>" title="<?php echo JText::_('COM_JNEGOCIO_FILTER_SEARCH_DESC'); ?>" />

            <a href="javascript:;" class="nec_btn nec_action_applyfilters"><span class="icon applyfilters"></span><?= @JText::_('COM_JNEGOCIO_FILTER_APPLY'); ?></a>
            <a href="javascript:;" class="nec_btn nec_action_clearfilters"><span class="icon clearbutton"></span><?= @JText::_('COM_JNEGOCIO_FILTER_CLEAR'); ?></a>			
        </div>
        <div class="filter-select fltrt">
            <?= jFWSelect::state(@$state->filter_state); ?>
        </div>
    </fieldset>
    <div class="clr"> </div>

    <table class="adminlist">
        <thead>
            <tr>
                <th width="1%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onClick="Joomla.checkAll(this)" /></th>
                <th class="title"><?php echo jFWGrid::jFWsort('COM_JNEGOCIO_LANGUAGE_NAME_LABEL', 'tbl.name', @$state->filter_order_Dir, @$state->filter_order); ?></th>
                <th width="5%"><?= @JText::_('COM_JNEGOCIO_LANGUAGE_FRONT_LANG'); ?></th>
                <th width="5%"><?= @JText::_('COM_JNEGOCIO_LANGUAGE_LANG_FOR_COPY'); ?></th>
                <th width="1%" class="nowrap center"><?php echo jFWGrid::jFWsort('COM_JNEGOCIO_PUBLISHED', 'tbl.published', @$state->filter_order_Dir, @$state->filter_order); ?></th>
                <th width="1%" class="nowrap center"><?php echo jFWGrid::jFWsort('COM_JNEGOCIO_FIELD_ID_LABEL', 'tbl.' . $tbl_key, @$state->filter_order_Dir, @$state->filter_order); ?></th>
            </tr>		
        </thead>

        <tbody>
            <?php
            $k = 0;
            for ($i = 0, $n = count($this->rows); $i < $n; $i++) {
                $row = $this->rows[$i];

                $published = jFWGrid::jFWpublished($row->published, $i);
                $checked = jFWGrid::checkedout($row, $i, 'id');
                ?>
                <tr class="row<?= @$k; ?>">
                    <td width="7"><?php echo @$checked; ?></td>
                    
                    <td align="left">
                        <?php echo @htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'); ?>
                    </td>

                    <td align="center">
                        <?php
                        if ($this->config->frontend_lang == $row->language) {
                            echo '<img src="' . jFWBase::getURL('icons') . '16/default.png" border="0" alt="" />';
                        }
                        ?>
                    </td>
                    <td align="center">
                        <?php
                        if ($this->config->default_lang == $row->language) {
                            echo '<img src="' . jFWBase::getURL('icons') . '16/default.png" border="0" alt="" />';
                        }
                        ?>
                    </td>

                    <td align="center"><?php echo @$published; ?></td>
                    <td align="center"><?php echo @$row->$tbl_key; ?></td>
                </tr>
                <?php $k = 1 - $k;
            } ?>

            <?php if (!count($this->rows)) : ?>
                <tr>
                    <td colspan="20" align="center">
                        <?php echo @JText::_('COM_JNEGOCIO_NO_ITEMS_FOUND'); ?>
                    </td>
                </tr>
            <?php endif; ?>	
        </tbody>

        <tfoot>
            <tr>
                <td colspan="20"><?= @$this->pageNav->getListFooter(); ?></td>
            </tr>
        </tfoot>        
    </table>
    
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="filter_order" value="<?= @$state->filter_order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?= @$state->filter_order_Dir; ?>" />
    <?= @JHTML::_('form.token'); ?>
</form>