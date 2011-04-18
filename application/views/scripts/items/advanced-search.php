<?php 
if (!$isPartial):
    head(array('title' => 'Advanced Search', 
               'bodyclass' => 'advanced-search', 
               'bodyid' => 'advanced-search-page'));
?>
<?php if(!is_admin_theme()): ?>
<div id="primary">
<?php endif; ?>
<h1>Advanced Search</h1>
<?php if(is_admin_theme()): ?>
<div id="primary">
<?php endif; ?>
     
<?php endif; ?>
<?php 
if ($formActionUri):
    $formAttributes['action'] = $formActionUri;
else:
    $formAttributes['action'] = uri(array('controller'=>'items', 
                                          'action'=>'browse'));
endif; 
$formAttributes['method'] = 'GET';    
?>
	
<form <?php echo _tag_attributes($formAttributes); ?>>
	<div id="search-keywords" class="field">    
		<?php echo label('keyword-search','Search for Keywords'); ?>
		<div class="inputs">
        <?php echo text(array(
                'name' => 'search',
                'size' => '40',
                'id' => 'keyword-search',
                'class' => 'textinput'), @$_REQUEST['search']); ?>
        </div>
    </div>
    <div id="search-narrow-by-fields" class="field">
        <div class="label">Narrow by Specific Fields</div>
        <div class="inputs">
        <?php 
        // If the form has been submitted, retain the number of search 
        // fields used and rebuild the form
        if(!empty($_GET['advanced'])) {
            $search = $_GET['advanced'];
        }else {
            $search = array(array('field'=>'','type'=>'','value'=>''));
        }

        //Here is where we actually build the search form
        foreach ($search as $i => $rows): ?>
            <div class="search-entry">		
                <?php 
                //The POST looks like => 
                // advanced[0] =>
                //[field] = 'description'
                //[type] = 'contains'
                //[terms] = 'foobar'
                //etc
                echo select_element(
                    array('name' => "advanced[$i][element_id]"), 
                    @$rows['element_id'],
                    null,
                    array('record_types' => array('Item', 'All'), 
                          'sort' => 'alphaBySet'));
                echo select(
                    array('name' => "advanced[$i][type]"),
                    array('contains' => 'contains', 
                          'does not contain' => 'does not contain',
                          'is exactly' => 'is exactly',
                          'is empty' => 'is empty', 
                          'is not empty' => 'is not empty'),
                    @$rows['type']
                );
                echo text(
                    array('name' => "advanced[$i][terms]", 
                          'size' => 20),
                    @$rows['terms']); ?>
                <button type="button" class="remove_search" disabled="disabled" style="display: none;">-</button>
            </div>
    <?php endforeach; ?>
        </div>
        <button type="button" class="add_search">Add a Field</button>
    </div>

        <div id="search-by-range" class="field">
            <label for="range">Search by a range of ID#s (example: 1-4, 156, 79)</label>
            <div class="inputs">
            <?php echo text(
                    array('name' => 'range', 
                          'size' => '40',
                          'class' => 'textinput'), 
                    @$_GET['range']); ?>
            </div>
        </div>

        <div id="search-selects">
            <div class="field">
                <?php echo label('collection-search', 'Search By Collection'); ?>
                <div class="inputs"><?php 
                    echo select_collection(array(
                        'name' => 'collection', 
                        'id' => 'collection-search'
                    ), @$_REQUEST['collection']); ?>
                </div>
            </div>
            <div class="field">
                <?php echo label('item-type-search', 'Search By Type'); ?>
                <div class="inputs"><?php 
                    echo select_item_type(array('name'=>'type', 'id'=>'item-type-search'), 
                        @$_REQUEST['type']); ?>
                </div>
            </div>
	
	<?php if(has_permission('Users', 'browse')): ?>
            <div class="field">
            <?php 			
                echo label('user-search', 'Search By User');?>
                <div class="inputs"><?php 
                    echo select_user(array(
                            'name' => 'user', 
                            'id' => 'user-search'), 
                        @$_REQUEST['user']);?>
                </div>
            </div>
	<?php endif; ?>
            <div class="field">
                <?php echo label('tag-search', 'Search By Tags'); ?>
                <div class="inputs"><?php 
                    echo text(array(
                        'name' => 'tags',
                        'size' => '40',
                        'id' => 'tag-search',
                        'class'=>'textinput'), 
                    @$_REQUEST['tags']); ?>
                </div>
            </div>
	</div>
    <?php if (has_permission('Items','showNotPublic')): ?>
    <div class="field">
        <?php echo label('public','Public/Non-Public'); ?>
        <div class="inputs">
            <?php echo select(array('name' => 'public', 'id' => 'public'), 
                array('1' => 'Only Public Items', 
                      '0' => 'Only Non-Public Items')); ?>
        </div>
    </div>

    <div class="field">
        <?php echo label('featured','Featured/Non-Featured'); ?>
        <div class="inputs">
            <?php echo select(array('name' => 'featured', 'id' => 'featured'), 
                array('1' => 'Only Featured Items', 
                      '0' => 'Only Non-Featured Items')); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php is_admin_theme() ? fire_plugin_hook('admin_append_to_advanced_search') : fire_plugin_hook('public_append_to_advanced_search'); ?>
    <div>
        <input type="submit" class="submit" name="submit_search" id="submit_search_advanced" value="Search" />
    </div>
</form>

<?php echo js('search'); ?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        Omeka.Search.activateSearchButtons();
    });
</script>
<?php if (!$isPartial): ?>
    </div> <!-- Close 'primary' div. -->
    <?php foot(); ?>
<?php endif; ?>
