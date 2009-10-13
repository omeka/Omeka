<?php    
    $itemTitle = strip_formatting(item('Dublin Core', 'Title'));
    if ($itemTitle != '' && $itemTitle != '[Untitled]') {
        $itemTitle = ': &quot;' . $itemTitle . '&quot; ';
    } else {
        $itemTitle = '';
    }
    $itemTitle = 'Item #' . item('id') . $itemTitle;
?>
<?php head(array('title' => $itemTitle, 'bodyclass'=>'items show primary-secondary')); ?>

<?php echo js('scriptaculous', 'javascripts', array('controls')); ?>

<h1 id="item-title"><?php echo $itemTitle; ?> <span class="view-public-page">[ <a href="<?php echo html_escape(public_uri('items/show/'.item('id'))); ?>">View Public Page</a> ]</span></h1>

<?php if (has_permission('Items', 'edit') or $item->wasAddedBy(current_user())): ?>
<p id="edit-item" class="edit-button"><?php 
echo link_to_item('Edit this Item', array('class'=>'edit'), 'edit'); ?></p>   
<?php endif; ?>

<ul class="item-pagination navigation">
<li id="previous-item" class="previous">
    <?php echo link_to_previous_item('Previous'); ?>
</li>
<li id="next-item" class="next">
    <?php echo link_to_next_item('Next'); ?>
</li>
</ul>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
    
    //Handles tagging of items via AJAX
    function modifyTags() {
        //Add the tags with this request
        $('tags-form').request({
            onComplete: function(t) {
                $('tag-cloud').hide();
                $('tag-cloud').update(t.responseText);
                Effect.Appear('tag-cloud', {duration: 1.0});
            }           
        });     
    }
    
    Event.observe(window, 'load', function() {
        $('tags-submit').observe('click', function(e){
            Event.stop(e);
            modifyTags();
        });
    });
    
    //End tagging functions
    
    //Image gallery functions
    function swapImage(which,where) {
      var source = which.getAttribute("href");
      where.setAttribute("src",source);
      return false;
    }

    function imageGallery() {
        if(!document.getElementById || !document.getElementsByTagName) return;
        var mainfile = $$('#main-image img')[0];
        if(!mainfile) return;
        mainfile.setAttribute('width',null);
        mainfile.setAttribute('height',null);
        $$('#files a').each(function(el){
            el.onclick = function() {
                return swapImage(this,mainfile);
            }
        });
    }

    new Event.observe(window,'load',imageGallery);
    
    //End image gallery functions
    
    // Tags autocomplete
    Event.observe(window, 'load', function(){
        new Ajax.Autocompleter("tags-field", "tag-choices", 
        <?php echo js_escape(uri(array('controller'=>'tags', 'action'=>'autocomplete'), 'default')); ?>, {
            tokens: ',',
            paramName: 'tag_start'
        });
    });

//]]>     
</script>
<div id="primary">
<?php echo flash(); ?>

<div id="item-images">
<?php echo display_files_for_item(); ?> 
</div>

<div id="core-metadata" class="showitem">
<?php echo show_item_metadata(array('show_empty_elements' => true)); ?>
</div>

<?php fire_plugin_hook('admin_append_to_items_show_primary', $item); ?>

</div>
<div id="secondary">
    
    <div class="info-panel">
        <h2>Bibliographic Citation</h2>
        <div>
        <p><?php echo item_citation();?></p>
        </div>
    </div>
    
        <div id="collection" class="info-panel">
        <h2>Collection</h2>
        <div>
           <p><?php if ( item_belongs_to_collection() ) echo item('Collection Name'); else echo 'No Collection'; ?></p>

        </div>
        </div>
    
    <div id="tags" class="info-panel">
        <h2>Tags</h2>
        <div id="tag-cloud">
            <?php common('tag-list', compact('item'), 'items'); ?>
        </div>
        
        <?php if ( has_permission('Items','tag') ): ?>
        
        <h3>My Tags</h3>
        <div id="my-tags-show">
        
        <form id="tags-form" method="post" action="<?php echo html_escape(uri('items/modify-tags/')) ?>">
            <div class="input">
            <input type="hidden" name="id" value="<?php echo item('id'); ?>" id="item-id">
            <input type="text" class="textinput" name="tags" id="tags-field" value="<?php echo tag_string(current_user_tags_for_item()); ?>" />
            <div id="tag-choices" class="autocomplete"></div>
            </div>
            <input type="submit" class="submit submit-medium" name="modify_tags" value="Save Tags" id="tags-submit" />
        </form>
        </div>
        
        <?php endif; ?>
        
    </div>
    
    <div class="info-panel">
        <h2>View File Metadata</h2>
            <div id="file-list">
                <?php if(!item_has_files()):?>
                    <p>There are no files for this item. <?php echo link_to_item('Add some', array(), 'edit'); ?>.</p>
                <?php else: ?>
                <ul>
            <?php while(loop_files_for_item()): ?>
                <li><?php echo link_to_file_metadata(array('class'=>'show', 'title'=>'View File Metadata')); ?></li>
            <?php endwhile; ?>

            </ul>
            <?php endif;?>
            </div>
    </div>

    <div class="info-panel">
        <h2>Output Formats</h2>
        <div><?php echo output_format_list(); ?></div>
    </div>
    
    <?php fire_plugin_hook('admin_append_to_items_show_secondary', $item); ?>
</div>
<?php foot();?>
