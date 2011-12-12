<?php
$pageTitle = __('Browse Users');
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'users primary'));?>
<h1><?php echo $pageTitle; ?> <?php echo __('(%s total)', $total_records); ?></h1>
<?php if (has_permission('Users', 'add')): ?>
    <p id="add-user" class="add-button"><?php echo link_to('users', 'add', __('Add a User'), array('class'=>'add-user')); ?></p>    
<?php endif; ?>
<?php common('settings-nav'); ?>
<div id="primary">
<?php echo flash(); ?>
<form action="<?php echo html_escape(current_uri()); ?>" id="sort-users-form" method="get" accept-charset="utf-8">
    <fieldset>
        <p><?php echo __('Search Users'); ?>:</p>
        <?php echo $this->formSelect('role', @$_GET['role'], array(), 
            array(''=>__('Select Role')) + get_user_roles()); ?>
        <?php echo $this->formSelect('active', @$_GET['active'], array(),
            array(''=>__('Select Status'),  '1'=>__('Active'), '0'=>__('Inactive'))); ?>
        <?php echo $this->formSelect('sort', @$_GET['sort'], array(),
            array(  ''=>__('Sort By'), 
                    'first_name'=>__('First Name'),
                    'last_name'=>__('Last Name'),
                    'institution'=>__('Institution Name'),
                    'role'=>__('Role'),
                    'username'=>__('Username'))); ?>
        <?php echo $this->formSelect('sortOrder', @$_GET['sortOrder'], array(),
            array( ''=>__('Sort Order'),
                   'asc'=>__('Ascending'),
                   'desc'=>__('Descending'))); ?>
                   <input type="submit" class="submit-form" name="submit" value="<?php echo __('Search'); ?>" />
                   
    </fieldset>
</form>

<div class="pagination"><?php echo pagination_links(); ?></div>
<table id="users">
    <thead>
        <tr>
            <th><?php echo __('Username') ?></th>
            <th><?php echo __('Real Name'); ?></th>
            <th><?php echo __('Email'); ?></th>
            <th><?php echo __('Role'); ?></th>
            <?php if (has_permission('Users', 'edit')): ?>
            <th><?php echo __('Edit'); ?></th>            
            <?php endif; ?>
            <?php if (has_permission('Users', 'delete')): ?>
            <th><?php echo __('Delete'); ?></th>          
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
    <?php foreach( $users as $key => $user ): ?>
        <tr class="<?php if (current_user()->id == $user->id) echo 'current-user '; ?><?php if($key%2==1) echo 'even'; else echo 'odd'; ?>">
            <td><?php echo html_escape($user->username);?></td>
            <td><?php echo html_escape($user->first_name); ?> <?php echo html_escape($user->last_name); ?></td>
            <td><?php echo html_escape($user->email); ?></td>
            <td><span class="<?php echo html_escape($user->role); ?>"><?php echo html_escape(__(Inflector::humanize($user->role))); ?></span></td>
            <?php if (has_permission($user, 'edit')): ?>
            <td><?php echo link_to($user, 'edit', __('Edit'), array('class'=>'edit')); ?></td>
            <?php endif; ?>     
            <?php if (has_permission($user, 'delete')): ?>
            <td><?php echo delete_button($user); ?></td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
</tbody>
</table>
<div class="pagination"><?php echo pagination_links(); ?></div>
</div>
<?php foot();?>
