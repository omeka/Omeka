<div class="field">
<?php 
echo label('collection-id', 'Collection');
echo select_collection(array('name'=>'collection_id', 'id'=>'collection-id'),
	$item->collection_id); ?>
</div>