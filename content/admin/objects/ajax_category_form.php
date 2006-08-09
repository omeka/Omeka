<?php
$cat = $__c->categories()->findById();
?>

<h2>Category: <?php echo $cat->category_name ?></h2>
<fieldset class="formElement">
	<label>Category Description:</label>
	<p><?php echo $cat->category_description; ?></p>
	
	<?php $i=0; foreach( $cat->metafields as $metafield ): ?>
		<fieldset class="formElement">
		<label><?php echo $metafield->metafield_name ?></label>
		<p><?php echo $metafield->metafield_description ?></p>
		<input type="hidden" name="metadata[<?php echo $i; ?>][metafield_id]" value="<?php echo $metafield->metafield_id; ?>" />
		<?php switch ($metafield->metafield_id) {
			case ('2'):
			case ('3'):
			case ('4'):
			case ('5'):
			case ('6'):
			case ('19'):
			case ('26'):
			case ('31'):
				echo '<textarea rows="10" cols="60" name="metadata['.$i.'][metatext_text]"></textarea>';
				break;
			case ('7'):
			case ('8'):
			case ('9'):
			case ('10'):
			case ('11'):
			case ('12'):
			case ('13'):
			case ('14'):
			case ('15'):
			case ('16'):
			case ('17'):
			case ('18'):
			case ('20'):
			case ('21'):
			case ('22'):
			case ('23'):
			case ('24'):
			case ('25'):
			case ('27'):
			case ('28'):
			case ('29'):
			case ('30'):
			case ('32'):
				echo '<input type="text" class="textinput" name="metadata['.$i.'][metatext_text]" />';
				break;
		}
		?>
		</fieldset>
	<?php $i++; endforeach; ?>
</fieldset>