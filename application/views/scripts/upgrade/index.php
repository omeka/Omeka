<?php head(); ?>
<div id="primary">
<h2 class="instruction">Upgrade Your Omeka Database</h2> 
<p>Your Omeka database is not compatible with your current
version of Omeka.  

Please backup your existing database and then click the button to upgrade:</p>
<?php echo link_to('upgrade', 'migrate', 'Upgrade', array('id' => 'upgrade-database-link', 'class'=>'button')); ?>                        
</div>
<?php foot(); ?>