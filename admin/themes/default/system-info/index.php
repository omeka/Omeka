<?php
$title = __('System Information');
head(array('title' => $title, 'bodyclass' => 'system-info')); ?>

<h1><?php echo $title; ?></h1>

<div id="primary">
<?php echo flash(); ?>

<pre id="info-field">
<?php
foreach ($info as $category => $entries) {
    echo html_escape(__($category)) . "\n";

    foreach ($entries as $name => $value) {
        printf("    %-20s%s\n", html_escape($name) . ':', html_escape($value));
    }

    echo "\n";
}
?>
</pre>
</div>

<?php foot();
