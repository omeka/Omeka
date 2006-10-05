<?php
// Layout: show;
$__c->items()->add();
$__c->types()->change();
$saved = self::$_session->getValue( 'item_form_saved' );

?>
<ul id="sub-navigation" class="navigation subnav">
	<li<?php if(self::$_route['template'] == 'index') {echo ' class="current"';} ?>><a href="<?php echo $_link->to('items'); ?>">Show Items</a></li>
	<li<?php if(self::$_route['template'] == 'add') {echo ' class="current"';} ?>><a href="<?php echo $_link->to('items', 'add'); ?>">Add Item</a></li>
</ul>

<h2>Add an Item</h2>

<form method="post" id="item-addedit" action="<?php echo $_link->to( 'items', 'add' ); ?>" enctype="multipart/form-data">

<?php include( 'form.php' ); ?>

<input type="submit" value="Insert Item &gt;&gt;" name="item_add" />

</form>