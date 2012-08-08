<?php
$pageTitle = __('Browse Users') . ' ' . __('(%s total)', $total_records);
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'users primary'));?>

<?php if (has_permission('Users', 'add')): ?>
    <?php echo link_to('users', 'add', __('Add a User'), array('class'=>'small green button')); ?>
<?php endif; ?>
<?php echo flash(); ?>
<script type="text/javascript">
    jQuery(window).load(function() {
        var itemCheckboxes = jQuery("table#users tbody input[type=checkbox]");
        var globalCheckbox = jQuery('th#batch-edit-heading').html('<input type="checkbox">').find('input');
        var batchEditSubmit = jQuery('.batch-edit-option input');
        
        globalCheckbox.change(function() {
            itemCheckboxes.prop('checked', !!this.checked);
            checkBatchEditSubmitButton();
        });
        
        itemCheckboxes.change(function(){
            if(!this.checked) {
                globalCheckbox.prop('checked', false);
            }
            checkBatchEditSubmitButton();
        });
        
        function checkBatchEditSubmitButton() {
            var checked = false;
            itemCheckboxes.each(function() {
                if (this.checked) {
                    checked = true;
                    return false;
                }
            });
        
            batchEditSubmit.prop('disabled', !checked);
        }    
    });
    
</script>
            <form class="top" action="<?php echo html_escape(uri('users/batch-edit')); ?>" method="post" accept-charset="utf-8">

                <div class="item-actions">
                    <?php if (has_permission('Users', 'edit')): ?>
                    <input type="submit" class="edit-items small blue button" name="submit" value="<?php echo __('Change Role'); ?>" />
                    <?php endif; ?>
                    <?php if (has_permission('Users', 'delete')): ?>
                    <input type="submit" class="red small" name="submit" value="<?php echo __('Delete'); ?>">
                    <?php endif; ?>                    
                </div>
    
                <?php echo pagination_links(array('partial_file' => common('pagination_control'))); ?>
                
                <table id="users" class="full">
                    <thead>
                        <tr>
                            <th class="batch-edit-heading"><?php echo __('Select') ?></th>
                            <th><?php echo __('Username') ?></th>
                            <th><?php echo __('Real Name'); ?></th>
                            <th><?php echo __('Email'); ?></th>
                            <th><?php echo __('Role'); ?></th>            
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach( $users as $key => $user ): ?>
                        <tr class="<?php if (current_user()->id == $user->id) echo 'current-user '; ?><?php if($key%2==1) echo 'even'; else echo 'odd'; ?>">
                            <td class="batch-edit-check"><input type="checkbox" name="users[]" value="<?php echo html_escape($user->username);?>" /></td>
                            <td>
                            <?php echo html_escape($user->username); ?>
                            <ul class="action-links group">
                                <?php if (has_permission($user, 'edit')): ?>
                                <li><?php echo link_to($user, 'edit', __('Edit'), array('class'=>'edit')); ?></li>
                                <?php endif; ?>     
                                <?php if (has_permission($user, 'delete')): ?>
                                <li><?php echo link_to($user, 'delete-confirm', __('Delete'), array('class'=>'delete')); ?></li>
                                <?php endif; ?>
                            </ul>
                           </td>
                            <td><?php echo html_escape($user->name); ?></td>
                            <td><?php echo html_escape($user->email); ?></td>
                            <td><span class="<?php echo html_escape($user->role); ?>"><?php echo html_escape(__(Inflector::humanize($user->role))); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination"><?php echo pagination_links(); ?></div>
        </form>
<?php foot();?>
