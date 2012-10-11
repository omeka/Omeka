<?php
$pageTitle = __('Search Settings');
echo head(array('title' => $pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary'));
?>
<div class="seven columns alpha">
<?php echo common('settings-nav'); ?>
<?php echo flash(); ?>
<h2>Record Types</h2>
<p>Here you may customize which types of records will be searchable in Omeka.</p>
<form method="post">
<ul>
    <?php foreach ($this->searchRecordTypes as $key => $value): ?>
    <li><?php echo $this->formCheckbox('search_record_types[]', $key, 
                   array('checked' => array_key_exists($key, $this->customSearchRecordTypes))); ?> <?php echo $value; ?></li>
    <?php endforeach; ?>
</ul>
<input type="submit" name="customize_search_record_types" value="<?php echo __('Select Record Types'); ?>"/>
</form>
<h2>Indexing</h2>
<p>Indexing means to collect, parse, and store data to facilitate fast and 
accurate searches. Omeka will automatically index individual records as they are 
saved, but there are circumstances when your records are not indexed; for 
instance, when updating from an earlier version of Omeka or after you customize 
which records will be searchable.</p>
<p>Click on the following button to re-index your records.</p>
<form method="post">
    <input type="submit" name="index_records" value="<?php echo __('Index Records'); ?>"/>
</form>
</div>
<?php echo foot(); ?>
