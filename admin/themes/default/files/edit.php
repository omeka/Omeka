<?php
$fileTitle = metadata('file', 'display title');
if ($fileTitle != '') {
    $fileTitle = ': &quot;' . $fileTitle . '&quot; ';
} else {
    $fileTitle = '';
}
$fileTitle = __('Edit File #%s', metadata('file', 'id')) . $fileTitle;

queue_js_file(['vendor/tinymce/tinymce.min', 'elements', 'tabs']);
echo head(['title' => $fileTitle, 'bodyclass' => 'files edit']);
include 'form-tabs.php';
echo flash();
?>
<form method="post" action="">
    <section class="seven columns alpha" id="edit-form">
        <?php echo file_markup($file); ?>
        <div id="file-metadata">
            <?php foreach ($tabs as $tabName => $tabContent): ?>
            <?php if (!empty($tabContent)): ?>
                <div id="<?php echo text_to_id(html_escape($tabName)); ?>-metadata">
                    <fieldset class="set">
                        <h2><?php echo html_escape(__($tabName)); ?></h2>
                        <?php echo $tabContent; ?>
                    </fieldset>
                </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div> <!-- end file-metadata div -->
        <?php fire_plugin_hook('admin_files_form', ['file' => $file, 'view' => $this]); ?>
    </section>
    <?php echo $csrf; ?>
    <section class="three columns omega">
        <div id="save" class="panel">
            <input type="submit" name="submit" class="submit big green button" value="<?php echo __('Save Changes'); ?>" id="file_edit" /> 
            <?php if (is_allowed('Files', 'delete')): ?>
                <?php echo link_to($file, 'delete-confirm', __('Delete'), ['class' => 'big red button delete-confirm']); ?>
            <?php endif; ?>
            <?php fire_plugin_hook("admin_files_panel_buttons", ['view'=>$this, 'record'=>$file]); ?>
            <div id="alt-text-form" class="field">
                <?php echo $this->formLabel('file-alt-text', __('Alt Text'));?>
                <div class="inputs">
                    <p class="explanation"><?php echo __('Provide a brief description of visual files to screen reader users.'); ?></p>
                    <?php
                        echo $this->formTextarea(
                            'alt_text',
                            $file->alt_text,
                            [
                                'id' => 'file-alt-text',
                                'rows' => '5'
                            ]
                        );
                    ?>
                </div>
            </div>
            <?php fire_plugin_hook("admin_files_panel_fields", ['view'=>$this, 'record'=>$file]); ?>
        </div>
    </section>
</form>
<script type="text/javascript" charset="utf-8">
jQuery(document).ready(function () {
    Omeka.Tabs.initialize();
    Omeka.wysiwyg({
        selector: false,
        forced_root_block: false
    });

    // Must run the element form scripts AFTER reseting textarea ids.
    jQuery(document).trigger('omeka:elementformload');
});

jQuery(document).bind('omeka:elementformload', function (event) {
    Omeka.Elements.makeElementControls(event.target, <?php echo js_escape(url('elements/element-form')); ?>,'File'<?php if ($id = metadata('file', 'id')) echo ', '.$id; ?>);
    Omeka.Elements.enableWysiwyg(event.target);
});
</script>
<?php echo foot(); ?>
