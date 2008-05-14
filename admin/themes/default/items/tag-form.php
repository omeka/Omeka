<div class="field">
<label for="tags-field">Your Tags</label>
<input type="text" name="tags" id="tags-field" class="textinput" value="<?php echo not_empty_or($_POST['tags'], tag_string(current_user_tags($item))); ?>" />
</div>

<?php fire_plugin_hook('append_to_item_form_tags', $item); ?>

<?php if(has_tags($item) and has_permission('Items','untagOthers')): ?>
<div class="field">
	<label for="all-tags">Remove Other Users' Tags</label>
	<ul id="tags-list">
		<?php foreach( $item->Tags as $key => $tag ): ?>
        	<li>
        		<input type="image" src="<?php echo img('delete.gif'); ?>" name="remove_tag" value="<?php echo h($tag->id); ?>" />
        		<?php echo h($tag->name); ?>
        	</li>
        <?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>