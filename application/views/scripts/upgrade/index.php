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
<h2>Upgrade Omeka</h2>
<?php if (OMEKA_MIGRATION > (int) get_option('migration')): ?>
<p>Warning: Your Omeka database is not compatible with the 
version of Omeka that you are running.</p>
<?php if (has_permission('Upgrade', 'migrate')): ?>
<p>Please backup your existing database and then click the
following button to upgrade:</p>
<form action="<?php echo uri('upgrade/migrate'); ?>" method="post">
<fieldset>
<input type="submit" id="submitUpgrade" value="upgrade omeka database" />
</fieldset>
</form>
<?php else: ?>
Please notify an administrator to upgrade the database.
<?php endif; ?>
<?php else: ?>
<p>Your Omeka database is up-to-date! <a href="<?php echo uri(''); ?>">Return to Dashboard</a></p>
<?php endif; ?>
<?php foot();?>
