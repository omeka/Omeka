<?php 
//Loop through all of the element records for the item's item type
$elements = get_current_item()->getItemTypeElements(); 
echo display_form_input_for_element($elements, get_current_item());
?>