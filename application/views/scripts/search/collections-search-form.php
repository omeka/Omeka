<?php if (!$formActionUri): ?>
    <?php $formActionUri = uri(array('controller'=>'search', 'action'=>'results')); ?>
<?php endif; ?>
	
<form <?php echo _tag_attributes($formAttributes); ?> action="<?php echo $formActionUri; ?>" method="get">
	<div id="search-keywords" class="field">    
		<?php echo label('search','Search for Keywords'); ?>
		<div class="inputs">
		<?php echo text(array('name'=>'search','size' => '40','id'=>'search','class'=>'textinput'),$_REQUEST['search']); ?>
		</div>
	</div>
	<div id="search-narrow-by-fields" class="field">	
	    <div id="search-selects">
	    
	        <div class="field">
                <?php echo label(array('for'=>'collectors'), 'Add a Collector (optional)'); ?>
                <div class="inputs">
            	    <?php echo select_entity(array('name'=>'collectors[]', 'id'=>'collector')); ?>
                </div>
            </div>
	
    	    <?php if (has_permission('Collections','showNotPublic')): ?>
    	    <div class="field">
    		    <?php echo label('public','Only Public Collections'); ?>
    		    <div class="inputs">
    	            <?php echo checkbox(array('name'=>'public', 'id'=>'public'), $_REQUEST['public'], null); ?>
    	        </div>
    	    </div>
	
    	    <div class="field">
    		    <?php echo label('featured','Only Featured Collections'); ?>
    		    <div class="inputs">
    	            <?php echo checkbox(array('name'=>'featured', 'id'=>'featured'), $_REQUEST['featured'], null); ?>
    	        </div>
    	    </div>
    	    <?php endif; ?>
	    </div>
	</div>
	<?php echo hidden(array('name'=>'model', 'id'=>'model'), 'Collection'); ?>
	<?php is_admin_theme() ? fire_plugin_hook('admin_append_to_advanced_search') : fire_plugin_hook('public_append_to_advanced_search'); ?>
	<input type="submit" class="submit submit-medium" name="submit_search" id="submit_search" value="Search" />
</form>