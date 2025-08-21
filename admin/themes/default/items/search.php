<?php
$pageTitle = __('Search Items');
echo head(
    [
        'title' => $pageTitle,
        'bodyclass' => 'items advanced-search',
        'bodyid' => 'advanced-search-page'
    ]
);
echo $this->partial('items/search-form.php',
    [
        'formAttributes' => ['id'=>'advanced-search-form'],
        'useSidebar' => true
    ]
);
echo foot();
?>
