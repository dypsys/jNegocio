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

$db = & JFactory::getDBO();
$user = & JFactory::getUser();
$nullDate = $db->getNullDate();
$document = & JFactory::getDocument();

jFWBase::load('jFWHelperBase', 'helpers._base');
jFWBase::load('jFWUrl', 'library.url');

$script = array();
$script[] = 'window.addEvent(\'domready\', function() {';
$script[] = 'var necformlist = new Negocio.formlist.App({';
$script[] = 'locale:\'' . $this->config->default_lang . '\'';
$script[] = '});';
$script[] = '});';
$document->addScriptDeclaration(implode("\n", $script));

$tbl_key = $this->idkey;
$state = @$this->state;
$ordering = (@$state->filter_order == 'tbl.lft');
$saveOrder = (@$state->filter_order == 'tbl.lft' && @$state->filter_order_Dir == 'asc');

if (count($this->rows)) {
    // Preprocess the list of items to find ordering divisions.
    foreach ($this->rows as &$item) {
        $this->ordering[$item->parent_id][] = $item->category_id;
    }
}
?>
<form action="<?php echo JRoute::_($this->action); ?>" method="post" name="adminForm" id="adminForm">
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
                <th class="title"><?php echo jFWGrid::jFWsort('COM_JNEGOCIO_CATEGORY_NAME_LABEL', 'tbl.name', @$state->filter_order_Dir, @$state->filter_order); ?></th>
                <th width="1%" class="nowrap center"><?php echo JText::_('COM_JNEGOCIO_IMAGEN'); ?></th>
                <th width="1%" class="nowrap center"><?php echo jFWGrid::jFWsort('COM_JNEGOCIO_PUBLISHED', 'tbl.published', @$state->filter_order_Dir, @$state->filter_order); ?></th>
                <th width="10%">
                    <?php echo @jFWGrid::jFWsort('COM_JNEGOCIO_ORDER', "tbl.lft", @$state->filter_order_Dir, @$state->filter_order); ?>
                    <?php echo $ordering ? jFWGrid::jFWorder(@$this->rows, 'filesave.png', 'saveorder') : ''; ?>            	
                </th>
                <th width="1%" class="nowrap center"><?php echo jFWGrid::jFWsort('COM_JNEGOCIO_FIELD_ID_LABEL', 'tbl.' . $tbl_key, @$state->filter_order_Dir, @$state->filter_order); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php
            $k = 0;
            for ($i = 0, $n = count($this->rows); $i < $n; $i++) {
                $row = $this->rows[$i];

                $orderkey = array_search($row->$tbl_key, $this->ordering[$row->parent_id]);

                $link = 'index.php?option=' . jFWBase::getComponentName() . '&controller=' . $this->_name . '&view=' . $this->_name . '&task=edit&amp;cid[]=' . $row->$tbl_key;
                $published = jFWGrid::jFWpublished($row->published, $i);
                $checked = jFWGrid::checkedout($row, $i, 'id');
                ?>
                <tr class="row<?= @$k; ?>">
                    <td width="7"><?php echo @$checked; ?></td>

                    <td align="left">
                        <?php
                        if ($row->level >= 1) {
                            echo str_repeat('<span class="gi">|&mdash;</span>', $row->level - 1);
                        }

                        if ($row->checked_out && ( $row->checked_out != $this->user->get('id') )) {
                            echo htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8');
                        } else {
                            ?>
                            <span class="editlinktip hasTip" title="<?= @JText::_('COM_JNEGOCIO_EDIT'); ?>::<?php echo $row->name; ?>">
                                <a href="<?php echo $link; ?>">
                                    <?= @htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'); ?>
                                </a></span>
                            <?php
                        }
                        ?>
                    </td>

                    <td align="center">
                        <?php
                        if (!empty($row->attachment) && JFile::exists( JPATH_SITE .DS. $row->locationuri .DS. $row->attachment)) {
                            $src = JURI::root() . $row->locationurl . '/' . $row->attachment;
                            $src_thumbs = JURI::root() . $row->locationurl . '/thumbs/' . $row->attachment;
                            $img_thumb = "<img src='" . jFWBase::getURL('icons'). "16/media.png' align='center' border='0' >";

                            echo jFWUrl::popup( $src, $img_thumb, array('update' => false, 'img' => true));
                        }
                        ?>
                    </td>
                    
                    <td align="center"><?php echo @$published; ?></td>
                    <td class="order">
                        <?php if ($saveOrder) : ?>
                            <span><?php echo $this->pageNav->orderUpIcon($i, isset($this->ordering[$row->parent_id][$orderkey - 1]), 'orderup', 'Move Up', $ordering); ?></span>
                            <span><?php echo $this->pageNav->orderDownIcon($i, $this->pageNav->total, isset($this->ordering[$row->parent_id][$orderkey + 1]), 'orderdown', 'Move Down', $ordering); ?></span>
                        <?php endif; ?>
                        <?php $disabled = $saveOrder ? '' : '"disabled=disabled"'; ?>				
                        <input type="text" name="order[]" size="5" value="<?php echo $orderkey + 1; ?>" <?php echo $disabled ?> class="text-area-order" />
                        <?php $originalOrders[] = $orderkey + 1; ?>
                    </td>
                    <td align="center"><?php echo @$row->$tbl_key; ?></td>
                </tr>
                <?php
                $k = 1 - $k;
            }
            ?>

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
                <td colspan="20"><?php echo @$this->pageNav->getListFooter(); ?></td>
            </tr>
        </tfoot>

    </table>
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="filter_order" value="<?php echo @$state->filter_order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo @$state->filter_order_Dir; ?>" />
    <?php echo @JHTML::_('form.token'); ?>
</form>