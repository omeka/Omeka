
	<label for="collection_name">Name:</label>
<?php
	$_form->text( array(	'id'	=> 'collection_name',
	 						'name'	=> 'collection[collection_name]',
							'class' => 'textinput',
	 						'value'	=> $collection->collection_name,
	 						'size' => '23' ) );
?>

	<label for="collection_description">Description:</label>
	<?php
		$_form->textarea( array(	'name' => 'collection[collection_description]',
		 							'rows' => '10',
									'cols' => '40' ), $collection->collection_description );
	?>

	<label for="collection_collector">Collector:</label>
	<?php
		$_form->textarea( array(	'name' => 'collection[collection_collector]',
		 							'rows' => '4',
									'cols' => '30' ), $collection->collection_collector );
	?>
	<p class="field-caption">Type in the name of the Collector(s). If there is more than one Collector, separate individuals by a comma.  Background information on each Collector should be entered in the "Contributors" table.</p>
