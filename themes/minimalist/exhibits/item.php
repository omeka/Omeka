<?php head(); ?>
<div id="primary">
<h2><?php echo h($exhibit->title); ?></h2>
<h3>Description</h3>
<?php echo nls2p($exhibit->description); ?>

<h3>Credits</h3>
<p><?php echo h($exhibit->credits); ?></p>


<?php section_nav(); ?>
</div>
<?php foot(); ?>