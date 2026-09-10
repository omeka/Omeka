<nav id="main-nav" class="two columns" role="navigation" aria-label="<?php echo __('Content'); ?>">
    <nav id="user-nav">
        <ul>
        <?php if ($user = current_user()): ?>
            <?php
                $name = html_escape($user->name);
                if (is_allowed($user, 'edit')) {
                    $userLink = '<a href="' . html_escape(url('users/edit/' . $user->id)) . '" id="current-user"><span class="icon"></span>' . $name . '</a>';
                } else {
                    $userLink = $name;
                }
            ?>
            <li><?php echo $userLink; ?></li>
            <li><a href="<?php echo html_escape(url('users/logout'));?>" id="logout"><span class="icon"></span><?php echo __('Log Out'); ?></a></li>
        <?php endif; ?>
        </ul>
    </nav>
    <div id="mobile-nav">
        <?php echo common('global-nav'); ?>
        <?php fire_plugin_hook('admin_header_bottom', ['view'=>$this]); ?>

        <button type="button" id="content-nav-toggle" class="mobile-menu collapse" data-target="#content-nav" aria-expanded="true"><?php echo __('Content'); ?></button>
        <nav id="content-nav" class="in">
            <?php
                $mainNav = [
                    [
                        'label' => __('Dashboard'),
                        'uri' => url('')
                    ],
                    [
                        'label' => __('Items'),
                        'uri' => url('items')
                    ],
                    [
                        'label' => __('Collections'),
                        'uri' => url('collections')
                    ],
                    [
                        'label' => __('Item Types'),
                        'uri' => url('item-types')
                    ],
                    [
                        'label' => __('Tags'),
                        'uri' => url('tags')
                    ]
                ];
                $nav = nav($mainNav, 'admin_navigation_main');
                echo $nav;
            ?>
        </nav>
    </div>
</nav>
