<?php 
$pathToConvert = get_option('path_to_convert');
if (empty($pathToConvert) && is_allowed('Settings', 'edit')): ?>
    <div class="error"><?php echo __('The path to Image Magick has not been set. No derivative images will be created. If you would like Omeka to create derivative images, please add the path to your settings form.'); ?></div>
<?php endif; ?>
<?php if (metadata('item', 'has files')): ?>
    <p class="description">You can click and drag the files into your preferred display order.</p>
    <div id="file-list">
        <ul class="sortable">
        <?php foreach( $item->Files as $key => $file ): ?>
            <li class="file">
                <div class="sortable-item">
                    <?php echo link_to($file, 'show', html_escape($file->original_filename), array()); ?>
                    <?php echo $this->formHidden("order[{$file->id}]", $file->order, array('class' => 'file-order')); ?>
                    <ul class="action-links">
                        <li><?php echo link_to($file, 'edit', __('Edit'), array('class'=>'edit')); ?></li>
                        <li><a href="#" class="delete"><?php echo __('Delete '); ?></a> <?php echo $this->formCheckbox('delete_files[]', $file->id, array('checked' => false)); ?></li>
                    </ul>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="add-new-item"><?php echo __('Add New Files'); ?></div>
<div class="drawer-contents">
    <p>The maximum file size is <?php echo max_file_size(); ?>.</p>
    
    <div class="field two columns alpha" id="file-inputs">
        <label><?php echo __('Find a File'); ?></label>
    </div>

    <div class="files four columns omega">
        <input name="file[0]" type="file">
    </div>
</div>

<?php fire_plugin_hook('admin_items_form_files', array('item' => $item, 'view' => $this)); ?>
