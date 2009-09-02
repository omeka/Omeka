<?php head(array('title'=>'Users: '.$user->username, 'content_class' => 'vertical-nav', 'bodyclass'=>'themes primary'));?>
<h1>User: <?php echo html_escape($user->first_name); ?> <?php echo html_escape($user->last_name); ?> <a class="edit" href="<?php echo uri('users/edit/'.$user->id); ?>">(Edit)</a></h1>

<?php common('settings-nav'); ?>

<div id="primary">

    <h2>Username</h2>
    <p><?php echo html_escape($user->username); ?></p>
    <h2>Real Name</h2>
    <p><?php echo html_escape($user->first_name . ' ' . $user->last_name); ?></p>
    <h2>Email</h2>
    <p><?php echo html_escape($user->email); ?></p>
    <h2>Institution</h2>
    <p><?php echo html_escape($user->institution); ?></p>
</div>
<?php foot();?>