<?php
$pageTitle = __('Search Settings');
echo head(array('title' => $pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary'));
?>
<div class="seven columns alpha">
<?php echo common('settings-nav'); ?>
<?php echo flash(); ?>
<h2>Indexing</h2>
<p>Indexing means to collect, parse, and store data to facilitate fast and 
accurate searches. Currently the following records are indexed for search:</p>
<ul>
    <?php foreach ($this->searchRecordTypes as $searchRecordType): ?>
    <li><?php echo $searchRecordType; ?></li>
    <?php endforeach; ?>
</ul>
<p>Omeka will automatically index individual records as they are saved, but 
there are circumstances when your records are not indexed; for instance, when 
updating from an earlier version of Omeka or when a plugin modifies how an 
existing record is indexed.</p>
<p>Click on the following button to re-index your records.</p>
<form method="post">
    <input type="submit" name="index_search_text" value="Index Records"/>
</form>
</div>
<?php echo foot(); ?>
