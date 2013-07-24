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

jimport('joomla.filesystem.file');

$db = & JFactory::getDBO();
$user = & JFactory::getUser();
$nullDate = $db->getNullDate();
$document = & JFactory::getDocument();

jFWBase::load('jFWHelperBase', 'helpers._base');
jFWBase::load('HelperSelect', 'helpers.select' );
jFWBase::load('HelperProduct', 'helpers.product');
jFWBase::load('HelperCategory', 'helpers.category');
jFWBase::load( 'HelperCurrency', 'helpers.currency' );
jFWBase::load('jFWUrl', 'library.url');

$script = array();
$script[] = 'window.addEvent(\'domready\', function() {';
$script[] = 'var necformlist = new Negocio.formlist.App({';
$script[] = 'locale:\'' . $this->config->default_lang . '\'';
$script[] = '});';
$script[] = '});';
$document->addScriptDeclaration(implode("\n", $script));

$tbl_key    = $this->idkey;
$state      = @$this->state;
$attribs    = array('class' => 'inputbox necFilter', 'size' => '1', 'onchange' => 'document.adminForm.submit();');
$ordering   = (@$state->filter_order == 'tbl.ordering');

$languages  = jFWBase::getClass('HelperLanguages', 'helpers.language')->getlang();
$hProduct   = HelperProduct::getInstance('product');
$hCategory  = HelperCategory::getInstance('category');
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
            <?= HelperSelect::category(@$state->filter_categoryid, 'filter_categoryid', $attribs, 'filter_categoryid', true); ?>
            <?= jFWSelect::state(@$state->filter_state); ?>
        </div>
    </fieldset>
    <div class="clr"> </div>

    <table class="adminlist">
        <thead>
            <tr>
                <th width="1%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onClick="Joomla.checkAll(this)" /></th>
                <th class="title"><?php echo jFWGrid::jFWsort('COM_JNEGOCIO_PRODUCT_NAME_LABEL', 'tbl.name', @$state->filter_order_Dir, @$state->filter_order); ?></th>
                <th width="title"><?php echo JText::_('COM_JNEGOCIO_CATEGORY_NAME_LABEL'); ?></th>
                <th class="title" align="right"><?php echo jFWGrid::jFWsort('COM_JNEGOCIO_PRODUCT_PRICE_LABEL', 'price', @$state->filter_order_Dir, @$state->filter_order); ?></th>
                <th width="1%" class="nowrap center"><?php echo JText::_('COM_JNEGOCIO_IMAGEN'); ?></th>
                <th width="1%" class="nowrap center"><?php echo jFWGrid::jFWsort('COM_JNEGOCIO_PUBLISHED', 'tbl.published', @$state->filter_order_Dir, @$state->filter_order); ?></th>
                <th width="1%" class="nowrap center"><?php echo jFWGrid::jFWsort('COM_JNEGOCIO_FIELD_ID_LABEL', 'tbl.' . $tbl_key, @$state->filter_order_Dir, @$state->filter_order); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php
            $k = 0;
            for ($i = 0, $n = count($this->rows); $i < $n; $i++) {
                $row = $this->rows[$i];

                $link = 'index.php?option=' . jFWBase::getComponentName() . '&controller=' . $this->_name . '&view=' . $this->_name . '&task=edit&amp;cid[]=' . $row->$tbl_key;
                $published = jFWGrid::jFWpublished($row->published, $i);
                $checked = jFWGrid::checkedout($row, $i, 'id');
                ?>
                <tr class="row<?= @$k; ?>">
                    <td width="7"><?php echo @$checked; ?></td>

                    <td align="left">
                        <?php
                        if ($row->checked_out && ( $row->checked_out != $this->user->get('id') )) {
                            echo htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8');
                        } else { ?>
                            <a href="<?php echo $link; ?>">
                            <?php echo @htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        <?php }?>
                    </td>

                    <td align="center">
                        <?php
                        $pcategories = $hProduct->getCategories($row->$tbl_key);
                        foreach($pcategories as $pcategory) {
                            // echo var_dump($pcategory)."<hr/>";
                            $aPath = $hCategory->getPathName($pcategory->category_id);
                            $aPTotal = count($aPath);
                            foreach($aPath as $keycat=>$cate) {
                                if ($keycat>=1) {
                                    $fieldname = $languages->getField('name');
                                    echo $cate->$fieldname;
                                    if ($keycat<=$aPTotal-2) {
                                        echo ' <b>&gt;</b> ';
                                    }
                                }
                            }
                            echo "<br/>";
                        }
                        ?>
                    </td>
                    
                    <td align="right"><?= HelperCurrency::format( @$row->price ); ?></td>
                    
                    <td align="center">
                    </td>
                    
                    <td align="center"><?php echo @$published; ?></td>
                    <td align="center"><?php echo @$row->$tbl_key; ?></td>
                </tr>
                <?php
                $k = 1 - $k;
            }?>

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