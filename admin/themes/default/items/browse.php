<?php
$pageTitle = __('Browse Items') . ' ' . __('(%s total)', $total_records);
head(array('title'=>$pageTitle,'content_class' => 'horizontal-nav', 'bodyclass'=>'items primary browse-items')); ?>

            <?php echo flash(); ?>

            <?php display_search_filters(); ?>

            <?php if ( total_results() ): ?>
            <script type="text/javascript">
                jQuery(window).load(function() {
                    var detailsText = <?php echo js_escape(__('Details')); ?>;
                    var showDetailsText = <?php echo js_escape(__('Show Details')); ?>;
                    var hideDetailsText = <?php echo js_escape(__('Hide Details')); ?>;
                    jQuery('.item-details').hide();
                    jQuery('.action-links').prepend('<li class="details">' + detailsText + '</li>');
         
                    jQuery('tr.item').each(function() {
                        var itemDetails = jQuery(this).find('.item-details');
                        if (jQuery.trim(itemDetails.html()) != '') {
                            jQuery(this).find('.details').css({'color': '#4E7181', 'cursor': 'pointer'}).click(function() {
                                itemDetails.slideToggle('fast');
                            });
                        }
                    });
         
                    var toggleList = '<a href="#" class="toggle-all-details small blue button">' + showDetailsText + '</a>';
         
                    jQuery('.edit-items').before(toggleList);
         
                    // Toggle item details.
                    jQuery('.toggle-all-details').toggle(function(e) {
                        e.preventDefault();
                        jQuery('.toggle-all-details').text(hideDetailsText);
                        jQuery('.item-details').slideDown('fast');
                    }, function(e) {
                        e.preventDefault();
                        jQuery('.toggle-all-details').text(showDetailsText);
                        jQuery('.item-details').slideUp('fast');
                    });
         
                    var itemCheckboxes = jQuery("table#items tbody input[type=checkbox]");
                    var globalCheckbox = jQuery('th.batch-edit-heading').html('<input type="checkbox">').find('input');
                    var batchEditSubmit = jQuery('.batch-edit-option input');
                    /**
                     * Disable the batch submit button first, will be enabled once item
                     * checkboxes are checked.
                     */
                    batchEditSubmit.prop('disabled', true);
         
                    /**
                     * Check all the itemCheckboxes if the globalCheckbox is checked.
                     */
                    globalCheckbox.change(function() {
                        itemCheckboxes.prop('checked', !!this.checked);
                        checkBatchEditSubmitButton();
                    });
         
                    /**
                     * Unchecks the global checkbox if any of the itemCheckboxes are
                     * unchecked.
                     */
                    itemCheckboxes.change(function(){
                        if (!this.checked) {
                            globalCheckbox.prop('checked', false);
                        }
                        checkBatchEditSubmitButton();
                    });
         
                    /**
                     * Function to check whether the batchEditSubmit button should be
                     * enabled. If any of the itemCheckboxes is checked, the
                     * batchEditSubmit button is enabled.
                     */
                    function checkBatchEditSubmitButton() {
                        var checked = false;
                        itemCheckboxes.each(function() {
                            if (this.checked) {
                                checked = true;
                                return false;
                            }
                        });
         
                        batchEditSubmit.prop('disabled', !checked);
                    }
                });
            </script>
            <?php echo pagination_links(array('partial_file' => common('pagination_control'))); ?>
            
            <form action="<?php echo html_escape(uri('items/batch-edit')); ?>" method="post" accept-charset="utf-8">                

                <div class="item-actions batch-edit-option fourteen columns alpha">
                    <?php if (has_permission('Items', 'add')): ?>
                    <a href="<?php echo html_escape(uri('items/add')); ?>" class="add-item button small green"><?php echo __('Add an Item'); ?></a>
                    <?php endif; ?>
                    <?php echo link_to_advanced_search(__('Advanced Search'), array('id' => 'advanced-search-link', 'class' => 'small blue button')); ?>
                    <?php if (has_permission('Items', 'edit')): ?>
                    <input type="submit" class="edit-items small blue button" name="submit-batch-edit" value="<?php echo __('Edit'); ?>" />
                    <?php endif; ?>
                    <?php if (has_permission('Items', 'delete')): ?>
                    <input type="submit" class="red small" name="submit-batch-delete" value="<?php echo __('Delete'); ?>">
                    <?php endif; ?>
                    <?php echo common('quick-filters',array(),'items'); ?>
                </div>

            <table id="items" class="full" cellspacing="0" cellpadding="0">
                 <thead>
                    <tr>
                        <?php if (has_permission('Items', 'edit')): ?>
                        <th class="batch-edit-heading"><?php echo __('Select'); ?></th>
                        <?php endif; ?>
                        <?php
                        $browseHeadings[__('Title')] = 'Dublin Core,Title';
                        $browseHeadings[__('Creator')] = 'Dublin Core,Creator';
                        $browseHeadings[__('Type')] = null;
                        $browseHeadings[__('Date Added')] = 'added';
                        echo browse_headings($browseHeadings); ?>
                    </tr>
                </thead>
                <tbody>
                <?php $key = 0; ?>
                <?php while($item = loop_items()): ?>
                <tr class="item <?php if(++$key%2==1) echo 'odd'; else echo 'even'; ?>">
                    <?php $id = metadata('item', 'id'); ?>
                    <?php if (has_permission($item, 'edit') || has_permission($item, 'tag')): ?>
                    <td class="batch-edit-check" scope="row"><input type="checkbox" name="items[]" value="<?php echo $id; ?>" /></td>
        <?php endif; ?>
                    <?php if ($item->featured): ?>
                    <td class="item-info featured">
                    <?php else: ?>
                    <td class="item-info">
                    <?php endif; ?>
                        <span class="title">
                        <?php echo link_to_item(); ?>
                        <?php if(!$item->public): ?>
                        <?php echo __('(Private)'); ?>
                        <?php endif; ?>
                        </span>
                        <ul class="action-links group">
                            <?php if (has_permission($item, 'edit')): ?>
                            <li><?php echo link_to_item(__('Edit'), array(), 'edit'); ?></li>
                            <?php endif; ?>
                            <?php if (has_permission($item, 'delete')): ?>
                            <li><?php echo link_to_item(__('Delete'), array('class' => 'delete-confirm'), 'delete-confirm'); ?></li>
                            <?php endif; ?>
                        </ul>
                        <?php fire_plugin_hook('admin_append_to_items_browse_simple_each'); ?>
                        <div class="item-details">
                            <?php echo snippet_by_word_count(strip_formatting(metadata('item', array('Dublin Core', 'Description'))), 40); ?>
                            <p><strong><?php echo __('Collection'); ?>:</strong> <?php if (item_belongs_to_collection()) echo link_to_collection_for_item(); else echo __('No Collection'); ?></p><p><strong><?php echo __('Tags'); ?>:</strong> <?php if ($tags = item_tags_as_string()) echo $tags; else echo __('No Tags'); ?></p>
                            </ul>
                            <?php fire_plugin_hook('admin_append_to_items_browse_detailed_each'); ?>
                        </div>
                    </td>
                    <td><?php echo strip_formatting(metadata('item', array('Dublin Core', 'Creator'))); ?></td>
                    <td><?php echo ($typeName = metadata('item', 'Item Type Name'))
                                ? $typeName
                                : metadata('item', array('Dublin Core', 'Type'), array('snippet' => 35)); ?></td>
                    <td><?php echo format_date(metadata('item', 'Date Added')); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
            
                <div class="item-actions batch-edit-option">
                    <?php if (has_permission('Items', 'edit')): ?>
                    <input type="submit" class="edit-items small blue button" name="submit-batch-edit" value="<?php echo __('Edit'); ?>" />
                    <?php endif; ?>
                    <?php if (has_permission('Items', 'delete')): ?>
                    <input type="submit" class="red small" name="submit-batch-delete" value="<?php echo __('Delete'); ?>">
                    <?php endif; ?>
                </div>
                
                <?php echo common('quick-filters',array(),'items'); ?>
                        
            </form>
    
                <div class="pagination">
                    <div class="pagination"><?php echo pagination_links(); ?></div>
                </div>            

            <?php else: ?>
                <p><?php echo __('The query searched %s items and returned no results.', total_items()); ?> <?php echo __('Would you like to %s?', link_to_advanced_search(__('refine your search'))); ?></p>
            
            <?php endif; ?>
            
            
            
            <?php fire_plugin_hook('admin_append_to_items_browse_primary', array('items' => $items)); ?>
        
        <?php foot(); ?>
