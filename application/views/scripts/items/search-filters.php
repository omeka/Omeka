
<?php if (!empty($displayArray) || !empty($advancedArray)): ?>
<?php $url = $this->url(); ?>
<div id="item-filters">
    <ul>
    <?php foreach($displayArray as $name => $query): ?>
        <?php
            $class = html_escape(strtolower(str_replace(' ', '-', $name)));
            $text = isset($query['value']) ? $query['value'] : $query;
            $href = false;
            if (isset($query['key'])) {
                $requestArrayCopy = $requestArray;
                unset($requestArrayCopy[$query['key']]);
                $params = http_build_query($requestArrayCopy);
                $href = $url . (strlen($params) ? '?'. $params : '');
            }
        ?>
        <li class="<?php echo $class; ?>">
            <?php if ($href): ?>
            <a href="<?php echo $href ?>" class="search-filter">
            <?php endif; ?>
            <?php echo html_escape(__($name)) . ': ' . html_escape($text); ?>
            <?php if ($href): ?>
            </a>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    <?php if(!empty($advancedArray)): ?>
        <?php foreach($advancedArray as $j => $advanced): ?>
        <?php
            $text = isset($advanced['value']) ? $advanced['value'] : $advanced;
            $href = false;
            if (isset($advanced['key'])) {
                $requestArrayCopy = $requestArray;
                unset($requestArrayCopy['advanced'][$advanced['key']]);
                $params = http_build_query($requestArrayCopy);
                $href = $url . (strlen($params) ? '?'. $params : '');
            }
        ?>
        <li class="advanced">
            <?php if ($href): ?>
            <a href="<?php echo $href ?>" class="search-filter">
            <?php endif; ?>
            <?php echo html_escape($text); ?>
            <?php if ($href): ?>
            </a>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    <?php endif; ?>
    </ul>
</div>
<?php endif; ?>
