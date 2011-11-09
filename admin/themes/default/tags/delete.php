<?php
$pageTitle = __('Delete Tag');
head(array('title'=>$pageTitle, 'content_class' => 'horizontal-nav','bodyclass'=>'tags')); ?>
<h1><?php echo $pageTitle; ?></h1>
<?php common('tags-nav'); ?>
<div id="primary">
<?php echo flash(); ?>
<?php if (count($tags)): ?>
<form method="post" action="">
    <div class="field">
    <label for="delete_tag"><?php echo __('Select a tag to delete:'); ?></label>
    <div class="inputs">
        <select name="delete_tag" id="delete_tag">
            <?php foreach( $tags as $key => $tag ): ?>
                <option value="<?php echo $tag['id']; ?>"><?php echo html_escape($tag['name']); ?> (<?php echo $tag['tagCount']; ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<div>
    <input type="submit" name="submit" class="submit" value="<?php echo __('Delete Tag'); ?>" />
</div>
</form>
<?php else: ?>
    <p><?php echo __('There are no tags to delete. You must first tag some items.'); ?></p>
<?php endif; ?>
</div>
<?php foot(); ?>
