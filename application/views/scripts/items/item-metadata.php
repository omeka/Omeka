<div class="element-set">
    <h2><?php echo $setName ?></h2>
    <?php foreach ($elementsInSet as $info): 
        $elementName = $info['elementName'];
        if ($info['isShowable']): ?>
    <div id="<?php echo text_to_id("$setName $elementName"); ?>" class="element">
        <h3><?php echo $elementName; ?></h3>
        <?php if ($info['isEmpty']): ?>
            <div class="element-text-empty"><?php echo $info['emptyText']; ?></div>
        <?php else: ?>
        <?php foreach (item($elementName, array('all'=>true)) as $text): ?>
            <div class="element-text"><?php echo $text; ?></div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div><!-- end element -->
    <?php endif; ?>
    <?php endforeach; ?>
</div><!-- end element-set -->