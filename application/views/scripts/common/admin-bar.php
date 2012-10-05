<?php if ($user = current_user()): ?>
<ul id="admin-bar">
<?php
$links = array(
    __('Welcome, %s', $user->name) => admin_url('/users/edit/'.$user->id),
    __('Omeka Admin') => admin_url('/'),
    __('Log Out') => url('/users/logout')
);
echo nav(apply_filters('admin_bar_nav', $links));
?>
</ul>
<?php endif; ?>
