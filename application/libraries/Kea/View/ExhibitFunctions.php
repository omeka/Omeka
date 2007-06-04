<?php 
///// EXHIBIT FUNCTIONS /////

/**
 * Load either the default theme or the chosen exhibit theme, depending
 *
 * @return void
 **/
function exhibit_head()
{
	$exhibit = Zend::Registry('exhibit');
	if($exhibit->theme) {
		common('header',array(),'exhibit_themes'.DIRECTORY_SEPARATOR.$exhibit->theme);
	}else {
		head();
	}
	
}

function exhibit_foot()
{
	$exhibit = Zend::Registry('exhibit');
	if($exhibit->theme) {
		common('footer',array(),'exhibit_themes'.DIRECTORY_SEPARATOR.$exhibit->theme);
	}else {
		foot();
	}
	
}

function page_text($order)
{
	$page = Zend::Registry('page');
	$text = $page->ItemsPages[$order]->text;
	return $text;
}

function page_item($order)
{
	$page = Zend::Registry('page');
	$item = $page->ItemsPages[$order]->Item;
	return $item;
}

function page_item_id($order)
{
	$page = Zend::Registry('page');
	return $page->ItemId($order);
}

function show_items_and_pagination($url)
{		
	//Retrieve items with their pagination
	$retVal = _make_omeka_request('Items','browse',array('pagination_url'=>$url),array('items','pagination'));
	extract($retVal);
	
	foreach ($items as $item) {
		echo '<div class="item">';
		thumbnail($item); echo $item->id . ')' . $item->title;
		echo '</div>';
	}
	
	echo $pagination;
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
	
	//get rid of duplicates
	$array = array_flip(array_flip($array));
	return $array;
}

function exhibit_layout($layout)
{	
	//Load the thumbnail image
	$imgFile = WEB_EXHIBIT_LAYOUTS.DIRECTORY_SEPARATOR.$layout.DIRECTORY_SEPARATOR.'layout.jpg';
	
	echo "<img src='$imgFile' />";
	
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
	include EXHIBIT_LAYOUTS_DIR.DIRECTORY_SEPARATOR.$layout.DIRECTORY_SEPARATOR.'form.php';
}
///// END EXHIBIT FUNCTIONS /////
 
?>
