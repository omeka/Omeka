<h2>Edit existing elements:</h2>
<ul>
<?php foreach ($elements as $key => $element): ?>
    <li class="element <?php echo is_odd($key) ? 'odd' : 'even'; ?>">
        <div class="element-name"><?php echo htmlentities($element->name); ?>
            <a href="<?php echo url_for(array('controller'=>'item-types', 'action'=>'delete-element', 'element-id'=>$element->id, 'item-type-id'=>$itemtype->id), 'default'); ?>" class="delete-element">[X]</a>
        </div>
        <div class="element-order"><?php echo __v()->formText("elements[$key][order]", $key+1, array('size'=>2)); // Key starts at 0 ?></div>
        
        <?php echo __v()->formHidden("elements[$key][element_id]", $element->id); ?>
    </li>
<?php endforeach; ?>
</ul>