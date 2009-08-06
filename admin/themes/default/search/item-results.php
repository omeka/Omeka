<?php
$items = array();
foreach($hits as $hit) {
    $record = get_record_for_search_hit($hit);
    $className = get_class($record);
    switch($className) {
        case 'Item':
            $items[] = $record;
        break;
    }
}
__v()->items = $items;

common('browse', compact('items'), 'items');
?>