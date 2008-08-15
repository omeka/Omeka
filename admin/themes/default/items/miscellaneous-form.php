<?php if ( has_permission('Items', 'makePublic') ): ?>
	<div class="field">
		<div class="label">Item is public:</div> 
		<div class="radio"><?php echo radio(array('name'=>'public', 'id'=>'public'), array('0'=>'No','1'=>'Yes'), $item->public); ?></div>
	</div>
<?php endif; ?>
<?php if ( has_permission('Items', 'makeFeatured') ): ?>
	<div class="field">
		<div class="label">Item is featured:</div> 
		<div class="radio"><?php echo radio(array('name'=>'featured', 'id'=>'featured'), array('0'=>'No','1'=>'Yes'), $item->featured); ?></div>
	</div>
<?php endif; ?>

<fieldset id="additional-plugin-data">
	<?php fire_plugin_hook('append_to_item_form', $item); ?>
</fieldset>