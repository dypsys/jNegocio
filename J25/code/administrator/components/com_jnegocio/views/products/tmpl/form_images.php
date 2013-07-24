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

$idkey	= $this->idkey;
?>
<div id="maincontainer_images" class="nec_images">
    <div class="listview">
        <?php
        $ncont = 0;
        for ($ncont = 0, $ntotal = count($this->image_rows); $ncont < $ntotal; $ncont++) {
            $image = $this->image_rows[$ncont];
            $checked = jFWGrid::checkedout($image, $ncont, 'productimage_id');
            ?>
            <div class="gallery">
                <div class="image-preview">
                    <img src="<?= JURI::root() . $image->locationurl . '/full/' . $image->attachment ?>" id="image-preview" width="100px;" />
                </div>

                <div class="details">
                    <div style="display:none;">
                        <?= @$checked; ?>
                        <input name="gallery[<?= $ncont; ?>][productimage_id]" type="text" value="<?= $image->productimage_id; ?>" />
                    </div>
                    <div class="clearfix"></div>

                    <?php
                    foreach ($this->languages as $lang) {
                        $field = "alt_" . $lang->language;
                        ?>
                        <label id="alt_<?php print $lang->language; ?>-lbl" for="Alt_<?php print $lang->language; ?>" class="mainlabel hasTip" title="<?= @JText::_('COM_JNEGOCIO_PRODUCTIMAGE_ALT_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_PRODUCTIMAGE_ALT_DESC'); ?>">
                            <?= @JText::_('COM_JNEGOCIO_PRODUCTIMAGE_ALT_LABEL'); ?> <?php if ($this->multilang) print "(" . $lang->lang . ")"; ?>
                        </label>
                        <input name="gallery[<?= $ncont; ?>][alt_<?php print $lang->language; ?>]" type="text" value="<?= $image->$field; ?>" />
                        <div class="clearfix"></div>
                    <?php } ?>

                    <label for="order_image_field" class="mainlabel"><?= @JText::_('COM_JNEGOCIO_ORDER') ?></label>
                    <span><?php echo $this->image_pageNav->orderUpIcon($ncont, true, 'imageorderup', 'Move Up'); ?></span>
                    <span><?php echo $this->image_pageNav->orderDownIcon($ncont, $ntotal, true, 'imageorderdown', 'Move Down'); ?></span>
                    <input type="text" name="order[]" size="5" value="<?php echo $image->ordering; ?>" class="text-area-order" />
                    <div class="clearfix"></div>

                    <label for="delete_image_field" class="mainlabel"><?= @JText::_('COM_JNEGOCIO_DELETE_FILE') ?></label>
                    <input type="checkbox" name="gallery[<?= $ncont; ?>][delete]" />
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="clearfix"></div>
        <?php } ?>
        <input name="image_total" type="hidden" value="<?= $ntotal; ?>" /><br/>
    </div>
    <div class="clearfix"></div>

    <?php if (@$this->row->$idkey >= 1) { ?>
        <div id="gallerycontainer">
            <div class="plupload_content">
                <div id="fileheader" class="plupload_filelist_header" style="display:none;">
                    <div class="plupload_file_name"><?= @JText::_('COM_JNEGOCIO_GALLERY_HEADER_FILENAME'); ?></div>
                    <div class="plupload_file_status"><span><?= @JText::_('COM_JNEGOCIO_GALLERY_HEADER_STATUS'); ?></span></div>
                    <div class="plupload_file_size"><?= @JText::_('COM_JNEGOCIO_GALLERY_HEADER_SIZE'); ?></div>
                    <div class="plupload_clearer">&nbsp;</div>
                </div>

                <ul id="filelist" class="plupload_filelist"></ul>

                <div class="plupload_filelist_footer">
                    <div class="plupload_file_name">
                        <div class="plupload_buttons">
                            <button id="gallerypickfiles" class="plupload_add"><?= @JText::_('COM_JNEGOCIO_GALLERY_ADD_FILES'); ?></button>
                            <button id="galleryuploadfiles" class="plupload_start" style="display:none;"><?= @JText::_('COM_JNEGOCIO_GALLERY_START_UPLOAD'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    <?php } ?>
</div>

<?php if (@$this->row->$idkey >= 1) {?>
<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function () {
    // Custom example logic
    function myDoc(id) {
        return document.getElementById(id);
    }

    function handleStatus(file) {
        var actionClass;
        if (file.status === plupload.DONE) {actionClass = 'plupload_done';}
        if (file.status === plupload.FAILED) {actionClass = 'plupload_failed';}
        if (file.status === plupload.QUEUED) {actionClass = 'plupload_delete';}
        if (file.status === plupload.UPLOADING) {actionClass = 'plupload_uploading';}
        if (file.hint) {icon.attr('title', file.hint);}
    }

    function iniciarSubida() {
        jQuery('#fileheader').css('display', 'block');
        uploader.start();
    }
    
    function finalizarSubida() {
        jQuery('#fileheader').css('display', 'none');
        Joomla.submitbutton('apply');
    }

    function checkQueue() {
        var filesQUEUED = 0;
        var filesDONE = 0;
        jQuery.each(uploader.files, function(i, file) {
            if (file.status === plupload.QUEUED) { filesQUEUED += 1; }
            if (file.status === plupload.DONE) { filesDONE += 1; }
        });
        if (filesQUEUED >= 1) { iniciarSubida(); }
        if (filesDONE === uploader.files.length) { finalizarSubida(); }
        
    }

    var uploader = new plupload.Uploader({
        runtimes : '<?php echo $this->config->get('plupload_runtime', 'gears,html5,flash,silverlight,browserplus'); ?>',
        browse_button : 'gallerypickfiles',
        max_file_size : '<?php echo $this->config->get('plupload_max_file_size', 1048576) . $this->config->get('$plupload_max_file_size_unit', 'kb'); ?>',
        url : 'index.php?option=<?php echo jFWBase::getComponentName();?>&controller=<?php echo $this->_name; ?>&no_html=1&itemid=<?php echo @$this->row->$idkey; ?>&task=uploadmedia&<?php echo JSession::getFormToken(); ?>=1',
        flash_swf_url : '<?php echo jFWBase::getUrl('js', false); ?>jquery/plupload/plupload.flash.swf',
        silverlight_xap_url : '<?php echo jFWBase::getUrl('js', false); ?>jquery/plupload/plupload.silverlight.xap',        
        <?php
        if ($this->config->get('plupload_chunk_size', 0) !== 0 || $this->config->get('plupload_chunk_unit', 'kb') !== "") {
            echo "chunk_size : '" . $this->config->get('plupload_chunk_size', 0) . $this->config->get('plupload_chunk_unit', 'kb') . "',";
        }
        if ($this->config->get('plupload_enable_image_resizing', 0)) {
            echo "resize : {"
                . "width : " . $this->config->get('plupload_resize_width', '640') . ","
                . "height : " . $this->config->get('plupload_resize_height', '480') . ","
                . "quality : " . $this->config->get('plupload_resize_quality', '90')
            . "},\n";
        }
        
        echo "filters : [{ title : 'Image files',";
        echo "extensions : \"". $this->config->get('plupload_image_file_extensions', 'jpg,png,gif') . "\"";
        echo "}]\n";
        ?>
    });

    uploader.bind('Init', function(up, params) {
        // myDoc('galleryfilelist').innerHTML = "<div>Current runtime: " + params.runtime + "</div>";
    });
    
    uploader.bind('FilesAdded', function(up, files) {
        var fileList = jQuery('ul.plupload_filelist').html('');
        var inputHTML = '';
        jQuery.each(files, function(i, file) {
            inputHTML = '';
            if (file.status === plupload.DONE) {
            }

            fileList.append(
                '<li id="' + file.id + '">' +
                '<div class="plupload_file_name"><span>' + file.name + '</span></div>' +
                '<div class="plupload_file_status">' + file.percent + '%</div>' +
                '<div class="plupload_file_size">' + plupload.formatSize(file.size) + '</div>' +
                '<div class="plupload_clearer">&nbsp;</div>' +
                inputHTML +
                '</li>'
            );
        });
    });

    uploader.bind('FileUploaded', function(up, file) {
        handleStatus(file);
    });

    uploader.bind('UploadProgress', function(up, file) {
        jQuery('#' + file.id + ' div.plupload_file_status').html(file.percent + '%');
        handleStatus(file);
        if (file.status === plupload.DONE) { jQuery('#' + file.id).css('display', 'none'); };
    });
        
    uploader.bind('QueueChanged', function(up) { checkQueue(); });
    uploader.bind('StateChanged', function(up) { checkQueue(); });
    
    uploader.bind('Error', function(up, err) {
        var file = err.file, message;
        if (file) {
            message = err.message;
            if (err.details) {message += " (" + err.details + ")";}
            if (err.code === plupload.FILE_SIZE_ERROR) {alert( _("Error: File too large: ") + file.name);}
            if (err.code === plupload.FILE_EXTENSION_ERROR) {alert( _("Error: Invalid file extension: ") + file.name);}

            file.hint = message;
            jQuery('#' + file.id).attr('class', 'plupload_failed').find('a').css('display', 'block').attr('title', message);
        }
        up.refresh(); // Reposition Flash/Silverlight
    });

    myDoc('galleryuploadfiles').onclick = function() {
            iniciarSubida();
            return false;
        };

    uploader.init();
    
});
</script>
<?php } ?>