<?php 
///// EXHIBIT FUNCTIONS /////

/**
 * 
 *
 * @return string
 **/
function exhibit_thumbnail($item, $props=array('class'=>'permalink')) 
{	
	$uri = exhibit_item_uri($item);
		
	$output = '<a href="' . $uri . '">';
	
	$file = $item->Files[0];
	
	$output .= thumbnail($file);
	
	$output .= '</a>';
	
	return $output;
}

/**
 * Duplication of exhibit_thumbnail()
 *
 * @return string
 **/
function exhibit_fullsize($item, $props=array('class'=>'permalink'))
{
	$uri = exhibit_item_uri($item);
		
	$output = '<a href="' . $uri . '">';
	
	$file = $item->Files[0];
	
	$output .= fullsize($file, $props);
	
	$output .= '</a>';
	
	return $output;
}


function section_has_pages($section) 
{
	return $section->hasPages();
}

function link_to_exhibit($exhibit, $text=null, $props=array(), $section=null, $page = null)
{	
	$uri = exhibit_uri($exhibit, $section, $page);
	
	$text = !empty($text) ? $text : $exhibit->title;
	
	return '<a href="'.$uri.'">' . h($text) . '</a>';
}

/**
 * @internal This relates to: ExhibitsController::showAction(), ExhibitsController::summaryAction()
 *
 * @return string
 **/
function exhibit_uri($exhibit, $section=null, $page=null)
{
	$exhibit_slug = ($exhibit instanceof Exhibit) ? $exhibit->slug : $exhibit;
	
	$section_slug = ($section instanceof ExhibitSection) ? $section->slug : $section;
	
	$page_num = ($page instanceof ExhibitPage) ? $page->order : $page;
	
	//If there is no section slug available, we want to build a URL for the summary page 
	if(!$section_slug) {
	    $uri = generate_url(array('slug'=>$exhibit_slug), 'exhibitSimple');
	}else {
	    $uri = generate_url(array('slug'=>$exhibit_slug, 'section'=>$section_slug, 'page'=>$page_num), 'exhibitShow');
	}

    //If we are in the admin theme, we have to hack a solution that sends you to the public theme
    //Keep in mind, WEB_DIR can be /admin, but WEB_ROOT is always the public site
    $uri = str_replace(WEB_DIR, WEB_ROOT, $uri);

	return $uri;
}

function link_to_exhibit_item($item, $props=array())
{	
	$uri = exhibit_item_uri($item);
	
	echo '<a href="' . $uri . '" '. _tag_attributes($props). '>' . h($item->title) . '</a>';
}

function exhibit_item_uri($item, $exhibit=null, $section=null)
{
	if(!$exhibit) {
		$exhibit = Zend_Registry::get('exhibit');
	}
	
	if(!$section) {
		$section = Zend_Registry::get('section');
	}
	
	//If the exhibit has a theme associated with it
	if(!empty($exhibit->theme)) {
		return generate_url(array('slug'=>$exhibit->slug,'section'=>$section->slug,'item_id'=>$item->id), 'exhibitItem');
	}
	
	else {
		return generate_url(array('controller'=>'items','action'=>'show','id'=>$item->id), 'id');
	}
	
}

function exhibits($params = array()) {
	return _get_recordset($params, 'exhibits');
}

function recent_exhibits($num = 10) {
	return exhibits(array('recent'=>true,'limit'=>$num));
}

function exhibit($id=null) {
	if(!$id) {
		if(Zend_Registry::isRegistered('exhibit')) {
			return Zend_Registry::get('exhibit');
		}
	}else {
		return get_db()->getTable('Exhibit')->find($id);
	}
}

function exhibit_section($id=null) {
	if(!$id) {
		if(Zend_Registry::isRegistered('section')) {
			return Zend_Registry::get('section');
		}
	}else {
		return get_db()->getTable('ExhibitSection')->find($id);
	}
}

/**
 * Load either the default theme or the chosen exhibit theme, depending
 *
 * @return void
 **/
function exhibit_head()
{
	$exhibit = Zend_Registry::get('exhibit');

	if($exhibit->theme) {
		common('header',compact('exhibit'),'exhibit_themes'.DIRECTORY_SEPARATOR.$exhibit->theme);
	}else {
		head(compact('exhibit'));
	}
	
}

function exhibit_foot()
{
	$exhibit = Zend_Registry::get('exhibit');

	if($exhibit->theme) {
		common('footer',compact('exhibit'),'exhibit_themes'.DIRECTORY_SEPARATOR.$exhibit->theme);
	}else {
		foot(compact('exhibit'));
	}
	
}

function page_text($order, $addTag=true)
{
	$page = Zend_Registry::get('page');

	$text = $page->ExhibitPageEntry[$order]->text;
	if($addTag) {
		return nls2p($text);
	}
	return $text;
}

function page_item($order)
{
	$page = Zend_Registry::get('page');

	$item = $page->ExhibitPageEntry[(int) $order]->Item;
	
	if(!$item or !$item->exists()) {
		return null;
	}

	return $item;
}

function layout_form_item($order, $label='Enter an Item ID #') {	
	echo '<div class="item-drop">';	
	$item = page_item($order);
	if($item and $item->exists()) {
		echo '<div class="item-drag"><div class="item_id">' . $item->id . '</div>';
			if(has_thumbnail($item)){
				echo thumbnail($item);
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
	textarea(array('name'=>'Text['.$order.']','rows'=>'30','cols'=>'50','class'=>'textinput'), page_text($order, false), $label); 
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
	if(Zend_Registry::isRegistered('exhibit')) {
		$ex = Zend_Registry::get('exhibit');

		$path = $ex->theme.DIRECTORY_SEPARATOR.$file.'.css';
		
		if(file_exists(EXHIBIT_THEMES_DIR.DIRECTORY_SEPARATOR.$path)) {
			return WEB_EXHIBIT_THEMES.DIRECTORY_SEPARATOR.$path;
		}
	}
	
}

function layout_css($file='layout')
{
	if(Zend_Registry::isRegistered('page')) {
		$p = Zend_Registry::get('page');

		$path = $p->layout.DIRECTORY_SEPARATOR.$file.'.css';
		
		if(file_exists(EXHIBIT_LAYOUTS_DIR.DIRECTORY_SEPARATOR.$path)) {
			return WEB_EXHIBIT_LAYOUTS.DIRECTORY_SEPARATOR.$path;
		}
	}
}

function section_nav()
{
	$exhibit = Zend_Registry::get('exhibit');

	//Use class="section-nav"
	$output = '<ul class="exhibit-section-nav">';

	foreach ($exhibit->Sections as $key => $section) {		
	
		$uri = exhibit_uri($exhibit, $section);
		$output .= '<li><a href="' . $uri . '"' . (is_current($uri) ? ' class="current"' : ''). '>' . h($section->title) . '</a></li>';
	
	}
	
	$output .= '</ul>';
	return $output;
}

function page_nav()
{
	if(!Zend_Registry::isRegistered('section') or !Zend_Registry::isRegistered('page')) {
		return false;
	}
	
	$section = Zend_Registry::get('section');
	
	$currentPage = Zend_Registry::get('page');
		
	$output = '<ul class="exhibit-page-nav">';
	
	$key = 1;
    if($section) {
    	foreach ($section->Pages as $key => $page) {
	
    		$uri = exhibit_uri($section->Exhibit, $section, $page);
		
    		//Create the link (also check if uri matches current uri)
    		$output .= '<li'. ($page->id == $currentPage->id ? ' class="current"' : '').'><a href="'. $uri . '">Page '. $key .'</a></li>';
	
	    }
    }
	$output .= '</ul>';
	
	return $output;
}

function render_exhibit_page()
{
	$exhibit = Zend_Registry::get('exhibit');

	try {
		$section = Zend_Registry::get('section');

		$page = Zend_Registry::get('page');

		if ($page->layout) {
			include EXHIBIT_LAYOUTS_DIR.DIRECTORY_SEPARATOR.$page->layout.DIRECTORY_SEPARATOR.'layout.php';
		} else {
			echo "this section has no pages added to it yet";
		}
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

/**
 * A set of linked thumbnails for the items on a given exhibit page.  Each 
 * thumbnail is wrapped with a div of class = "exhibit-item"
 *
 * @param int $start The range of items on the page to display as thumbnails
 * @param int $end The end of the range
 * @param array $props Properties to apply to the <img> tag for the thumbnails
 * @return string HTML output
 **/
function display_exhibit_thumbnail_gallery($start, $end, $props=array())
{
    $output = '';
    
    for ($i=(int)$start; $i <= (int)$end; $i++) { 
        if($item=page_item($i)) {    
    	    $output .= "\n" . '<div class="exhibit-item">';
    	    $output .= exhibit_thumbnail($item, $props);
            $output .= '</div>' . "\n";
        }
    }
    
    return $output;
}

///// END EXHIBIT FUNCTIONS /////
 
?>
