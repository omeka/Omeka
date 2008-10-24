<div id="tag-form">
<?php if(has_tags($item) and has_permission('Items','untagOthers')): ?>
		<div id="edit-all-tags">
    	<ul id="tags-list">
	
    		<?php foreach( $item->Tags as $key => $tag ): ?>
	
            	<li class="tag-delete">
            		<input type="image" src="<?php echo img('delete.gif'); ?>" name="remove_tag" value="<?php echo h($tag->id); ?>" />
            		<?php echo htmlentities($tag->name); ?>
            	</li>

            <?php endforeach; ?>

    	</ul>
</div>
<?php endif; ?>

<div id="edit-your-tags">
	<h3>Your Tags</h3>
		<input type="text" name="tags" size="30" id="tags-field" class="textinput" value="<?php echo not_empty_or($_POST['tags'], tag_string(current_user_tags($item))); ?>" />
		
	<p class="explanation">Separate tags with commas (lorem, ipsum, dolor sit, amet).</p>
	
</div>

<?php fire_plugin_hook('append_to_item_form_tags', $item); // Deprecated in 0.10 ?>
<?php fire_plugin_hook('admin_append_to_items_form_tags', $item); ?>
</div>
