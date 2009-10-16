<?php head(array('title'=>'Delete Tag', 'content_class' => 'horizontal-nav','bodyclass'=>'tags')); ?>
<?php echo flash(); ?>

<h1>Delete Tag</h1>
<?php common('tags-nav'); ?>
<div id="primary">
<?php if (count($tags)): ?>
<form method="post">
    <div class="field">
    <label for="delete_tag">Select a tag to delete:</label>
<div class="inputs">
<select name="delete_tag">
    <?php foreach( $tags as $key => $tag ): ?>
        <option value="<?php echo $tag['id']; ?>"><?php echo html_escape($tag['name']); ?> (<?php echo $tag['tagCount']; ?>)</option>
    <?php endforeach; ?>
</select>
</div>
</div>
<input type="submit" name="submit" class="submit submit-medium" value="Delete Tag" />

</form>
<?php else: ?>
    <p>There are no tags to delete.  You must first tag some items.</p>
<?php endif; ?>
</div>
<?php foot(); ?>