<?php foreach( $exhibit->Sections as $key => $section ): ?>
	<li id="section_<?php echo $section->order; ?>">
		<span class="left">
			<span class="handle"><img src="<?php echo img('arrow_move.gif'); ?>" alt="Move" /></span>
		<span class="input"><?php text(array('name'=>"Sections[$key][order]",'size'=>2,'class'=>'order-input'), $section->order); ?></span>

		<span class="section-title"><?php echo h($section->title); ?></span>
		</span>
		<span class="right">
		<span class="section-edit"><a href="<?php echo uri('exhibits/edit-section/'.$section->id); ?>" class="edit">Edit</a></span>
		<span class="section-delete"><a href="<?php echo uri('exhibits/delete-section/'.$section->id); ?>" class="delete">Delete</a></span>
		</span>
	</li>
<?php endforeach; ?>