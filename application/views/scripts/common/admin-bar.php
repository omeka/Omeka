<nav id="admin-bar" aria-label="<?php echo __('Omeka'); ?>">

<?php if ($user = current_user()) {
    $links = [
        [
            'label' => __('Welcome, %s', $user->name),
            'uri' => admin_url('/users/edit/'.$user->id)
        ],
        [
            'label' => __('Omeka Admin'),
            'uri' => admin_url('/')
        ],
        [
            'label' => __('Log Out'),
            'uri' => url('/users/logout')
        ]
    ];
} else {
    $links = [];
}

echo nav($links, 'public_navigation_admin_bar');
?>
</nav>
