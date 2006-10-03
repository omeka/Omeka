<?php
$cat = $__c->types()->findById();
?>

<h2>Type: <?php echo $cat->type_name ?></h2>
<fieldset class="formElement">
	<label>Type Description:</label>
	<p><?php echo $cat->type_description; ?></p>
	
	<?php $i=0; foreach( $cat->metafields as $metafield ): ?>
		<fieldset class="formElement">
		<label><?php echo $metafield->metafield_name ?></label>
		<p><?php echo $metafield->metafield_description ?></p>
		<input type="hidden" name="metadata[<?php echo $i; ?>][metafield_id]" value="<?php echo $metafield->metafield_id; ?>" />
		<textarea rows="5" cols="60" name="metadata[<?php echo $i; ?>][metatext_text]"></textarea>
		</fieldset>
	<?php $i++; endforeach; ?>
</fieldset>