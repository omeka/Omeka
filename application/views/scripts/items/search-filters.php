
<?php if (!empty($displayArray) || !empty($advancedArray)): ?>
<div id="item-filters">
    <ul>
    <?php foreach($displayArray as $name => $query): ?>
        <?php $class = html_escape(strtolower(str_replace(' ', '-', $name))); ?>
        <li class="<?php echo $class; ?>"><?php echo html_escape(__($name)) . ': ' . html_escape($query); ?></li>
    <?php endforeach; ?>
    <?php if(!empty($advancedArray)): ?>
        <?php foreach($advancedArray as $j => $advanced): ?>
        <li class="advanced"><?php echo html_escape($advanced); ?></li>
        <?php endforeach; ?>
    <?php endif; ?>
    </ul>
</div>
<?php endif; ?>
