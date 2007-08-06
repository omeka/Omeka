<?php head(); ?>
<h2>Summary Page</h2>

<dl>
	<dt>Title</dt>
	<dd><?php echo h($exhibit->title); ?></dd>
</dl>

<dl>
	<dt>Description</dt>
	<dd><?php echo h($exhibit->description); ?></dd>
</dl>

<dl>
	<dt>Credits</dt>
	<dd><?php echo h($exhibit->credits); ?></dd>
</dl>

<?php section_nav(); ?>

<?php foot(); ?>