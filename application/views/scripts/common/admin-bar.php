<?php if ($user = current_user()): ?>
<nav id="admin-bar">
<?php
$links = array(
    array(
        'label' => __('Welcome, %s', $user->name),
        'uri' => admin_url('/users/edit/'.$user->id),
        'theme' => 'admin'
    ),
    array(
        'label' => __('Omeka Admin'),
        'uri' => admin_url('/'),
        'theme' => 'admin'
    ),
    array(
        'label' => __('Log Out'),
        'uri' => url('/users/logout'),
        'theme' => '' // use the current base url
    )
);
echo nav($links, 'public_navigation_admin_bar');
?>
</nav>
<?php endif; ?>
