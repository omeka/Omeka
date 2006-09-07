<?php
// Layout: default;
$categories = $__c->categories()->all( 'array' );
?>


			<dl class="typeslist">
			<?php foreach( $categories as $category ): ?>
			<dt><a href="<?php echo $_link->to( 'browse' ); ?>?type=<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></a> (<?php echo $__c->objects()->totalInCategory( $category['category_id'] ); ?>)</dt>
			<dd><?php echo $category['category_description']; ?></dd>
			<?php endforeach; ?>
			</dl>
