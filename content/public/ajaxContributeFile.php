<?php
if( self::$_request->getProperty( 'contribute_submit' ) )
{
	$saved = self::$_request->getProperties();	
}
else
{
	$saved = false;
}
?>

<?php $_form->displayError( 'Object', 'empty_object_title', $__c->public()->validationErrors() ); ?>
<label for="object_title">Title <span class="required">(Required)</span></label>
<?php
	$_form->text( array('size'	=> 18,
						'class' => 'textinput',
						'name'	=> 'Object[object_title]',
						'value'	=> $saved['Object']['object_title'] ) );
?>

<label for="object_files">Attach a file <span class="required">(Required)</span></label>
<p>Note that you can only upload files that are smaller than 2<abbr title="Megabytes">MB</abbr>.  If your file is larger, or if you have several files that add up to over 2<abbr>MB</abbr>, please <a href="<?php echo $_link->to('contact'); ?>">contact us</a>.</p>
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
<!-- Name of input element determines name in $_FILES array -->
<ol id="files">
<li><input name="objectfile[]" type="file" /></li>
</ol>
<?php
	if( isset( $saved['MAX_FILE_SIZE'] ) )
	{
		if (empty($_FILES['objectfile']['name'][0])) $_form->displayError( 'Object', 'empty_object_file', $__c->public()->validationErrors() );
		else echo '<div class="form-error">Please re-enter the files you wish to contribute</div>';
	}
?>
<p><a href="javascript:void(0);" onclick="addFile()">Attach another file</a></p>