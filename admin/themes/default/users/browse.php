<?php head(array('title'=>'Browse Users', 'content_class' => 'vertical-nav', 'bodyclass'=>'users primary'));?>
<h1>Browse Users (<?php echo $total_records; ?> total)</h1>
<?php if (has_permission('Users', 'add')): ?>
    <p id="add-user" class="add-button"><?php echo link_to('users', 'add', 'Add a User', array('class'=>'add-user')); ?></p>    
<?php endif; ?>
<?php common('settings-nav'); ?>
<div id="primary">
<?php echo flash(); ?>
<form action="<?php echo html_escape(current_uri()); ?>" id="sort-users-form" method="get" accept-charset="utf-8">
    <fieldset>
        <p>Search Users:</p>
        <?php echo $this->formSelect('role', @$_GET['role'], array(), 
            array(''=>'Select Role') + get_user_roles()); ?>
        <?php echo $this->formSelect('active', @$_GET['active'], array(),
            array(''=>'Select Status',  '1'=>'Active', '0'=>'Inactive')); ?>
        <?php echo $this->formSelect('sort', @$_GET['sort'], array(),
            array(  ''=>'Sort By', 
                    'first_name'=>'First Name',
                    'last_name'=>'Last Name',
                    'institution'=>'Institution Name',
                    'role'=>'Role',
                    'username'=>'Username')); ?>
        <?php echo $this->formSelect('sortOrder', @$_GET['sortOrder'], array(),
            array( ''=>'Sort Order',
                   'asc'=>'Ascending',
                   'desc'=>'Descending')); ?>
                   <input type="submit" class="submit-form" name="submit" value="Search" />
                   
    </fieldset>
</form>

<div class="pagination"><?php echo pagination_links(); ?></div>
<table id="users">
    <thead>
        <tr>
            <th>Username</th>
            <th>Real Name</th>
            <th>Email</th>
            <th>Role</th>
            <?php if (has_permission('Users', 'edit')): ?>
            <th>Edit</th>            
            <?php endif; ?>
            <?php if (has_permission('Users', 'delete')): ?>
            <th>Delete</th>          
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
    <?php foreach( $users as $key => $user ): ?>
        <tr class="<?php if (current_user()->id == $user->id) echo 'current-user '; ?><?php if($key%2==1) echo 'even'; else echo 'odd'; ?>">
            <td><?php echo html_escape($user->username);?></td>
            <td><?php echo html_escape($user->first_name); ?> <?php echo html_escape($user->last_name); ?></td>
            <td><?php echo html_escape($user->email); ?></td>
            <td><span class="<?php echo html_escape($user->role); ?>"><?php echo html_escape($user->role); ?></span></td>
            <?php if (has_permission($user, 'edit')): ?>
            <td><?php echo link_to($user, 'edit', 'Edit', array('class'=>'edit')); ?></td>
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
