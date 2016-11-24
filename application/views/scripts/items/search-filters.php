
<?php if (!empty($displayArray) || !empty($advancedArray)): ?>
<?php
    $url = $this->url();
    $removableFilter = !empty($removableFilter);
?>
<div id="item-filters">
    <ul>
    <?php foreach($displayArray as $name => $query): ?>
        <?php
            $class = html_escape(strtolower(str_replace(' ', '-', $name)));
            $text = isset($query['value']) ? $query['value'] : $query;
            $li = html_escape(__($name)) . ': ' . html_escape($text);
            if ($removableFilter && isset($query['key'])) {
                $requestArrayCopy = $requestArray;
                unset($requestArrayCopy[$query['key']]);
                $params = http_build_query($requestArrayCopy);
                $href = $url . (strlen($params) ? '?'. $params : '');
                $li = '<a href="'. $href .'" class="search-filter">'. $li .'</a>';
                unset($requestArrayCopy, $params, $href);
            }
        ?>
        <li class="<?php echo $class; ?>">
            <?php echo $li; ?>
        </li>
    <?php endforeach; ?>
    <?php if(!empty($advancedArray)): ?>
        <?php foreach($advancedArray as $j => $advanced): ?>
        <?php
            $text = isset($advanced['value']) ? $advanced['value'] : $advanced;
            $li = html_escape($text);
            if ($removableFilter && isset($advanced['key'])) {
                $requestArrayCopy = $requestArray;
                unset($requestArrayCopy['advanced'][$advanced['key']]);
                $params = http_build_query($requestArrayCopy);
                $href = $url . (strlen($params) ? '?'. $params : '');
                $li = '<a href="'. $href .'" class="search-filter">'. $li .'</a>';
                unset($requestArrayCopy, $params, $href);
            }
        ?>
        <li class="advanced">
            <?php echo $li; ?>
        </li>
        <?php endforeach; ?>
    <?php endif; ?>
    </ul>
</div>
<?php endif; ?>
