<?php
if (!empty($formActionUri)):
    $formAttributes['action'] = $formActionUri;
else:
    $formAttributes['action'] = url(array('controller' => 'items',
                                          'action' => 'browse'));
endif;
$formAttributes['method'] = 'GET';
?>

<form <?php echo tag_attributes($formAttributes); ?>>
    <div id="search-keywords" class="field">
        <?php echo $this->formLabel('keyword-search', __('Search for Keywords')); ?>
        <div class="inputs">
        <?php
            echo $this->formText(
                'search',
                @$_REQUEST['search'],
                array('id' => 'keyword-search', 'size' => '40')
            );
        ?>
        </div>
    </div>
    <div id="search-narrow-by-field-alerts" class="sr-only alerts" aria-atomic="true" aria-live="polite">
        <p><?php echo __('Number of rows in "%s":', __('Narrow by Specific Fields')); ?> <span class="count">1</span></p>
    </div>
    <div id="search-narrow-by-fields" class="field">
        <div id="search-narrow-by-fields-label" class="label"><?php echo __('Narrow by Specific Fields'); ?></div>
        <div id="search-narrow-by-fields-property" class="label sr-only" aria-hidden="true"><?php echo __('Search Field'); ?></div>
        <div id="search-narrow-by-fields-type" class="label sr-only" aria-hidden="true"><?php echo __('Search Type'); ?></div>
        <div id="search-narrow-by-fields-terms" class="label sr-only" aria-hidden="true"><?php echo __('Search Terms'); ?></div>
        <div id="search-narrow-by-fields-joiner" class="label sr-only" aria-hidden="true"><?php echo __('Search Joiner'); ?></div>
        <div id="search-narrow-by-fields-remove-field" class="label" aria-hidden="true"><?php echo __('Remove field'); ?></div>
        <div class="inputs">
        <?php
        // If the form has been submitted, retain the number of search
        // fields used and rebuild the form
        if (!empty($_GET['advanced'])) {
            $search = $_GET['advanced'];
        } else {
            $search = array(array('field' => '', 'type' => '', 'value' => ''));
        }

        //Here is where we actually build the search form
        //The POST looks like =>
        // advanced[0] =>
        //[field] = 'description'
        //[type] = 'contains'
        //[terms] = 'foobar'
        //etc
        foreach ($search as $i => $rows): ?>
            <div class="search-entry" id="search-row-<?php echo $i; ?>" aria-label="<?php echo __('Row %s', $i+1); ?>">
                <div class="input advanced-search-joiner"> 
                    <span aria-hidden="true" class="visible-label"><?php echo __('Joiner'); ?></span>
                    <?php 
                    echo $this->formSelect(
                        "advanced[$i][joiner]",
                        @$rows['joiner'],
                        array(
                            'id' => null,
                            'aria-labelledby' => 'search-narrow-by-fields-label search-row-' . $i . ' search-narrow-by-fields-joiner',
                        ),
                        array(
                            'and' => __('AND'),
                            'or' => __('OR'),
                        )
                    );
                    ?>
                </div>
                <div class="input advanced-search-element"> 
                    <span aria-hidden="true" class="visible-label"><?php echo __('Field'); ?></span>
                    <?php 
                    echo $this->formSelect(
                        "advanced[$i][element_id]",
                        @$rows['element_id'],
                        array(
                            'aria-labelledby' => 'search-narrow-by-fields-label search-row-' . $i . ' search-narrow-by-fields-property',
                            'id' => null,
                        ),
                        get_table_options('Element', null, array(
                            'record_types' => array('Item', 'All'),
                            'sort' => 'orderBySet')
                        )
                    );
                    ?>
                </div>
                <div class="input advanced-search-type"> 
                    <span aria-hidden="true" class="visible-label"><?php echo __('Type'); ?></span>
                    <?php 
                    echo $this->formSelect(
                        "advanced[$i][type]",
                        @$rows['type'],
                        array(
                            'aria-labelledby' => 'search-narrow-by-fields-label search-row-' . $i . ' search-narrow-by-fields-type',
                            'id' => null,
                        ),
                        label_table_options(array(
                            'contains' => __('contains'),
                            'does not contain' => __('does not contain'),
                            'is exactly' => __('is exactly'),
                            'is empty' => __('is empty'),
                            'is not empty' => __('is not empty'),
                            'starts with' => __('starts with'),
                            'ends with' => __('ends with'))
                        )
                    );
                    ?>
                </div>
                <div class="input advanced-search-terms">
                    <span aria-hidden="true" class="visible-label"><?php echo __('Terms'); ?></span>
                    <?php 
                    echo $this->formText(
                        "advanced[$i][terms]",
                        @$rows['terms'],
                        array(
                            'size' => '20',
                            'aria-labelledby' => 'search-narrow-by-fields-label search-row-' . $i . ' search-narrow-by-fields-terms',
                            'id' => null,
                        )
                    );
                    ?>
                </div>
                <button type="button" class="remove_search" disabled="disabled" style="display: none;" aria-labelledby="search-narrow-by-fields-label search-row-<?php echo $i; ?> search-narrow-by-fields-remove-field" title="<?php echo __('Remove field'); ?>"><?php echo __('Remove field'); ?></button>
            </div>
        <?php endforeach; ?>
        </div>
        <button type="button" class="add_search"><?php echo __('Add a Field'); ?></button>
    </div>

    <div id="search-by-range" class="field">
        <?php echo $this->formLabel('range', __('Search by a range of ID#s (example: 1-4, 156, 79)')); ?>
        <div class="inputs">
        <?php
            echo $this->formText('range', @$_GET['range'],
                array('size' => '40')
            );
        ?>
        </div>
    </div>

    <div id="search-by-collection" class="field">
        <?php echo $this->formLabel('collection-search', __('Search By Collection')); ?>
        <div class="inputs">
        <?php
            echo $this->formSelect(
                'collection',
                @$_REQUEST['collection'],
                array('id' => 'collection-search'),
                get_table_options('Collection', null, array('include_no_collection' => true))
            );
        ?>
        </div>
    </div>

    <div id="search-by-type" class="field">
        <?php echo $this->formLabel('item-type-search', __('Search By Type')); ?>
        <div class="inputs">
        <?php
            echo $this->formSelect(
                'type',
                @$_REQUEST['type'],
                array('id' => 'item-type-search'),
                get_table_options('ItemType')
            );
        ?>
        </div>
    </div>

    <?php if (is_allowed('Users', 'browse')): ?>
    <div id="search-by-user" class="field">
    <?php
        echo $this->formLabel('user-search', __('Search By User'));?>
        <div class="inputs">
        <?php
            echo $this->formSelect(
                'user',
                @$_REQUEST['user'],
                array('id' => 'user-search'),
                get_table_options('User')
            );
        ?>
        </div>
    </div>
    <?php endif; ?>

    <div id="search-by-tag" class="field">
        <?php echo $this->formLabel('tag-search', __('Search By Tags')); ?>
        <div class="inputs">
        <?php
            echo $this->formText('tags', @$_REQUEST['tags'],
                array('size' => '40', 'id' => 'tag-search')
            );
        ?>
        </div>
    </div>


    <?php if (is_allowed('Items', 'showNotPublic')): ?>
    <div id="search-by-public" class="field">
        <?php echo $this->formLabel('public', __('Public/Non-Public')); ?>
        <div class="inputs">
        <?php
            echo $this->formSelect(
                'public',
                @$_REQUEST['public'],
                array(),
                label_table_options(array(
                    '1' => __('Only Public Items'),
                    '0' => __('Only Non-Public Items')
                ))
            );
        ?>
        </div>
    </div>
    <?php endif; ?>

    <div id="search-by-featured" class="field">
        <?php echo $this->formLabel('featured', __('Featured/Non-Featured')); ?>
        <div class="inputs">
        <?php
            echo $this->formSelect(
                'featured',
                @$_REQUEST['featured'],
                array(),
                label_table_options(array(
                    '1' => __('Only Featured Items'),
                    '0' => __('Only Non-Featured Items')
                ))
            );
        ?>
        </div>
    </div>

    <?php fire_plugin_hook('public_items_search', array('view' => $this)); ?>
    <div>
        <?php if (!isset($buttonText)) {
            $buttonText = __('Search for items');
        } ?>
        <input type="submit" class="submit" name="submit_search" id="submit_search_advanced" value="<?php echo $buttonText ?>">
    </div>
</form>

<?php echo js_tag('items-search'); ?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        Omeka.Search.activateSearchButtons();
    });
</script>
