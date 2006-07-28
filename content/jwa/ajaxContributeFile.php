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

<label for="object_title">Title (<em>One name which describes the general subject of your file(s)</em>)</label>
<?php
	$_form->text( array('size'	=> 18,
						'class' => 'textinput',
						'name'	=> 'Object[object_title]',
						'value'	=> $saved['Object']['object_title'] ) );
?>
<label for="object_files">Attach a file</label>
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
<!-- Name of input element determines name in $_FILES array -->
<ol id="files">
<li><label>Add a file</label><input name="objectfile[]" class="textinput" type="file" /></li>
</ol>
<?php
	if( isset( $saved['MAX_FILE_SIZE'] ) )
	{
		echo '<div class="form-error">Please re-enter the files you wish to contribute</div>';
	}
?>
<p><a href="javascript:void(0);" onclick="addFile()">Attach another file</a></p>