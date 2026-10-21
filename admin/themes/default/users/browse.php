<?php
$isFiltered = isset($_GET['search']);
if ($isFiltered) {
    $pageTitle = __('Browse Users') . ' ' . __('(filtered results: %s total)', $total_results);
} else {
    $pageTitle = __('Browse Users') . ' ' . __('(%s total)', $total_results);
}
$userSearchTypes = [
    'name' => __('Name'),
    'username' => __('Username'),
    'email' => __('Email')
];
echo head(['title'=>$pageTitle, 'bodyclass'=>'users']);
echo flash();
?>

<?php if($isFiltered):?>
<?php $currentSearchType = $_GET['search-type']; ?>
<div id='search-filters'>
    <ul>
        <li>
        <?php echo $userSearchTypes[$currentSearchType]; ?>: 
        <?php echo html_escape($_GET['search']); ?>
        </li>
    </ul>
</div>
<?php endif; ?>


<?php if (is_allowed('Users', 'add')): ?>
        <?php echo link_to('users', 'add', __('Add a User'), ['class'=>'green button']); ?>
    <?php endif; ?>

<form id='search-users' method='GET' role="group" aria-labelledby="search-users-button">
    <label id="search-users-type"><?php echo __('Search by'); ?>
        <select name="search-type">
            <?php foreach ($userSearchTypes as $name => $label): ?>
            <?php $checked = (isset($currentSearchType) && ($currentSearchType == $name)) ? 'selected="true"' : ''; ?>
            <option value="<?php echo $name; ?>" <?php echo $checked; ?>><?php echo $label; ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <input type='text' name='search' aria-label="<?php echo __('Query'); ?>" aria-describedby="search-users-type" />
    <button id="search-users-button"><?php echo __('Search users'); ?></button>
</form>

<?php echo pagination_links(['attributes' => ['aria-label' => __('Pagination')]]); ?>
<div class="table-responsive">
    <table id="users" aria-label="<?php echo __('Users'); ?>">
        <thead>
            <tr>
            <?php $sortLinks = [
                    __('Username') => 'username',
                    __('Display Name') => 'name',
                    __('Email') => 'email',
                    __('Role') => 'role'
                    ];
            ?>
            <?php echo browse_sort_links($sortLinks,  ['link_tag' => 'th scope="col"', 'list_tag' => '']); ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach( $users as $key => $user ): ?>
            <tr class="<?php if (current_user()->id == $user->id) echo 'current-user '; ?><?php if($key%2==1) echo 'even'; else echo 'odd'; ?><?php if(!$user->active): ?> inactive<?php endif; ?>">
                <td>
                <?php echo html_escape($user->username); ?> <?php if(!$user->active): ?>(<?php echo __('inactive'); ?>)<?php endif; ?>
                <ul class="action-links group">
                    <?php if (is_allowed($user, 'edit')): ?>
                    <li><?php echo link_to($user, 'edit', __('Edit'), ['class'=>'edit']); ?></li>
                    <?php endif; ?>
                    <?php if (is_allowed($user, 'delete')): ?>
                    <li><?php echo link_to($user, 'delete-confirm', __('Delete'), ['class'=>'delete-confirm']); ?></li>
                    <?php endif; ?>
                </ul>
                <?php fire_plugin_hook('admin_users_browse_each', ['user' => $user, 'view' => $this]); ?>
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
<?php fire_plugin_hook('admin_users_browse', ['users' => $users, 'view' => $this]); ?>
<?php echo foot();?>
