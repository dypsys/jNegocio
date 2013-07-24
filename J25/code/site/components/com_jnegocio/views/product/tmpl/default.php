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
?>
<div class="necShop">
    <div class="necProduct-details row-fluid">
        <div class="necProduct-img span6">
            <?= @$this->loadTemplate('images'); ?>
        </div>
        <div class="necProduct-info span6">
            <?= @$this->loadTemplate('info'); ?>
        </div>
    </div>
    <div class="necProduct-descriptions row-fluid">
        <div class="span12">
            <?= @$this->loadTemplate('tabs'); ?>
        </div>
    </div>
    
    <div class="necProduct-related row-fluid">
        <div class="span12">
            <?= @$this->loadTemplate('related'); ?>
        </div>
    </div>
</div>
