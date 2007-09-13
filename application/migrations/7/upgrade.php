<?php 
//Remove orphaned sections

$findSections = 
/*
	"DELETE FROM sections WHERE id IN (
SELECT s.id FROM sections s 
WHERE s.exhibit_id NOT IN (SELECT DISTINCT e.id FROM exhibits e)
)";

*/	
"SELECT s.id FROM sections s 
WHERE s.exhibit_id NOT IN (SELECT DISTINCT e.id FROM exhibits e)";

$res = $this->query($findSections);

if(!empty($ids)) {
	$delete = "DELETE FROM sections WHERE id IN (" . join(', ', $ids ) . ")";
	$this->query($delete);
}

//Removed orphaned section pages

$findPages = "SELECT p.id FROM section_pages p WHERE p.section_id NOT IN (SELECT DISTINCT s.id FROM sections s)";

$res = $this->query($findPages);

$ids = pluck('id', $res);

if(!empty($ids)) {
	$delete = "DELETE FROM section_pages WHERE id IN (" . join(', ', $ids) . ")";
	$this->query($delete);
}

//Remove orphaned items_section_pages

$findIPs = "SELECT ip.id FROM items_section_pages ip WHERE ip.page_id NOT IN (SELECT DISTINCT p.id FROM section_pages p)";

$res = $this->query($findIPs);

$ids = pluck('id', $res);

if(!empty($ids)) {
	$delete = "DELETE FROM items_section_pages WHERE id IN (" . join(', ', $ids) . ")";
	
	$this->query($delete);
}

//Consolidate duplicate first items_section_pages entries into a single entry and remove the extra one 

//Select the IDs of all the page entries

$sql = "SELECT DISTINCT(page_id) FROM items_section_pages";

$res = $this->query($sql);

$ids = pluck('page_id', $res);

foreach ($ids as $page_id) {
	$dupSql = "SELECT * FROM items_section_pages WHERE page_id = $page_id AND entry_order = 1";
	$dups = $this->query($dupSql);
	$dupCount = count($dups);
	if($dupCount > 1) {
		$text = null;
		$item_id = null;
		
		//Loop through each duplicate and determine whether which part of each to keep
		foreach ($dups as $key => $row) {
			if(!empty($row['text'])) {
				$text = $row['text'];
			}
			if(!empty($row['item_id'])) {
				$item_id = $row['item_id'];
			}
		}
		
		//Now delete all the rows that aren't necessary anymore, and update the single remaining row with the correct values
		
		$this->query("DELETE FROM items_section_pages WHERE page_id = $page_id AND entry_order = 1 LIMIT " . ($dupCount - 1) );
				
		$update = "UPDATE items_section_pages SET text = '" . mysql_real_escape_string($text) . "' ";
		
		if(!empty($item_id)) {
			$update .= ", item_id = $item_id ";
		}	
	 	$update .= "WHERE page_id = $page_id AND entry_order = 1";
		
		$this->query($update);
	}
}
?>
