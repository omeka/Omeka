<?php 
/**
* Foobar Test Plugin
*/
class Foobar extends Kea_Plugin
{
	public function definition()
	{
		$this->hasMetafield('Foo');
		$this->hasMetafield('Bar');
		
		$this->hasConfig('Default Foo Value', 'This is the default value for the metafield Foo', 40);
		$this->hasScriptPath('scripts');
	}
	
	public function customInstall()
	{
		$conn = $this->getDbConn();
		$sql = "CREATE TABLE `foo` (
				`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`foo` TEXT NOT NULL
				) ENGINE = MYISAM;";
		
		$conn->execute($sql);
	}
	
	public function onShowItem($item) { 
		Zend::dump( 'showing item with ID = '.$item->id ); 
	}
	public function onBrowseItems($items) {
//		Zend::dump( get_class($items) );
	}
	public function onAddItem($item) {
		Zend::dump( 'you added an item!  It has an ID = '. $item->id );exit;
	}
	public function onMakeFavoriteItem($item, $user) {
		Zend::dump( 'the user with ID = '.$user->id.' has favorited item with ID =' .$item->id );exit;
	}
	public function onUntagItem($item, $tags, $user_id) {
		Zend::dump( $tags );
	}
	public function onTagItem($item, $tags, $user_id) {
		Zend::dump( $tags );
	}
	public function onEditItem($item) {
//		Zend::dump( 'edited item with ID = '.$item->id );exit;
	}
	public function onDeleteItem($item) {
//		Zend::dump( 'deleting item with ID = '.$item->id );exit;
	}
	public function onMakePublicItem($item) {
		Zend::dump( $item->id );exit;
	}
	
	/////END ITEM HOOKS
	
	public function onAddCollection($coll) {
		Zend::dump( 'added a collection with ID = '.$coll->id );exit;
	}
	public function onEditCollection($coll) {
		Zend::dump( 'edited a collection with ID = '.$coll->id );exit;
	}
	public function onBrowseCollections($colls) {
//		Zend::dump( 'browsing '.count($colls).' collections' );
	}
	public function onShowCollection($coll) {
		Zend::dump( 'showing collection with ID = '.$coll->id );
	}
	public function onDeleteCollection($coll) {
		Zend::dump( 'deleting a collection with ID = '.$coll->id );exit;
	}	
	
	/////END COLLECTION HOOKS
	
	public function onBrowseExhibits($exhibits) {
//		Zend::dump( 'browsing '.count($exhibits).' exhibits' );
	}
	
	public function onAddExhibit($ex) {
//		Zend::dump( 'added an exhibit with ID = '.$ex->id );exit;
	}

	public function onEditExhibit($ex) {
//		Zend::dump( 'edited an exhibit with ID = '.$ex->id );
	}
	
	public function onDeleteExhibit($ex) {
//		Zend::dump( 'about to delete an exhibit with ID = '.$ex->id );exit;
	}
	public function onTagExhibit($ex, $tags, $user_id) {
//		Zend::dump( 'tagged an exhibit with '.print_r($tags, true).' by '.$user_id );exit;
	}
	public function onUntagExhibit($ex, $tags) {
//		Zend::dump( 'removed these tags'.print_r($tags, true));exit;
	}
	public function onShowExhibit($ex, $section, $page) {
//		Zend::dump( 'Showing Exhibit ID = '.$ex->id . ' Section ID = '.$section->id.' Page ID = '.$page->id );
	}
	public function onAddExhibitPage($page) {
//		Zend::dump( 'added a page to an exhibit (Page ID = '.$page->id.')' );exit;
	}
	public function onAddExhibitSection($section) {
//		Zend::dump( 'added a section to an exhibit (new section has ID = '.$section->id.')' );exit;
	}
	public function onEditExhibitPage($page) {
//		Zend::dump( 'edited an exhibit page with ID = '.$page->id );exit;
	}
	public function onEditExhibitSection($section) {
//		Zend::dump( 'edited an exhibit Section with ID = '.$section->id );
	}
	public function onDeleteExhibitPage($page) {
//		Zend::dump( 'about to delete an exhibit page with ID = '.$page->id );exit;
	}
	public function onDeleteExhibitSection($section) {
//		Zend::dump( 'about to delete an exhibit section with ID = '.$section->id );exit;
	}
	public function onShowExhibitItem($item, $exhibit) {
//		Zend::dump( 'showing item with ID = '.$item->id .' within exhibit with ID = '.$exhibit->id );exit;
	}
	
	/////END EXHIBIT HOOKS
	
	public function onAddType($type) {
		Zend::dump( 'added a type (ID = '.$type->id.')' );exit;
	}
	public function onEditType($type) {
		Zend::dump( 'edited a type (ID = '.$type->id.')' );exit;
	}
	public function onDeleteType($type) {
		Zend::dump( 'about to delete a type (ID = '.$type->id.')' );exit;
	}
	public function onShowType($type) {
		Zend::dump( 'showing a type with ID = '.$type->id );
	}
	public function onBrowseTypes($types) {
//		Zend::dump( 'Browsing '.count($types). ' types' );
	}		
	
	/////END TYPE HOOKS
	
	public function onBrowseTags($tags, $for) {
//		Zend::dump( 'browsing '.count($tags).' tags for class = '.$for );
	}
}
 
?>
