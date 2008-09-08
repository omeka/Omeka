<ul class="primary-nav navigation">
<?php
	$primaryNav = array(
	    'Items' => url_for('items'), 
	    'Collections' => url_for('collections'),
	    'Item Types' => url_for('item-types'),
	    'Tags' => url_for('tags')
	    );
if (has_permission('Settings', 'edit')) 
{
    $primaryNav['Settings'] = url_for('settings');
	
}
				
	echo nav(apply_filters('admin_navigation_main', $primaryNav));
?>
</ul>