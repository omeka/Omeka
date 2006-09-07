<?php
// Layout: default;
$tags = $__c->tags()->getTags();
$max = $__c->tags()->getMaxCount();
?>

			<h3>Tags</h3>
			<div id="tagcloud">
			<?php
				$_html->tagCloud( $tags, $max, $_link->to( 'browse' ), 4 );

			?>
			</div>
			<p class="info"><a href="<?php echo $_link->to('whataretags'); ?>" class="popup">What are tags?</a></p>
