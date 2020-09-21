<?php if ($item->Tags): ?>
    <ul class="tags">
        <?php foreach( $item->Tags as $key => $tag ): ?>
        <li class="tag">
            <a href="<?php echo html_escape(url('items/browse', array('tags' => $tag->name))); ?>"><?php echo html_escape($tag->name); ?></a>
        </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
