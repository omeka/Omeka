<?php
$db = new mysqli( 'localhost', 'root', '', 'sitebuilder2' );

if( mysqli_connect_errno() ) {
	echo mysqli_connect_error();
	exit();
}

$sql = "SELECT *
		FROM objects
		LEFT JOIN objectTypes USING( objectTypeID )
		WHERE objects.objectID = 1";
		
$sql2 = "SELECT metaField_name, metaField_type, metaText_text
		 FROM objectTypes_metaFields
		 LEFT JOIN metaFields USING ( metaField_id )
		 LEFT JOIN metaText ON metaText.metaText_metaField_id = metaFields.metaField_id
		 WHERE objectTypes_metaFields.objectType_id = 1
		 AND metaText.metaText_object_id = 1";

if( !$res = $db->query( $sql ) ) {
	print_r( $db->error );
	exit();
}

if( !$res2 = $db->query( $sql2 ) ) {
	print_r( $db->error );
	exit();
}

while( $array = $res->fetch_assoc() ) {
print_r($array);
}

while( $array2 = $res2->fetch_assoc() ) {
print_r($array2);
}

?>