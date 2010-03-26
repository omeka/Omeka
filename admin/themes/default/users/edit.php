<?php
    $userTitle = strip_formatting($user->username);
    if ($userTitle != '') {
        $userTitle = ': &quot;' . html_escape($userTitle) . '&quot; ';
    } else {
        $userTitle = '';
    }
    $userTitle = 'Edit User #' . $user->id . $userTitle;
?>
<?php head(array('title'=> $userTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'users primary'));?>
<h1><?php echo $userTitle; ?></h1>
<?php common('settings-nav'); ?>

<div id="primary">
<form method="post">
<?php include('form.php'); ?>
<input type="submit" name="submit" value="Save Changes" class="submit submit-medium" />
</form>

<?php echo $this->passwordForm; ?>
</div>
<?php foot();?>