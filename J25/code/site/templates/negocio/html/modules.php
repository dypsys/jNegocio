<?php
/**
 * @version	$Id$
 * @package	Joomla
 * @subpackage	Templates.jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 */
// No direct access.
defined('_JEXEC') or die;

function modChrome_aside($module, &$params, &$attribs) {
    if ($module->content) {
        echo "<div class='moduletable" . htmlspecialchars($params->get('moduleclass_sfx')) ." box'>\n";
        if ($module->showtitle) {
            $headerLevel = isset($attribs['headerLevel']) ? (int) $attribs['headerLevel'] : 3;
            echo "<div class=\"box-title\">\n";
            echo "<h" . $headerLevel . "><span>" . $module->title . "</span></h" . $headerLevel . ">";
            echo "</div>";
        }
        echo "<div class=\"box-content\">\n";
        echo $module->content;
        echo "</div>";
        echo "</div>";
    }
}

function modChrome_well($module, &$params, &$attribs) {
    if ($module->content) {
        echo "<div class=\"well " . htmlspecialchars($params->get('moduleclass_sfx')) . "\">";
        if ($module->showtitle) {
            $headerLevel = isset($attribs['headerLevel']) ? (int) $attribs['headerLevel'] : 3;
            echo "<h" . $headerLevel . " class=\"page-header\">" . $module->title . "</h" . $headerLevel . ">";
        }
        echo $module->content;
        echo "</div>";
    }
}

/**
 * beezDivision chrome.
 *
 * @since	1.6
 */
function modChrome_side($module, &$params, &$attribs) {
    $headerLevel = isset($attribs['headerLevel']) ? (int) $attribs['headerLevel'] : 3;
    if (!empty($module->content)) {
        ?>
        <div class="moduletable<?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?>">
        <?php if ($module->showtitle) { ?> 
                <h<?php echo $headerLevel; ?>>
                    <i class="icon-chevron-sign-right"></i> <?php echo $module->title; ?></h<?php echo $headerLevel; ?>>
            <?php }; ?>
            <div class="moduleSideContent">
            <?php echo $module->content; ?>
            </div>
            <div class="moduleSideBottom"></div>
        </div>
    <?php
    };
}
