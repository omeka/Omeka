<?php head(array('title'=>'Exhibit #'.$exhibit->id, 'body_class'=>'exhibits')); ?>
<div id="primary">

<h1>Exhibit Title: <?php echo h($exhibit->title); ?></h1>
<h2>Description</h2>
<?php echo nls2p(h($exhibit->description)); ?>

<h2>Credits</h2>
<p><?php echo h($exhibit->credits); ?></p>


<?php section_nav(); ?>
</div>
<?php foot(); ?>