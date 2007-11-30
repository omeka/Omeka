<?php 
$db = get_db();
//We don't actually use these keys so get rid of them

$indices_to_drop = array( 'title_search_idx',  'publisher_search_idx',  'language_search_idx',  'relation_search_idx',  'spatial_coverage_search_idx',  'rights_search_idx',  'description_search_idx',  'source_search_idx',  'subject_search_idx',  'creator_search_idx',  'additional_creator_search_idx',  'contributor_search_idx',  'rights_holder_search_idx',  'provenance_search_idx',  'citation_search_idx');

foreach ($indices_to_drop as $index) {
	$db->exec("ALTER TABLE `items` DROP INDEX `$index` "); 
}
?>
