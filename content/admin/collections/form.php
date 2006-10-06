
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

