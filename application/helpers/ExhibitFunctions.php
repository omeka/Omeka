<?php 
///// EXHIBIT FUNCTIONS /////

function section_has_pages($section) 
{
	return $section->Pages->count() > 0;
}

function link_to_exhibit($exhibit, $text=null, $props=array(), $section=null, $page = null)
{	
	$uri = exhibit_uri($exhibit, $section, $page);
	
	$text = !empty($text) ? $text : $exhibit->title;
	
	echo '<a href="'.$uri.'">' . h($text) . '</a>';
}

function exhibit_uri($exhibit, $section=null, $page=null)
{
	$exhibit_slug = ($exhibit instanceof Exhibit) ? $exhibit->slug : $exhibit;
	
	$section_slug = ($section instanceof Section) ? $section->slug : $section;
	
	$page_num = ($page instanceof SectionPage) ? $page->order : $page;
	
	$uri = 'exhibits/' . $exhibit_slug . '/' . ( !empty($section_slug) ? $section_slug . (!empty($page_num) ? '/' . $page_num : ''): '');
	
	return uri($uri);
}

function link_to_exhibit_item($item, $props=array())
{	
	$uri = exhibit_item_uri($item);
	
	echo '<a href="' . $uri . '" '. _tag_attributes($props). '>' . h($item->title) . '</a>';
}

function img_link_to_exhibit_item($item, $props=array(), $type="thumbnail")
{
	$uri = exhibit_item_uri($item);
	
	echo '<a href="' . $uri . '" '._tag_attributes($props).'>';
	switch ($type) {
		case 'thumbnail':
			thumbnail($item->Files[0]);
			break;
		case 'fullsize':
			fullsize($item->Files[0]);
		default:
			break;
	}
	
	echo '</a>';
}

function exhibit_item_uri($item, $exhibit=null, $section=null)
{
	if(!$exhibit) {
		$exhibit = Zend::Registry( 'exhibit' );
	}
	
	if(!$section) {
		$section = Zend::Registry( 'section' );
	}
	
	return uri('exhibits/' . $exhibit->slug . '/' . $section->slug . '/item/' . $item->id);
}

function exhibits($params = array()) {
	return _get_recordset($params, 'exhibits');
}

function recent_exhibits($num = 10) {
	return exhibits(array('recent'=>true,'limit'=>$num));
}

function exhibit($id=null) {
	if(!$id) {
		if(Zend::isRegistered('exhibit')) {
			return Zend::Registry( 'exhibit' );
		}
	}else {
		return Doctrine_Manager::getInstance()->getTable('Exhibit')->find($id);
	}
}

function exhibit_section($id=null) {
	if(!$id) {
		if(Zend::isRegistered('section')) {
			return Zend::Registry('section');
		}
	}else {
		return Doctrine_Manager::getInstance()->getTable('Section')->find($id);
	}
}

/**
 * Load either the default theme or the chosen exhibit theme, depending
 *
 * @return void
 **/
function exhibit_head()
{
	$exhibit = Zend::Registry('exhibit');
	if($exhibit->theme) {
		common('header',compact('exhibit'),'exhibit_themes'.DIRECTORY_SEPARATOR.$exhibit->theme);
	}else {
		head(compact('exhibit'));
	}
	
}

function exhibit_foot()
{
	$exhibit = Zend::Registry('exhibit');
	if($exhibit->theme) {
		common('footer',compact('exhibit'),'exhibit_themes'.DIRECTORY_SEPARATOR.$exhibit->theme);
	}else {
		foot(compact('exhibit'));
	}
	
}

function page_text($order, $addTag=true)
{
	$page = Zend::Registry('page');
	$text = $page->ItemsPages[$order]->text;
	if($addTag) {
		return nls2p($text);
	}
	return $text;
}

function page_item($order)
{
	$page = Zend::Registry('page');
	$item = $page->ItemsPages[$order]->Item;
	if(!$item->exists()) {
		return null;
	}
	return $item;
}

function page_item_id($order)
{
	$page = Zend::Registry('page');
	return $page->ItemId($order);
}

function layout_form_item($order, $label='Enter an Item ID #') {	
	echo '<div class="item-drop">';	
	$item = page_item($order);
	if($item and $item->exists()) {
		echo '<div class="item-drag"><div class="item_id">' . $item->id . '</div>';
			if(has_thumbnail($item)){
				thumbnail($item);
			} else {
				echo h($item->title);
			}
		echo '</div>';		
	}
	text(array('name'=>'Item['.$order.']', 'size'=>2), $item->id, $label);
	echo '</div>';
}

function layout_form_text($order, $label='Text') {
	echo '<div class="textfield">';
	textarea(array('name'=>'Text['.$order.']','rows'=>'10','cols'=>'40','class'=>'textinput'), page_text($order, false), $label); 
	echo '</div>';
}

/**
 * Get a list of the available exhibit themes
 *
 * @return array
 **/
function get_ex_themes() 
{	
	$path = EXHIBIT_THEMES_DIR;
	$iter = new VersionedDirectoryIterator($path);
	$array = $iter->getValid();
	return array_combine($array,$array);
}

function get_ex_layouts()
{
	$path = EXHIBIT_LAYOUTS_DIR;
	$it = new VersionedDirectoryIterator($path,false);
	$array = $it->getValid();
	
	//strip off file extensions
	foreach ($array as $k=>$file) {
		$array[$k] = array_shift(explode('.',$file));
	}
	
	natsort($array);
	
	//get rid of duplicates
	$array = array_flip(array_flip($array));
	return $array;
}

function exhibit_layout($layout, $input=true)
{	
	//Load the thumbnail image
	$imgFile = WEB_EXHIBIT_LAYOUTS.DIRECTORY_SEPARATOR.$layout.DIRECTORY_SEPARATOR.'layout.gif';
	echo '<div class="layout">';
	echo '<img src="'.$imgFile.'" />';
	if($input) {
		echo '<div class="input">';
		echo '<input type="radio" name="layout" value="'.$layout .'" />';
		echo '</div>';
	}
	echo '<div class="layout-name">'.$layout.'</div>'; 
	echo '</div>';
	//Load the name/description/author from the header of the file
	$file = EXHIBIT_LAYOUTS_DIR.DIRECTORY_SEPARATOR.$layout.'.php';
}

function exhibit_css($file)
{
	if(Zend::isRegistered('exhibit')) {
		$ex = Zend::Registry('exhibit');
		$path = $ex->theme.DIRECTORY_SEPARATOR.$file.'.css';
		
		if(file_exists(EXHIBIT_THEMES_DIR.DIRECTORY_SEPARATOR.$path)) {
			echo WEB_EXHIBIT_THEMES.DIRECTORY_SEPARATOR.$path;
		}
	}
	
}

function layout_css($file='layout')
{
	if(Zend::isRegistered('page')) {
		$p = Zend::Registry('page');
		$path = $p->layout.DIRECTORY_SEPARATOR.$file.'.css';
		
		if(file_exists(EXHIBIT_LAYOUTS_DIR.DIRECTORY_SEPARATOR.$path)) {
			echo WEB_EXHIBIT_LAYOUTS.DIRECTORY_SEPARATOR.$path;
		}
	}
}

function section_nav()
{
	$exhibit = Zend::registry('exhibit');
	
	//Use class="section-nav"
	echo '<ul class="exhibit-section-nav">';
	
	foreach ($exhibit->Sections as $key => $section) {		
	
		$uri = exhibit_uri($exhibit, $section);
		
		echo '<li><a href="' . $uri . '"' . (is_current($uri) ? ' class="current"' : ''). '>' . $section->title . '</a></li>';
	
	}
	
	echo '</ul>';
}

function page_nav()
{
	if(!Zend::isRegistered('section')) {
		return false;
	}
	
	$section = Zend::registry('section');
		
	echo '<ul class="exhibit-page-nav">';
	
	$key = 1;
	$section->loadPages();
	foreach ($section->Pages as $key => $page) {
	
		$uri = exhibit_uri($section->Exhibit, $section, $page);
		
		//Create the link (also check if uri matches current uri)
		echo '<li'. (is_current($uri) ? ' class="current"' : '').'><a href="'. $uri . '">Page '. $key .'</a></li>';
	
	}
	
	echo '</ul>';
}

function render_exhibit_page()
{
	$exhibit = Zend::Registry('exhibit');
	
	try {
		$section = Zend::Registry('section');
		$page = Zend::Registry('page');
		include EXHIBIT_LAYOUTS_DIR.DIRECTORY_SEPARATOR.$page->layout.DIRECTORY_SEPARATOR.'layout.php';
	} catch (Exception $e) {}
	
}

function render_layout_form($layout)
{
/*
		echo '<style>';
	include EXHIBIT_LAYOUTS_DIR.DIRECTORY_SEPARATOR.$layout.DIRECTORY_SEPARATOR.'layout.css';
	echo '</style>';
*/	
	
	include EXHIBIT_LAYOUTS_DIR.DIRECTORY_SEPARATOR.$layout.DIRECTORY_SEPARATOR.'form.php';
}
///// END EXHIBIT FUNCTIONS /////
 
?>
