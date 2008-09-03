<?php head(array('title'=>'Edit Item', 'body_class'=>'items narrow'));?>
<h1>Edit &#8220;<?php echo item('Title', ', '); ?>&#8221;</h1>

<!-- Create the sections for the various element sets -->

<ul id="section-nav" class="vertical navigation">
    
    <?php foreach ($elementSets as $key => $elementSet): ?>
    <li><a href="#<?php echo text_to_id($elementSet->name); ?>-metadata"><?php echo $elementSet->name; ?></a></li>
    
    <?php endforeach; ?>
    
    <?php 
    $otherTabs = array('Collection', 'Files', 'Tags', 'Miscellaneous'); ?>
    <?php foreach ($otherTabs as $tabName): ?>
    <li<?php if($tabName == 'Collection') echo ' class="current"'; ?>><a href="#<?php echo text_to_id($tabName);?>-metadata"><?php echo $tabName; ?></a></li>
    <?php endforeach; ?>
    
</ul>
<div id="primary">
	

	<form method="post" enctype="multipart/form-data" id="item-form" action="">
		<?php include 'form.php'; ?>
		<p id="item-form-submits">
		    <button type="submit" name="submit">Save Changes</button> or 
		    <?php echo link_to_item('show', 'Cancel', 
		        array('id'=>'cancel_changes', 'class'=>'cancel')); ?>
		</p>
		<p id="delete_item_link">
		    <?php echo link_to_item('delete', 'Delete This Item', 
		        array('class'=>'delete')); ?>
		</p>
	</form>



</div>
<?php foot();?>
