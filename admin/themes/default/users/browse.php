<?php
$pageTitle = __('Browse Users') . ' ' . __('(%s total)', $total_results);
echo head(array('title'=>$pageTitle, 'bodyclass'=>'users'));
echo flash();
?>

<?php if(isset($_GET['search'])):?>
<div id='search-filters'>
    <ul>
        <li>
        <?php switch($_GET['search-type']) {
                        case "name":
                            echo __("Name") . ': ';
                        break;
                        case "username":
                            echo __("Username") . ': ';
                        break;
                        case "email":
                            echo __("Email") . ': ';
                        break;
                    }
        ?>
        <?php echo html_escape($_GET['search']); ?>
        </li>
    </ul>
</div>
<?php endif; ?>


<?php if (is_allowed('Users', 'add')): ?>
        <?php echo link_to('users', 'add', __('Add a User'), array('class'=>'green button')); ?>
    <?php endif; ?>

<form id='search-users' method='GET'>
    <input type='text' name='search' aria-labelledby="search-users-button" />
    <fieldset>
        <legend class="sr-only"><?php echo __('Options'); ?></legend>
        <label><input type='radio' name='search-type' value='username' checked='checked' /><?php echo __('Username'); ?></label>
        <label><input type='radio' name='search-type' value='name' /><?php echo __('Display Name'); ?></label>
        <label><input type='radio' name='search-type' value='email' /><?php echo __('Email'); ?></label>
    </fieldset>
    <button id="search-users-button"><?php echo __('Search users'); ?></button>
</form>

<?php echo pagination_links(); ?>
<div class="table-responsive">
    <table id="users">
        <thead>
            <tr>
            <?php $sortLinks = array(
                    __('Username') => 'username',
                    __('Display Name') => 'name',
                    __('Email') => 'email',
                    __('Role') => 'role'
                    );
            ?>
            <?php echo browse_sort_links($sortLinks,  array('link_tag' => 'th scope="col"', 'list_tag' => '')); ?>
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
                    <li><?php echo link_to($user, 'delete-confirm', __('Delete'), array('class'=>'delete-confirm')); ?></li>
                    <?php endif; ?>
                </ul>
                <?php fire_plugin_hook('admin_users_browse_each', array('user' => $user, 'view' => $this)); ?>
               </td>
                <td><?php echo html_escape($user->name); ?></td>
                <td><?php echo html_escape($user->email); ?></td>
                <td><span class="<?php echo html_escape($user->role); ?>"><?php echo html_escape(__(Inflector::humanize($user->role))); ?></span></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php echo pagination_links(); ?>
<?php fire_plugin_hook('admin_users_browse', array('users' => $users, 'view' => $this)); ?>
<?php echo foot();?>
