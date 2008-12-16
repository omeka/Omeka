<?php head(array('title'=>'Edit Item', 'bodyclass'=>'items primary','content_class' => 'vertical-nav'));?>
<h1>Edit &#8220;<?php echo strip_formatting(item('Dublin Core', 'Title')); ?>&#8221;</h1>
<script type="text/javascript">

Event.observe(window,'load',function(){
$$('.tabs').each(function(tab_group){  
     new Control.Tabs(tab_group);  
 });
});

</script>

<?php include 'form-tabs.php'; // Definitions for all the tabs for the form. ?>

<div id="primary">

	<form method="post" enctype="multipart/form-data" id="item-form" action="">
		<?php include 'form.php'; ?>
		    <?php echo submit(array('name'=>'submit', 'id'=>'save-changes', 'class'=>'submit submit-medium'), 'Save Changes'); ?>
		<p id="delete_item_link">
		    <?php echo link_to_item('Delete This Item', 
		        array('class'=>'delete'), 'delete'); ?>
		</p>
	</form>



</div>

<?php foot();?>
