<?php if ($item->Tags): ?>
    <ul class="tags">
        <?php foreach( $item->Tags as $key => $tag ): ?>
        <li class="tag">
            <a href="<?php echo html_escape(url('items/browse/tag/' . urlencode($tag->name)));?>" rel="<?php echo html_escape($tag->id); ?>"><?php echo html_escape($tag); ?></a>
        </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
