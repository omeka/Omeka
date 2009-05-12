<?php head(array('title'=>'Omeka Upgrade')); ?>
<?php /*
// Attempting to disable submit button on upgrade, using Prototype. 
// Unfortunately the js() function does not work correctly in the application/
// views/scripts directory.
<?php echo js('prototype'); ?>
<script type="text/javascript" charset="utf-8">
    Event.observe(window, 'load', function() {
        $('submitUpgrade').observe('click', function() {
            alert(this);
        });
    });
</script>
*/ ?>
<div id="primary">
<h1>Upgrade Omeka</h1>
<?php if (OMEKA_MIGRATION > (int) get_option('migration')): ?>
<?php if (has_permission('Upgrade', 'migrate')): ?>
<p>Warning: Your Omeka database is not compatible with the version of Omeka that you are running. Please backup your Omeka database, then upgrade:</p>
<form action="<?php echo uri('upgrade/migrate'); ?>" method="post">
<fieldset>
<input type="submit" class="submit submit-medium" id="submitUpgrade" value="Upgrade Now" />
</fieldset>
</form>
<?php else: ?>
<p>Warning: Your Omeka database is not compatible with the version of Omeka that you are running. Please notify an administrator to upgrade the database.</p>
<?php endif; ?>
<?php else: ?>
<p>Your Omeka database is up-to-date! <a href="<?php echo uri('admin'); ?>">Return to Dashboard</a></p>
<?php endif; ?>
</div>
<?php foot();?>
