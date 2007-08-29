<h1><?php link_to_exhibit($exhibit); ?></h1>
empty h2
<div class="exhibit-description">
	<?php echo nls2p($exhibit->description); ?>
</div>

<h3>Sections</h3>
<div id="exhibit-sections">	
	<dl>
	<?php foreach($exhibit->Sections as $section): ?>
		<dt><?php echo $section->title; ?></dt>
		<dd><?php echo $section->description; ?></dd>
	<?php endforeach; ?>
	</dl>
</div>

<h3>Credits</h3>
<p><?php echo h($exhibit->credits); ?></p>
