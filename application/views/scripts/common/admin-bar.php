<?php if ($user = current_user()): ?>
<ul id="admin-bar">
<?php
    $links = array(
      __('Welcome, %s', $user->name) => uri('users/edit/'.$user->id),
      __('Omeka Admin') => admin_url(),
      __('Log Out') => uri('users/logout')
    );

    echo nav(apply_filters('admin_bar_nav', $links));
?>
</ul>
<?php endif; ?>
