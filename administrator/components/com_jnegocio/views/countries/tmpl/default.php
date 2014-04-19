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

$app		= JFactory::getApplication();
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('filter_order'));
$listDirn	= $this->escape($this->state->get('filter_order_Dir'));
$tbl_key 	= $this->idkey;

?>
<form action="<?php echo JRoute::_($this->action); ?>" method="post" name="adminForm" id="adminForm">
    <div id="j-sidebar-container" class="span2">
        <?php
        jFWBase::load('HelperManifest', 'helpers.manifest');
        $manifest = new HelperManifest();
        echo $manifest->menuadmin();
        ?>
    </div>
    <div id="j-main-container" class="span10">

        <div class="neg-stools clearfix">
            <div class="clearfix">
                <div class="neg-stools-container-bar">
                    <label for="filter_search" class="element-invisible">
                        <?php echo JText::_('JSEARCH_FILTER'); ?>
                    </label>
                    <div class="btn-wrapper input-append">
                        <input type="text" class="neg-stools-field-search" name="search" id="filter_search" value="<?php echo htmlspecialchars(@$this->state->get('search'), ENT_QUOTES); ?>" placeholder="Buscar" />
                        <button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
                            <i class="icon-search"></i>
                        </button>
                    </div>

                    <div class="btn-wrapper">
                        <button type="button" class="btn hasTooltip neg-stools-btn-clear" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>">
                            <?php echo JText::_('JSEARCH_FILTER_CLEAR');?>
                        </button>
                    </div>
                </div>

                <div class="neg-stools-container-list hidden-phone hidden-tablet">
                    <div class="ordering-select hidden-phone">
                        <div class="neg-stools-field-list">
                            <?php echo $this->pagination->getLimitBox(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (empty($this->items)) : ?>
            <div class="alert alert-no-items">
                <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php else : ?>
            <table class="table table-striped" id="countryList">
                <thead>
                    <tr>
                        <th width="1%" class="hidden-phone"><?php echo JHtml::_('grid.checkall'); ?></th>
                        <th><?php echo JHtml::_('grid.sort', 'COM_JNEGOCIO_COUNTRY_NAME_LABEL', 'tbl.name', $listDirn, $listOrder); ?></th>
                        <th width="10%" class="nowrap hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_JNEGOCIO_COUNTRY_ISO_2_LABEL', 'tbl.isocode_2', $listDirn, $listOrder); ?></th>
                        <th width="10%" class="nowrap hidden-phone"><?php echo JHtml::_('grid.sort', 'COM_JNEGOCIO_COUNTRY_ISO_3_LABEL', 'tbl.isocode_3', $listDirn, $listOrder); ?></th>
                        <th width="1%" class="nowrap center hidden-phone"><?php echo JHTML::_('grid.sort', 'COM_JNEGOCIO_FIELD_ID_LABEL', 'tbl.' . $tbl_key, $listDirn, $listOrder); ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td colspan="13">
                            <?php echo $this->pagination->getListFooter(); ?>
                        </td>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    $n = count($this->items);
                    foreach ($this->items as $i => $item) :
                        $link = 'index.php?option='.jFWBase::getComponentName().'&controller='.$this->getName().'&view='.$this->getName().'&task=edit&amp;cid[]='. $item->$tbl_key;
                        ?>
                        <tr class="row<?php echo $i % 2; ?>">
                            <td class="center hidden-phone"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                            <td>
                                <a href="<?php echo $link; ?>">
                                    <?php echo @htmlspecialchars($item->name, ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </td>
                            <td class="small hidden-phone">
                                <a href="<?php echo $link; ?>">
                                    <?php echo @htmlspecialchars($item->isocode_2, ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </td>
                            <td class="small hidden-phone">
                                <a href="<?php echo $link; ?>">
                                    <?php echo @htmlspecialchars($item->isocode_3, ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </td>
                            <td class="center hidden-phone"><?php echo @$item->$tbl_key; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
