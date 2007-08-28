<h2>Exhibit Summary</h2>

<p class="exhibit-description"><?php echo $exhibit->description; ?></p>

<h3>Sections</h3>
<div id="exhibit-sections">	
	<dl>
	<?php foreach($exhibit->Sections as $section): {echo "<dt>".$section->title."</dt><dd>".$section->description."</dd>";} endforeach; ?>
	</dl>
</div>

<h3>Credits</h3>
	<ul><li><?php echo $exhibit->credits; ?></li></ul>

<?php 	
// <a href=". uri('exhibits/'.$section->title)."\">" 
?>