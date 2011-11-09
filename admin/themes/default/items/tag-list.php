<?php if ($item->Tags): ?>
    <h3><?php echo __('All Tags'); ?></h3>
    <ul class="tags">
            <?php foreach( $item->Tags as $key => $tag ): ?>
            <li class="tag">
                <a href="<?php echo html_escape(uri('items/browse/tag/'.urlencode($tag->name)));?>" rel="<?php echo html_escape($tag->id); ?>"><?php echo html_escape($tag); ?></a>
            </li>
            <?php endforeach; ?>
    </ul>
<?php endif; ?>