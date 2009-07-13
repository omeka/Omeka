<?php head(array('title'=>'Browse Users', 'content_class' => 'vertical-nav', 'bodyclass'=>'users primary'));?>
<h1>Users (<?php echo $total_records ?> total)</h1>
<?php common('settings-nav'); ?>
<div id="primary">
<h2>Current Users</h2>

<form action="<?php echo current_uri(); ?>" method="get" accept-charset="utf-8">
    <fieldset>
        <?php echo $this->formSelect('role', $_GET['role'], array(), 
            array(''=>'Select Role') + get_user_roles()); ?>
        <?php echo $this->formSelect('active', $_GET['active'], array(),
            array(''=>'Select Status',  '1'=>'Active', '0'=>'Inactive')); ?>
        <?php echo $this->formSelect('sort', $_GET['sort'], array(),
            array(  ''=>'Sort By', 
                    'first_name'=>'First Name',
                    'last_name'=>'Last Name',
                    'institution'=>'Institution Name',
                    'role'=>'Role',
                    'username'=>'Username')); ?>
        <?php echo $this->formSelect('sortOrder', $_GET['sortOrder'], array(),
            array( ''=>'Sort Order',
                   'asc'=>'Ascending',
                   'desc'=>'Descending')); ?>
        <input type="submit" class="submit submit-medium" name="submit" value="Submit" />
    </fieldset>
</form>

<div class="pagination"><?php echo pagination_links(); ?></div>


<table id="users">
	<thead>
		<tr>
			<th>Username</th>
			<th>Real Name</th>
			<th>Role</th>
			<th>Active?</th>
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
	<tr class="<?php if(current_user()->id == $user->id) echo 'current-user '; ?><?php if($key%2==1) echo 'even'; else echo 'odd'; ?>">
		<td><?php  echo html_escape($user->username); ?></td>
		<td><?php echo html_escape($user->first_name); ?> <?php echo html_escape($user->last_name); ?></td>
		<td><span class="<?php echo html_escape($user->role); ?>"><?php echo html_escape($user->role); ?></span></td>
		
		<td><?php if($user->active):?>Active<?php else: ?>Not active<?php endif;?></td>
		
		<?php if (has_permission('Users', 'edit')): ?>
		<td>
		  <a class="edit" href="<?php echo uri('users/edit/'.$user->id);?>">Edit</a>
		</td>
		<?php endif; ?>	    
		
		<?php if (has_permission('Users', 'delete')): ?>  
		<td><?php if((current_user()->id != $user->id)): ?>
		    <a class="delete" href="<?php echo uri('users/delete/'.$user->id);?>">Delete</a>
		    <?php endif; ?>
		</td>
		<?php endif; ?>
		
		
	</tr>
<?php endforeach; ?>
</tbody>
</table>

<div>
    
    <?php if (has_permission('Users', 'add')): ?>
        <h2>Add a User</h2>
    	<form id="new-user-form" action="<?php echo uri('users/add'); ?>" method="post" accept-charset="utf-8">
    		<?php common('form', array(), 'users'); ?>

    		<input type="submit" name="submit" class="submit submit-medium" value="Add User" />
    	</form>        
    <?php endif; ?>	
</div>

</div>
<?php foot();?>