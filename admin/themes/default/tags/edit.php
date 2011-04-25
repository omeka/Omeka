<?php head(array('title'=>'Edit Tag', 'content_class' => 'horizontal-nav','bodyclass'=>'tags')); ?>

<h1>Edit Tag</h1>
<?php common('tags-nav'); ?>
<div id="primary">
<?php echo flash(); ?>
<?php if (count($tags)): ?>    
<form method="post" action="">
    <div class="field">
    <?php echo label('old_tag','Choose a tag to rename'); ?>
    <div class="inputs">
        <select name="old_tag" id="old_tag">
        <?php foreach($tags as $key => $tag): ?>
            <option value="<?php echo $tag['id']; ?>"><?php echo html_escape($tag['name']); ?> (<?php echo $tag['tagCount']; ?>)</option>
        <?php endforeach; ?>
        </select>
    </div>
</div>
<div class="field">
    <label for="new_tag">Enter a new tag name:</label>
    <div class="inputs">
        <input type="text" class="textinput" size="40" name="new_tag" id="new_tag" />
    </div>
</div>
<div>
    <input type="submit" name="submit" class="submit" value="Save Changes" />
</div>
</form>
<?php else: ?>
    <p>There are no tags to edit.  You must first tag some items.</p>
<?php endif; ?>
</div>
<?php foot(); ?>
