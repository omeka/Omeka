<?php
$pageTitle = __('405 Method Not Allowed');
head(array('title'=>$pageTitle));
?>
<div id="primary">
    <h1><?php echo $pageTitle; ?></h1>
    <p><?php echo __('The method used to access this URL (%s) is not valid.', html_escape($this->method)); ?></p>
</div>
<?php foot(); ?>
