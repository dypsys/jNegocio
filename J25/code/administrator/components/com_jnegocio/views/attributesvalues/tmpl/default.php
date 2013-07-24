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

$db = & JFactory::getDBO();
$user = & JFactory::getUser();
$nullDate = $db->getNullDate();
$document = & JFactory::getDocument();

jFWBase::load('jFWHelperBase', 'helpers._base');

$script = array();
$script[] = 'window.addEvent(\'domready\', function() {';
$script[] = 'var necformlist = new Negocio.formlist.App({';
$script[] = 'locale:\'' . $this->config->default_lang . '\'';
$script[] = '});';
$script[] = '});';
$document->addScriptDeclaration(implode("\n", $script));

$tbl_key = $this->idkey;
$state = @$this->state;
$attribs = array('class' => 'inputbox', 'size' => '1', 'onchange' => 'document.adminForm.submit();');
$ordering = (@$state->filter_order == 'tbl.ordering');
?>
<form action="<?= @JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('COM_JNEGOCIO_FILTER'); ?>:</label>
            <input type="text" name="search" id="search" value="<?php echo $this->escape(@$state->search); ?>" title="<?php echo JText::_('COM_JNEGOCIO_FILTER_SEARCH_DESC'); ?>" />

            <a href="javascript:;" class="nec_btn nec_action_applyfilters"><span class="icon applyfilters"></span><?= @JText::_('COM_JNEGOCIO_FILTER_APPLY'); ?></a>
            <a href="javascript:;" class="nec_btn nec_action_clearfilters"><span class="icon clearbutton"></span><?= @JText::_('COM_JNEGOCIO_FILTER_CLEAR'); ?></a>			
        </div>
        <div class="filter-select fltrt">
            
        </div>
    </fieldset>
    <div class="clr"> </div>

    <table class="adminlist">
        <thead>
            <tr>
                <th width="1%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onClick="Joomla.checkAll(this)" /></th>
                <th class="title"><?php echo jFWGrid::jFWsort('COM_JNEGOCIO_ATTRIBUTESVALUE_NAME_LABEL', 'tbl.name', @$state->filter_order_Dir, @$state->filter_order); ?></th>
                <th width="10%">
                    <?php echo @jFWGrid::jFWsort('COM_JNEGOCIO_ORDER', "tbl.ordering", @$state->filter_order_Dir, @$state->filter_order); ?>
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

                $link = 'index.php?option=' . jFWBase::getComponentName() . '&controller=' . $this->_name . '&view=' . $this->_name . '&task=edit&amp;cid[]=' . $row->$tbl_key;
                $checked = jFWGrid::checkedout($row, $i, 'id');
                ?>
                <tr class="row<?= @$k; ?>">
                    <td width="7"><?php echo @$checked; ?></td>

                    <td align="left">
                        <?php
                        if ($row->checked_out && ( $row->checked_out != $this->user->get('id') )) {
                            echo htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8');
                        } else {
                            ?>
                            <a href="<?php echo $link; ?>">
                                <?php echo @htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        <?php } ?>
                    </td>

                    <td class="order">
                        <span><?php echo $this->pageNav->orderUpIcon($i, true, 'orderup', 'Move Up', $ordering); ?></span>
                        <span><?php echo $this->pageNav->orderDownIcon($i, $n, true, 'orderdown', 'Move Down', $ordering); ?></span>
                        <?php $disabled = $ordering ? '' : '"disabled=disabled"'; ?>				
                        <input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="text-area-order" />
                    </td>
                    <td align="center"><?= @$row->$tbl_key; ?></td>
                </tr>
                <?php
                $k = 1 - $k;
            }
            ?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="20"><?php echo @$this->pageNav->getListFooter(); ?></td>
            </tr>
        </tfoot>
    </table>
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="option" value="<?= jFWBase::getComponentName(); ?>" />
    <input type="hidden" name="controller" value="<?= @$this->_name; ?>" />
    <input type="hidden" name="view" value="<?= @$this->_name; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="filter_order" value="<?= @$state->filter_order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?= @$state->filter_order_Dir; ?>" />
    <input type="hidden" name="filter_attrid" value="<?= @$state->filter_attrid; ?>" />
    <?= @JHTML::_('form.token'); ?>
</form>