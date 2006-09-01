<?php
if( self::$_request->getProperty( 'contribute_submit' ) )
{
	$saved = self::$_request->getProperties();
	//foreach($saved  as  $k=>$v) if (is_string($v)) $saved[$k] = stripslashes($v);
}
else
{
	$saved = false;
}
?>

<?php $_form->displayError( 'Object', 'empty_object_title', $__c->public()->validationErrors() ); ?>
<label for="object_title">Title <span class="required">(Required)</span></label>
<input type="text" class="textinput" size="20" name="Object[object_title]" value="<?php echo isset( $saved['Object']['object_title'] ) ? $saved['Object']['object_title'] : null; ?>"></input>

<?php $_form->displayError( 'Object', 'empty_object_story_text', $__c->public()->validationErrors() ); ?>
<label for="object_text">Type or copy and paste your text <span class="required">(Required)</span></label>
<textarea name="online_story_text" id="object_text" cols="30" rows="20"><?php echo $saved['online_story_text']; ?></textarea>

<input type="hidden" name="story_form" value="true" />
