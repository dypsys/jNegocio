<?php
/**
* @version		$Id$
* @package		Joomla
* @subpackage	jNegocio
* @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
* @license		Comercial License
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

?>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td valign="top">
        <div id="cpanel">
        	<?php 
        	$buttons = array();
        	$buttons[] = array( 
        			'link' 	=> JRoute::_('index.php?option='.jFWBase::getComponentName().'&view=countries'), 
        			'image' => jFWBase::getURL('icons', false) . '48/countries.png',
        			'text'	=> JText::_('NEC_COUNTRIES')
        		);
        	$buttons[] = array( 
        			'link' 	=> JRoute::_('index.php?option='.jFWBase::getComponentName().'&view=currencies'), 
        			'image' => jFWBase::getURL('icons', false) . '48/currencies.png',
        			'text'	=> JText::_('NEC_CURRENCIES')
        		);
        	foreach($buttons as $button) { 
        	?>
			<div class="icon-wrapper">
				<div class="icon">
					<a href="<?php echo $button['link']; ?>">
						<?php echo JHtml::_('image', $button['image'], NULL, NULL, true); ?>
						<span><?php echo $button['text']; ?></span></a>
				</div>
			</div>
			<?php } ?>
		</div>
		</td>
	</tr>
</table>