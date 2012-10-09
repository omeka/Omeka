<?php
$pageTitle = __('Omeka Advanced Search');
echo head(array('title' => $pageTitle));
?>
<div id="primary">
<form action="<?php echo url('search') ?>" method="get">
    <?php echo $this->formText('query', null, array('size' => 60)); ?>
    <?php echo $this->formCheckbox('boolean', null, array('disableHidden' => true)); ?> boolean<br />
    Search only these record types:<br />   
    <?php foreach ($this->searchRecordTypes as $searchRecordType): ?>
    <input type="checkbox" name="record_types[]" value="<?php echo $searchRecordType; ?>" checked="checked" /> <?php echo $searchRecordType; ?><br />
    <?php endforeach; ?><br />
    <?php echo $this->formSubmit(null, __('Search Omeka')); ?>
</form>
</div>
<?php echo foot(); ?>