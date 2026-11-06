<h2 id="element-set-title"><?php echo html_escape(@get_current_record('item')->Type->name); ?></h2>
<p class="element-set-description" tabindex="-1">
    <?php echo html_escape(@get_current_record('item')->Type->description); ?>
</p>
<?php
echo element_set_form(get_current_record('item'), 'Item Type Metadata');
?>
