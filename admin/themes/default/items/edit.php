<?php
    $itemTitle = strip_formatting(item('Dublin Core', 'Title'));
    if ($itemTitle != '' && $itemTitle != __('[Untitled]')) {
        $itemTitle = ': &quot;' . $itemTitle . '&quot; ';
    } else {
        $itemTitle = '';
    }
    $itemTitle = __('Edit Item #%s', item('id')) . $itemTitle;
?>
<?php head(array('title'=> $itemTitle, 'bodyclass'=>'items primary','content_class' => 'vertical-nav'));?>
<h1 class="section-title"><?php echo $itemTitle; ?></h1>
	<section id="content" class="container">
	
		<div class="two columns">
			&nbsp;
		</div>
		
		<div class="ten columns">

<?php include 'form-tabs.php'; // Definitions for all the tabs for the form. ?>

    <form method="post" enctype="multipart/form-data" id="item-form" action="">
        <?php include 'form.php'; ?>
        
        <div id="save" class="three columns omega">
        
        	<div class="panel">
        	
        	<?php echo submit(array('name'=>'submit', 'id'=>'save-changes', 'class'=>'big green button'), __('Save Changes')); ?>
        	<a href="<?php echo html_escape(public_uri('items/show/'.item('id'))); ?>" class="big blue button" target="_blank">View Public Page</a>
        	<?php echo link_to_item(__('Delete Item'), array('class' => 'big red button'), 'delete-confirm'); ?>
        	
        	</div>
        
        </div>
        
    </form>

</div>

<?php foot();?>
