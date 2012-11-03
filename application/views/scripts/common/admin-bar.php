<?php if ($user = current_user()): ?>
<nav id="admin-bar">
<?php
$links = array(
    array(
        'label' => __('Welcome, %s', $user->name),
        'uri' => admin_url('/users/edit/'.$user->id)
    ),
    array(
        'label' => __('Omeka Admin'),
        'uri' => admin_url('/')
    ),
    array(
        'label' => __('Log Out'),
        'uri' => url('/users/logout')
    )
);
echo nav($links, 'public_navigation_admin_bar');
?>
</nav>
<?php endif; ?>
