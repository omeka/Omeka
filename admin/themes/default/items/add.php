<?php head(array('title'=>'Add Item','content_class' => 'vertical-nav', 'body_class'=>'items primary'));?>


<h1>Add an Item</h1>
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

		<form method="post" enctype="multipart/form-data" id="item-form">
			<?php include('form.php'); ?>
			<input type="submit" name="submit" id="add_item" value="Add Item" />
		</form>
</div>

<?php foot();?>
