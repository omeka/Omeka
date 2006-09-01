<fieldset>
	<label for="collection_name">Name:</label><br/>
<?php
	$_form->text( array(	'id'	=> 'collection_name',
	 						'name'	=> 'collection[collection_name]',
	 						'value'	=> $collection->collection_name,
	 						'size' => '23' ) );
?>
</fieldset>

<fieldset>
	<label for="collection_description">Description:</label><br/>
	<?php
		$_form->textarea( array(	'name' => 'collection[collection_description]',
		 							'rows' => '10',
									'cols' => '40' ), $collection->collection_description );
	?>
</fieldset>

<fieldset>
	<label for="collection_collector">Collector:</label><br/>
	<?php
		$_form->textarea( array(	'name' => 'collection[collection_collector]',
		 							'rows' => '4',
									'cols' => '30' ), $collection->collection_collector );
	?>
	<p class="field-caption">Type in the name of the Collector(s). If there is more than one Collector, separate individuals by a comma.  Background information on each Collector should be entered in the "Contributors" table.</p>
</fieldset>

<?php if (1==0): // begin masking ?>
<fieldset>
	<label for="collection_active">Active:</label><br/>
	The collection is active: <input type="checkbox" name="collection[collection_active]" <?php if( $collection->collection_active != 0 ) echo 'checked'; ?>></input>
</fieldset>
<?php endif; // end masking?>
