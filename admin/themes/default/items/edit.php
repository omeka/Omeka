<?php head(array('title'=>'Edit Item', 'body_class'=>'items primary','content_class' => 'vertical-nav'));?>
<h1>Edit &#8220;<?php echo item('Title'); ?>&#8221;</h1>
<script type="text/javascript">

Event.observe(window,'load',function(){
$$('.tabs').each(function(tab_group){  
     new Control.Tabs(tab_group);  
 });
});

</script>
<!-- Create the sections for the various element sets -->

<ul id="section-nav" class="navigation tabs">
    
    <?php foreach ($elementSets as $key => $elementSet): ?>
    <li><a href="#<?php echo text_to_id($elementSet->name); ?>-metadata"><?php echo $elementSet->name; ?></a></li>
    
    <?php endforeach; ?>
    
    <?php 
    $otherTabs = array('Collection', 'Files', 'Tags', 'Miscellaneous'); ?>
    <?php foreach ($otherTabs as $tabName): ?>
    <li><a href="#<?php echo text_to_id($tabName);?>-metadata"><?php echo $tabName; ?></a></li>
    <?php endforeach; ?>
    
</ul>
<div id="primary">
	

	<form method="post" enctype="multipart/form-data" id="item-form" action="">
		<?php include 'form.php'; ?>
		    <input type="submit" name="submit" id="save-changes" class="submit submit-medium" value="Save Changes" />
		<p id="delete_item_link">
		    <?php echo link_to_item('Delete This Item', 
		        array('class'=>'delete'), 'delete'); ?>
		</p>
	</form>



</div>

<?php foot();?>
