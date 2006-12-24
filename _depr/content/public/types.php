<?php
// Layout: default;
$types = $__c->types()->all( 'array' );
?>


			<dl class="typeslist">
			<?php foreach( $types as $type ): ?>
			<dt><a href="<?php echo $_link->to( 'browse' ); ?>?type=<?php echo $type['type_id']; ?>"><?php echo $type['type_name']; ?></a> (<?php echo $__c->objects()->totalInType( $type['type_id'] ); ?>)</dt>
			<dd><?php echo $type['type_description']; ?></dd>
			<?php endforeach; ?>
			</dl>
