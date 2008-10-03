<?php ob_start(); // Only output the <fieldset> if the plugins actually use this hook.
    fire_plugin_hook('append_to_item_form', $item);
    $output = ob_get_contents();
    ob_end_clean(); 
    if (!empty($output)): ?>
    <fieldset id="additional-plugin-data">
        <?php echo $output; ?>
    </fieldset><?php endif; ?>