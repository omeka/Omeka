<?php 
$pathToConvert = get_option('path_to_convert');
if (empty($pathToConvert) && is_allowed('Settings', 'edit')): ?>
    <div class="error"><?php echo __('The path to Image Magick has not been set. No derivative images will be created. If you would like Omeka to create derivative images, please add the path to your settings form.'); ?></div>
<?php endif; ?>
<?php if ( item_has_files() ): ?>
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
    
    <div id="add-more-files">
    <label for="add_num_files"><?php echo __('Find a File'); ?></label>
        <div class="files">
        <?php $numFiles = (int)@$_REQUEST['add_num_files'] or $numFiles = 1; ?>
        <?php 
        echo $this->formText('add_num_files', $numFiles, array('size' => 2));
        echo $this->formSubmit('add_more_files', 'Add this many files'); 
        ?>
        </div>
    </div>
    
    <div class="field two columns alpha" id="file-inputs">
        <label><?php echo __('Find a File'); ?></label>
    </div>
    
    <?php for($i=0;$i<$numFiles;$i++): ?>
    <div class="files">
        <input name="file[<?php echo $i; ?>]" id="file-<?php echo $i; ?>" type="file" class="fileinput" />          
    </div>
    <?php endfor; ?>
</div>

<?php fire_plugin_hook('admin_append_to_items_form_files', array('item' => $item, 'view' => $this)); ?>
