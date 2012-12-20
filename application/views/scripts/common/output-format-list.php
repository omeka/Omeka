<?php if ($output_formats): ?>
    <?php if ($list): ?>
        <ul id="output-format-list">
        <?php foreach ($output_formats as $output_format): ?>
            <?php $query['output'] = $output_format; ?>
            <li><a href="<?php echo html_escape(url() . '?' . http_build_query($query)); ?>"><?php echo $output_format; ?></a></li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p id="output-format-list">
        <?php foreach ($output_formats as $key => $output_format): ?><?php $query['output'] = $output_format; ?><a href="<?php echo html_escape(url() . '?' . http_build_query($query)); ?>"><?php echo $output_format; ?></a><?php echo $key == (count($output_formats) - 1) ? '' : $delimiter; ?><?php endforeach; ?>
        </p>
    <?php endif; ?>
<?php endif; ?>
