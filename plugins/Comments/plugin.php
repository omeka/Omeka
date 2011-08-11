<?php
add_plugin_hook('install', 'comments_install');
add_plugin_hook('uninstall', 'comments_uninstall');
add_plugin_hook('config', 'comments_config');
add_plugin_hook('config_form', 'comments_config_form');

add_plugin_hook('public_append_to_items_show', 'comments_public_append_to_items_show');
add_plugin_hook('public_append_to_items_browse_each', 'comments_public_append_to_items_browse_each');

function comments_install()
{
    $db = get_db();
	$db->query("
CREATE TABLE IF NOT EXISTS `{$db->prefix}comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `item_id` int(10) unsigned NOT NULL,
  `guest_name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `guest_ip` varchar(255) collate utf8_unicode_ci NOT NULL,
  `added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `description` text collate utf8_unicode_ci NOT NULL,
  `rate` int(10) unsigned NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
	);
    set_option('comments_add_comments_to_item_show_pages', '1');
}

function comments_uninstall()
{
    delete_option('comments_site_acct');
    delete_option('comments_add_comments_to_item_show_pages');
}

function comments_config()
{
    set_option('comments_site_acct', $_POST['comments_site_acct']);
    set_option('comments_add_comments_to_item_show_pages', $_POST['comments_add_comments_to_item_show_pages']);
}

function comments_config_form()
{  
    $siteAccountId = get_option('comments_site_acct');
?>
    <div class="field">
        <label for="simple_pages_filter_page_content">Add Comments To All Item Pages?</label>
        <?php echo __v()->formCheckbox('comments_add_comments_to_item_show_pages', true, 
        array('checked'=>(boolean)get_option('comments_add_comments_to_item_show_pages'))); ?>
        <p class="explanation">If checked, comments will be added to all item show pages.</p>
    </div>
<?php
}

function comments_public_append_to_items_show()
{
    $appendToItemShowPages = (boolean)get_option('comments_add_comments_to_item_show_pages');
    if ($appendToItemShowPages) {
        echo comments_display_comments();
    }
}


function comments_public_append_to_items_browse_each()
{
    $item = get_current_item();
    $appendToItemShowPages = (boolean)get_option('comments_add_comments_to_item_show_pages');
    if ($appendToItemShowPages) {
        echo display_rate($item->id);
    }
}


function display_rate($id)
{
    ob_start();
?>
<p><strong>Rating:</strong> <span style="padding-left: 10px;" id="ratingValue_<?php echo $id?>">No Rated</span></p>
<script type='text/javascript' src='/plugins/Comments/comments.js'></script>
<script>
if (window['OMEKA'] == undefined)
	OMEKA = {};
if (OMEKA.RATEITEM == undefined)
	OMEKA.RATEITEM = new Array();
OMEKA.RATEITEM[OMEKA.RATEITEM.length] = <?php echo $id?>;
window.onload = function()
{
	OMEKA.COMMENTS.getRates(OMEKA.RATEITEM);
}
</script>
<?php 
    $ht = ob_get_contents();
    ob_end_clean();
    return $ht;
}




function comments_display_comments() 
{
    ob_start();
?>
<style>
#CommentsPost div.comment_body
{
	
	padding: 5px;
	font-size: 1.2em;
	background-color: #f8f8f8;
}
#CommentsPost div.author
{
	font-size: 1.5em;
	text-align: right;
}
#CommentsPost div.main
{
	padding-bottom: 10px;
}
div.newComment
{
	font-size: 1.2em;
}
#new_comment_body
{
	height: 60px;
	width: 100%;
}
</style>
	<h3>Rating <span style="padding-left: 10px;" id="ratingValue"></span></h3>
	<br><br>
	<h3>Comments</h3>
    <div id="CommentsPost" style="display:none"></div>
    <script type='text/javascript' src='/plugins/Comments/comments.js'></script>
    <div class=newComment>
    <br><br>
    <div style="text-align: right;">
    Rating:&nbsp;&nbsp;&nbsp;<select id=comment_rating><option value=''></option><option value=5>5</option><option value=4>4</option><option value=3>3</option><option value=2>2</option><option value=1>1</option></select>
    </div>
    <textarea id=new_comment_body></textarea>
    <br/>
    <input type=button name=Save value="Add new" onClick="OMEKA.COMMENTS.save();" />
    </div>
    <script>
    window.onload = function()
    {
    	OMEKA.COMMENTS.show();
    }
    </script>
<?php
    $ht = ob_get_contents();
    ob_end_clean();
    return $ht;
}