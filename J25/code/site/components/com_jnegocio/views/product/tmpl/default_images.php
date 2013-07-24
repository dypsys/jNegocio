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

$document   = & JFactory::getDocument();
$script = array();
$script[] = "jQuery(window).load(function(){";

$script[] = "jQuery('[id^=\"myCarousel\"]').each(function(index, element) {";
$script[] = "jQuery(this)[index].slide = null;";
$script[] = "});";

$script[] = "jQuery('[id^=\"myCarousel\"]').carousel();";
$script[] = "});";
$document->addScriptDeclaration(implode("\n", $script));

// $document->addScript(jFWBase::getUrl('js', false) . 'jquery/cloud-zoom/cloud-zoom.js');
// $document->addStyleSheet(jFWBase::getUrl('js', false) . 'jquery/cloud-zoom/cloud-zoom.css');

if (count($this->images)) {
    
    $htmlimage = '';
    if (count($this->images)>=1) {
        
        if (empty($this->images[0]->alt)) {
            $title = $this->row->name;
        } else {
            $title = $this->images[0]->alt;
        }
        
        $htmlimage = '<div class="thumbnail main_image">';
        // $htmlimage .= '<a href="' . JURI::root() . $this->images[0]->locationurl . '/' . $this->images[0]->attachment . '" title="' .$title . '" class="cloud-zoom" id="zoom1" rel="adjustX: -1, adjustY:-1, tint:\'#ffffff\',tintOpacity:0.1, zoomWidth:364">';
        $htmlimage .= '<img src="' . JURI::root() . $this->images[0]->locationurl . '/full/' . $this->images[0]->attachment . '" title="' .$title . '" alt="' .$title . '" id="image" />';
        // $htmlimage .= '</a>';
        // $htmlimage .= '<div class="zoom-b hidden-phone">';
        // $htmlimage .= '<a id="zoom-cb" class="colorbox" href="' . JURI::root() . $this->images[0]->locationurl . '/' . $this->images[0]->attachment . '">Zoom</a>';
        // $htmlimage .= '</div>';        
        $htmlimage .= '</div>';
    }
    
    $htmladditional = '';
    if( count($this->images)>=2) {
        $htmladditional = '<div class="row-fluid">';
        $htmladditional .= '<div class="carousel slide span12" id="myCarousel">';
        $htmladditional .= '<div class="carousel-inner">';
        $count_images_to_row = 3;
        $itemspan = 12 / $count_images_to_row;
        $ncont = 0;
        foreach ($this->images as $k=>$image) {
            if ($ncont%$count_images_to_row==0) {
                if ($ncont == 0) {
                    $htmladditional .= '<div class="item active">';
                } else {
                    $htmladditional .= '</ul></div>';
                    $htmladditional .= '<div class="item">';
                }
                $htmladditional .= '<ul class="thumbnails">';
            }
            
            $Img_big = JURI::root() . $image->locationurl . '/' . $image->attachment;
            $Img_norm = JURI::root() . $image->locationurl . '/full/' . $image->attachment;
            $Img_thumb = JURI::root() . $image->locationurl . '/thumb/' . $image->attachment;
            
            if (empty($image->alt)) {
                $title = $this->row->name;
            } else {
                $title = $image->alt;
            }

            $htmladditional .= '<li class="span'. $itemspan .'">';
            $htmladditional .= '<div class="thumbnail">';
            // $htmladditional .= '<a href="' . $Img_big. '" title="'. $title .'" class="cloud-zoom" rel="useZoom: \'zoom1\', smallImage: \''.$Img_norm.'\' ">';
            $htmladditional .= '<img src="' . $Img_thumb . '" class="img_carrusel" title="'.$title.'" alt="'.$title.'" />';
            // $htmladditional .= '</a>';
            $htmladditional .= '</div>';
            $htmladditional .= '</li>';
            $ncont++;
        }
        if ($ncont%$count_images_to_row!=$count_images_to_row-1) { $htmladditional .= '</ul></div>';}
        
        $htmladditional .= '</div>';
        $htmladditional .= '<a data-slide="prev" href="#myCarousel" class="left carousel-control">‹</a>';
        $htmladditional .= '<a data-slide="next" href="#myCarousel" class="right carousel-control">›</a>';
        $htmladditional .= '</div>';
        $htmladditional .= '</div>';
    }
    
    echo $htmlimage;
    echo $htmladditional;
}