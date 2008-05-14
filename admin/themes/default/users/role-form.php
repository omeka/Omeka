<?php if($hasPermission): ?>
    <img src="<?php echo img('tick.gif'); ?>" width="16" height="16" alt="yes" /><?php echo Inflector::titleize($privilege); ?>
<?php else: ?>
    <img src="<?php echo img('error.gif'); ?>" width="16" height="16" alt="no" /><?php echo Inflector::titleize($privilege); ?>
<?php endif; ?>
    <form action="<?php echo uri('users/toggle-privilege') ?>" method="post" accept-charset="utf-8">
        <input type="hidden" name="role" value="<?php echo $role; ?>" />
        <input type="hidden" name="resource" value="<?php echo $resource; ?>" />
        <input type="hidden" name="privilege" value="<?php echo $privilege; ?>" />
    </form>