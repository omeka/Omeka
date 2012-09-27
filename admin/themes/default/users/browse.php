<?php
$pageTitle = __('Browse Users') . ' ' . __('(%s total)', $total_records);
echo head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'users primary'));?>

<?php if (is_allowed('Users', 'add')): ?>
    <?php echo link_to('users', 'add', __('Add a User'), array('class'=>'small green button')); ?>
<?php endif; ?>
<?php echo flash(); ?>

            <form class="top" action="<?php echo html_escape(url('users/batch-edit')); ?>" method="post" accept-charset="utf-8">
    
                <?php echo pagination_links(); ?>
                
                <table id="users">
                    <thead>
                        <tr>
                            <th><?php echo __('Username') ?></th>
                            <th><?php echo __('Real Name'); ?></th>
                            <th><?php echo __('Email'); ?></th>
                            <th><?php echo __('Role'); ?></th>            
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach( $users as $key => $user ): ?>
                        <tr class="<?php if (current_user()->id == $user->id) echo 'current-user '; ?><?php if($key%2==1) echo 'even'; else echo 'odd'; ?><?php if(!$user->active): ?> inactive<?php endif; ?>">
                            <td>
                            <?php echo html_escape($user->username); ?> <?php if(!$user->active): ?>(<?php echo __('inactive'); ?>)<?php endif; ?>
                            <ul class="action-links group">
                                <?php if (is_allowed($user, 'edit')): ?>
                                <li><?php echo link_to($user, 'edit', __('Edit'), array('class'=>'edit')); ?></li>
                                <?php endif; ?>     
                                <?php if (is_allowed($user, 'delete')): ?>
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
<?php echo foot();?>
