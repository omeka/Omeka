<?php head(); ?>
<h2>Summary Page</h2>

<dl>
	<dt>Title</dt>
	<dd><?php echo $exhibit->title; ?></dd>
</dl>

<dl>
	<dt>Description</dt>
	<dd><?php echo $exhibit->description; ?></dd>
</dl>

<dl>
	<dt>Credits</dt>
	<dd><?php echo $exhibit->credits; ?></dd>
</dl>

<?php section_nav(); ?>

<?php foot(); ?>